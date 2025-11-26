<?php
// === ANALYTICS FUNCTIONS ===

function ap_track_event(string $eventType, array $eventData = [], ?int $userId = null): void
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('
        INSERT INTO analytics_events (event_type, event_data, user_id, session_id, ip_address, user_agent, created_at)
        VALUES (:event_type, :event_data, :user_id, :session_id, :ip_address, :user_agent, NOW())
    ');

    $sessionId = session_id() ?: 'unknown';
    $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

    $stmt->execute([
        ':event_type' => $eventType,
        ':event_data' => json_encode($eventData, JSON_UNESCAPED_UNICODE),
        ':user_id' => $userId,
        ':session_id' => $sessionId,
        ':ip_address' => $ipAddress,
        ':user_agent' => $userAgent,
    ]);
}

function ap_get_analytics_data(string $eventType, array $filters = []): array
{
    $pdo = ap_db();
    $sql = 'SELECT * FROM analytics_events WHERE event_type = :event_type';
    $params = [':event_type' => $eventType];

    if (!empty($filters['user_id'])) {
        $sql .= ' AND user_id = :user_id';
        $params[':user_id'] = $filters['user_id'];
    }

    if (!empty($filters['date_from'])) {
        $sql .= ' AND created_at >= :date_from';
        $params[':date_from'] = $filters['date_from'];
    }

    if (!empty($filters['date_to'])) {
        $sql .= ' AND created_at <= :date_to';
        $params[':date_to'] = $filters['date_to'];
    }

    $sql .= ' ORDER BY created_at DESC';

    if (!empty($filters['limit'])) {
        $sql .= ' LIMIT ' . (int) $filters['limit'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function ap_get_analytics_summary(array $filters = []): array
{
    $pdo = ap_db();

    $dateCondition = '';
    $params = [];

    if (!empty($filters['date_from'])) {
        $dateCondition .= ' AND created_at >= :date_from';
        $params[':date_from'] = $filters['date_from'];
    }

    if (!empty($filters['date_to'])) {
        $dateCondition .= ' AND created_at <= :date_to';
        $params[':date_to'] = $filters['date_to'];
    }

    // Default to last 30 days if no date filters
    if (empty($filters['date_from']) && empty($filters['date_to'])) {
        $dateCondition = ' AND created_at >= :thirty_days_ago';
        $params[':thirty_days_ago'] = date('Y-m-d H:i:s', strtotime('-30 days'));
    }

    // Total sessions (unique session_ids)
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT session_id) FROM analytics_events WHERE 1=1 {$dateCondition}");
    $stmt->execute($params);
    $totalSessions = (int) $stmt->fetchColumn();

    // Product views
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM analytics_events WHERE event_type = 'product_view' {$dateCondition}");
    $stmt->execute($params);
    $productViews = (int) $stmt->fetchColumn();

    // Add to cart events
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM analytics_events WHERE event_type = 'add_to_cart' {$dateCondition}");
    $stmt->execute($params);
    $addToCart = (int) $stmt->fetchColumn();

    // Orders completed
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE created_at >= :thirty_days_ago AND status IN ('processing', 'completed')");
    $stmt->execute([':thirty_days_ago' => date('Y-m-d H:i:s', strtotime('-30 days'))]);
    $ordersCompleted = (int) $stmt->fetchColumn();

    // Top products by views
    $stmt = $pdo->prepare("
        SELECT
            p.id,
            p.name,
            COUNT(ae.id) as views,
            COALESCE(order_stats.orders, 0) as orders
        FROM products p
        LEFT JOIN analytics_events ae ON ae.event_type = 'product_view'
            AND JSON_UNQUOTE(JSON_EXTRACT(ae.event_data, '$.product_id')) = p.id
            AND ae.created_at >= :thirty_days_ago
        LEFT JOIN (
            SELECT
                JSON_UNQUOTE(JSON_EXTRACT(ae.event_data, '$.product_id')) as product_id,
                COUNT(DISTINCT o.id) as orders
            FROM analytics_events ae
            JOIN orders o ON o.user_id = ae.user_id
                AND o.created_at >= ae.created_at
                AND o.created_at <= DATE_ADD(ae.created_at, INTERVAL 1 HOUR)
            WHERE ae.event_type = 'product_view'
                AND ae.created_at >= :thirty_days_ago
            GROUP BY JSON_UNQUOTE(JSON_EXTRACT(ae.event_data, '$.product_id'))
        ) order_stats ON order_stats.product_id = p.id
        WHERE p.is_active = 1
        GROUP BY p.id, p.name
        ORDER BY views DESC
        LIMIT 10
    ");
    $stmt->execute([':thirty_days_ago' => date('Y-m-d H:i:s', strtotime('-30 days'))]);
    $topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // User activities
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM analytics_events WHERE event_type = 'wishlist_add' {$dateCondition}");
    $stmt->execute($params);
    $wishlistAdds = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE created_at >= :thirty_days_ago");
    $stmt->execute([':thirty_days_ago' => date('Y-m-d H:i:s', strtotime('-30 days'))]);
    $reviewsSubmitted = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM analytics_events WHERE event_type = 'cart_abandon' {$dateCondition}");
    $stmt->execute($params);
    $abandonedCarts = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM analytics_events WHERE event_type = 'notification_read' {$dateCondition}");
    $stmt->execute($params);
    $notificationsRead = (int) $stmt->fetchColumn();

    // Traffic sources (mock data for now - would need referrer tracking)
    $trafficSources = [
        'Diretto' => rand(30, 50),
        'Google' => rand(20, 40),
        'Social' => rand(10, 25),
        'Altro' => rand(5, 15)
    ];

    // Device types (mock data for now - would need user agent parsing)
    $deviceTypes = [
        'Desktop' => rand(40, 70),
        'Mobile' => rand(20, 45),
        'Tablet' => rand(5, 15)
    ];

    return [
        'total_sessions' => $totalSessions,
        'product_views' => $productViews,
        'add_to_cart' => $addToCart,
        'orders_completed' => $ordersCompleted,
        'top_products' => $topProducts,
        'wishlist_adds' => $wishlistAdds,
        'reviews_submitted' => $reviewsSubmitted,
        'abandoned_carts' => $abandonedCarts,
        'notifications_read' => $notificationsRead,
        'traffic_sources' => $trafficSources,
        'device_types' => $deviceTypes,
    ];
}

function ap_get_conversion_funnel(): array
{
    $pdo = ap_db();

    // Get data for the last 30 days
    $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));

    // Sessions (unique session_ids)
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT session_id) FROM analytics_events WHERE created_at >= :date");
    $stmt->execute([':date' => $thirtyDaysAgo]);
    $sessions = (int) $stmt->fetchColumn();

    // Product views
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM analytics_events WHERE event_type = 'product_view' AND created_at >= :date");
    $stmt->execute([':date' => $thirtyDaysAgo]);
    $productViews = (int) $stmt->fetchColumn();

    // Add to cart events
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM analytics_events WHERE event_type = 'add_to_cart' AND created_at >= :date");
    $stmt->execute([':date' => $thirtyDaysAgo]);
    $addToCart = (int) $stmt->fetchColumn();

    // Orders completed
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE created_at >= :date AND status IN ('processing', 'completed')");
    $stmt->execute([':date' => $thirtyDaysAgo]);
    $ordersCompleted = (int) $stmt->fetchColumn();

    return [
        'sessions' => $sessions,
        'product_views' => $productViews,
        'add_to_cart' => $addToCart,
        'orders_completed' => $ordersCompleted,
    ];
}
}
