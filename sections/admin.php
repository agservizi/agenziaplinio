<?php
if (!ap_auth_is_admin()) {
    echo '<section class="section-padding"><div class="container"><div class="empty-state p-5 text-center"><h2>Accesso negato</h2><p>Questa area è riservata allo staff Agenzia Plinio.</p></div></div></section>';
    return;
}
$section = $_GET['section'] ?? 'dashboard';
$adminBasePath = $adminBasePath ?? '?page=admin';
$adminUrl = static function (array $params = [], string $hash = '') use ($adminBasePath): string {
    $url = $adminBasePath;
    if (!empty($params)) {
        $separator = str_contains($adminBasePath, '?') ? '&' : '?';
        $url .= $separator . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }
    if ($hash !== '') {
        $url .= '#' . ltrim($hash, '#');
    }
    return $url;
};
if (!isset($_GET['section']) && isset($_GET['id'])) {
    header('Location: ' . $adminUrl(['section' => 'edit_product', 'id' => (int) $_GET['id']]));
    exit;
}
$products = ap_fetch_products([]);
$statusFilter = $_GET['status'] ?? 'all';
$shippingFilter = $_GET['shipping'] ?? 'all';
$searchTerm = trim((string) ($_GET['q'] ?? ''));
$orderFilters = [];
if ($statusFilter !== 'all') {
    $orderFilters['status'] = $statusFilter;
}
if ($shippingFilter !== 'all') {
    $orderFilters['shipping_status'] = $shippingFilter;
}
if ($searchTerm !== '') {
    $orderFilters['search'] = $searchTerm;
}
$ordersPerPage = 10;
$ordersPage = max(1, (int) ($_GET['orders_page'] ?? 1));
$ordersTotal = ap_count_orders(null, $orderFilters);
$ordersPages = max(1, (int) ceil($ordersTotal / $ordersPerPage));
if ($ordersPage > $ordersPages) {
    $ordersPage = $ordersPages;
}
$orderFiltersPaginated = $orderFilters;
$orderFiltersPaginated['limit'] = $ordersPerPage;
$orderFiltersPaginated['offset'] = ($ordersPage - 1) * $ordersPerPage;
$orders = ap_fetch_orders(null, $orderFiltersPaginated);
$buildOrdersPageUrl = function (int $page) use ($searchTerm, $statusFilter, $shippingFilter, $adminUrl) {
    $query = ['orders_page' => max(1, $page)];
    if ($searchTerm !== '') {
        $query['q'] = $searchTerm;
    }
    if ($statusFilter !== 'all') {
        $query['status'] = $statusFilter;
    }
    if ($shippingFilter !== 'all') {
        $query['shipping'] = $shippingFilter;
    }
    return $adminUrl($query);
};
$insights = ap_admin_insights();
$shippingQueue = ap_fetch_orders(null, ['shipping_status' => 'preparing', 'limit' => 5]);
$paymentAlerts = ap_fetch_orders(null, ['payment_status' => 'pending', 'limit' => 5]);
$editingId = isset($_GET['product']) ? (int) $_GET['product'] : (isset($_GET['id']) ? (int) $_GET['id'] : null);
$editingProduct = $editingId ? ap_find_product($editingId) : null;
$categoryOptions = ap_product_category_options();
$customFields = $editingProduct ? ap_fetch_product_custom_fields($editingId) : [];

// Filtri e paginazione per il catalogo prodotti
$productFilters = [];
$productStatusFilter = $_GET['product_status'] ?? 'all';
$productCategoryFilter = $_GET['product_category'] ?? 'all';
$productFulfillmentFilter = $_GET['product_fulfillment'] ?? 'all';
$productSearchTerm = trim((string) ($_GET['product_q'] ?? ''));
$productSortBy = $_GET['sort'] ?? 'created_at';
$productSortOrder = $_GET['order'] ?? 'desc';

if ($productStatusFilter !== 'all') {
    $productFilters['is_active'] = $productStatusFilter === 'active';
}
if ($productCategoryFilter !== 'all') {
    $productFilters['category_key'] = $productCategoryFilter;
}
if ($productFulfillmentFilter !== 'all') {
    $productFilters['fulfillment_type'] = $productFulfillmentFilter;
}
if ($productSearchTerm !== '') {
    $productFilters['search'] = $productSearchTerm;
}

$productsPerPage = 20;
$productsPage = max(1, (int) ($_GET['products_page'] ?? 1));
$productsTotal = ap_count_products($productFilters);
$productsPages = max(1, (int) ceil($productsTotal / $productsPerPage));
if ($productsPage > $productsPages) {
    $productsPage = $productsPages;
}
$productFiltersPaginated = $productFilters;
$productFiltersPaginated['limit'] = $productsPerPage;
$productFiltersPaginated['offset'] = ($productsPage - 1) * $productsPerPage;
$productFiltersPaginated['sort_by'] = $productSortBy;
$productFiltersPaginated['sort_order'] = $productSortOrder;
$products = ap_fetch_products($productFiltersPaginated);

$buildProductsPageUrl = function (int $page) use ($productSearchTerm, $productStatusFilter, $productCategoryFilter, $productFulfillmentFilter, $productSortBy, $productSortOrder, $adminUrl) {
    $query = ['products_page' => max(1, $page)];
    if ($productSearchTerm !== '') {
        $query['product_q'] = $productSearchTerm;
    }
    if ($productStatusFilter !== 'all') {
        $query['product_status'] = $productStatusFilter;
    }
    if ($productCategoryFilter !== 'all') {
        $query['product_category'] = $productCategoryFilter;
    }
    if ($productFulfillmentFilter !== 'all') {
        $query['product_fulfillment'] = $productFulfillmentFilter;
    }
    if ($productSortBy !== 'created_at') {
        $query['sort'] = $productSortBy;
    }
    if ($productSortOrder !== 'desc') {
        $query['order'] = $productSortOrder;
    }
    return $adminUrl($query);
};
$totalRevenue = (int) ($insights['total_revenue'] ?? 0);
$recentRevenue = (int) ($insights['recent_revenue'] ?? 0);
$averageOrder = (int) ($insights['average_order'] ?? 0);
$activeProducts = count(array_filter($products, function ($product) {
    return (int) $product['is_active'] === 1;
}));
$pendingShipmentsCount = (int) ($insights['pending_shipments'] ?? 0);
$awaitingPaymentsCount = (int) ($insights['awaiting_payments'] ?? 0);
$orderStatuses = ['pending' => 'In attesa', 'processing' => 'In lavorazione', 'completed' => 'Completato', 'cancelled' => 'Annullato'];
$shippingStatuses = ['preparing' => 'In preparazione', 'in_transit' => 'In transito', 'delivered' => 'Consegnato', 'issue' => 'In verifica'];
$shippingMethods = ap_shipping_methods();
$coupons = ap_fetch_coupons();
$couponEditingId = isset($_GET['coupon']) ? (int) $_GET['coupon'] : null;
$editingCoupon = $couponEditingId ? ap_find_coupon_by_id($couponEditingId) : null;
$abandonedCarts = ap_fetch_abandoned_carts(8);
$abandonedPendingCount = 0;
foreach ($abandonedCarts as $abandonedCart) {
    if (empty($abandonedCart['notified_at'])) {
        $abandonedPendingCount++;
    }
}
$announcementsAdmin = ap_fetch_announcements(['include_inactive' => true]);
$announcementSlides = array_chunk($announcementsAdmin, 4);
$announcementEditingId = isset($_GET['announcement']) ? (int) $_GET['announcement'] : null;
$announcementEditing = $announcementEditingId ? ap_find_announcement($announcementEditingId) : null;
$faqsAdmin = ap_fetch_faqs();
$faqSlides = array_chunk($faqsAdmin, 3);
$faqEditingId = isset($_GET['faq']) ? (int) $_GET['faq'] : null;
$faqEditing = $faqEditingId ? ap_find_faq($faqEditingId) : null;
$newsIconOptions = [
    '⚡️' => 'Energia / Promo lampo',
    '🚀' => 'Lancio / Upgrade',
    '🎁' => 'Regalo / Bonus',
    '🛰️' => 'Servizi digitali',
    '📦' => 'Spedizioni / Logistica',
    '💳' => 'Pagamenti / Finanza',
    '🛡️' => 'Sicurezza / Compliance',
    '☎️' => 'Supporto clienti',
];
$italianProvinces = [
    'Agrigento', 'Alessandria', 'Ancona', 'Aosta', 'Arezzo', 'Ascoli Piceno', 'Asti', 'Avellino', 'Bari', 'Barletta-Andria-Trani', 'Belluno', 'Benevento', 'Bergamo', 'Biella', 'Bologna', 'Bolzano', 'Brescia', 'Brindisi', 'Cagliari', 'Caltanissetta', 'Campobasso', 'Carbonia-Iglesias', 'Caserta', 'Catania', 'Catanzaro', 'Chieti', 'Como', 'Cosenza', 'Cremona', 'Crotone', 'Cuneo', 'Enna', 'Fermo', 'Ferrara', 'Firenze', 'Foggia', 'Forlì-Cesena', 'Frosinone', 'Genova', 'Gorizia', 'Grosseto', 'Imperia', 'Isernia', 'La Spezia', 'L\'Aquila', 'Latina', 'Lecce', 'Lecco', 'Livorno', 'Lodi', 'Lucca', 'Macerata', 'Mantova', 'Massa-Carrara', 'Matera', 'Medio Campidano', 'Messina', 'Milano', 'Modena', 'Monza e della Brianza', 'Napoli', 'Novara', 'Nuoro', 'Ogliastra', 'Olbia-Tempio', 'Oristano', 'Padova', 'Palermo', 'Parma', 'Pavia', 'Perugia', 'Pesaro e Urbino', 'Pescara', 'Piacenza', 'Pisa', 'Pistoia', 'Pordenone', 'Potenza', 'Prato', 'Ragusa', 'Ravenna', 'Reggio Calabria', 'Reggio Emilia', 'Rieti', 'Rimini', 'Roma', 'Rovigo', 'Salerno', 'Sassari', 'Savona', 'Siena', 'Siracusa', 'Sondrio', 'Taranto', 'Teramo', 'Terni', 'Torino', 'Trapani', 'Trento', 'Treviso', 'Trieste', 'Udine', 'Varese', 'Venezia', 'Verbano-Cusio-Ossola', 'Vercelli', 'Verona', 'Vibo Valentia', 'Vicenza', 'Viterbo'
];
$userFilters = [];
$userStatusFilter = $_GET['user_status'] ?? 'all';
$userRoleFilter = $_GET['user_role'] ?? 'all';
$userSearchTerm = trim((string) ($_GET['user_q'] ?? ''));
if ($userStatusFilter !== 'all') {
    $userFilters['is_active'] = $userStatusFilter === 'active';
}
if ($userRoleFilter !== 'all') {
    $userFilters['role'] = $userRoleFilter;
}
if ($userSearchTerm !== '') {
    $userFilters['search'] = $userSearchTerm;
}
$usersPerPage = 20;
$usersPage = max(1, (int) ($_GET['users_page'] ?? 1));
$usersTotal = ap_count_users($userFilters);
$usersPages = max(1, (int) ceil($usersTotal / $usersPerPage));
if ($usersPage > $usersPages) {
    $usersPage = $usersPages;
}
$userFiltersPaginated = $userFilters;
$userFiltersPaginated['limit'] = $usersPerPage;
$userFiltersPaginated['offset'] = ($usersPage - 1) * $usersPerPage;
$users = ap_fetch_users($userFiltersPaginated);
$buildUsersPageUrl = function (int $page) use ($userSearchTerm, $userStatusFilter, $userRoleFilter, $adminUrl) {
    $query = ['users_page' => max(1, $page)];
    if ($userSearchTerm !== '') {
        $query['user_q'] = $userSearchTerm;
    }
    if ($userStatusFilter !== 'all') {
        $query['user_status'] = $userStatusFilter;
    }
    if ($userRoleFilter !== 'all') {
        $query['user_role'] = $userRoleFilter;
    }
    return $adminUrl($query);
};
$userEditingId = isset($_GET['user']) ? ($_GET['user'] === 'new' ? 'new' : (int) $_GET['user']) : null;
$editingUser = $userEditingId && $userEditingId !== 'new' ? ap_find_user($userEditingId) : null;
$newsIconOptions = [
    '⚡️' => 'Energia / Promo lampo',
    '🚀' => 'Lancio / Upgrade',
    '🎁' => 'Regalo / Bonus',
    '🛰️' => 'Servizi digitali',
    '📦' => 'Spedizioni / Logistica',
    '💳' => 'Pagamenti / Finanza',
    '🛡️' => 'Sicurezza / Compliance',
    '☎️' => 'Supporto clienti',
];
$italianProvinces = [
    'Agrigento', 'Alessandria', 'Ancona', 'Aosta', 'Arezzo', 'Ascoli Piceno', 'Asti', 'Avellino', 'Bari', 'Barletta-Andria-Trani', 'Belluno', 'Benevento', 'Bergamo', 'Biella', 'Bologna', 'Bolzano', 'Brescia', 'Brindisi', 'Cagliari', 'Caltanissetta', 'Campobasso', 'Carbonia-Iglesias', 'Caserta', 'Catania', 'Catanzaro', 'Chieti', 'Como', 'Cosenza', 'Cremona', 'Crotone', 'Cuneo', 'Enna', 'Fermo', 'Ferrara', 'Firenze', 'Foggia', 'Forlì-Cesena', 'Frosinone', 'Genova', 'Gorizia', 'Grosseto', 'Imperia', 'Isernia', 'La Spezia', 'L\'Aquila', 'Latina', 'Lecce', 'Lecco', 'Livorno', 'Lodi', 'Lucca', 'Macerata', 'Mantova', 'Massa-Carrara', 'Matera', 'Medio Campidano', 'Messina', 'Milano', 'Modena', 'Monza e della Brianza', 'Napoli', 'Novara', 'Nuoro', 'Ogliastra', 'Olbia-Tempio', 'Oristano', 'Padova', 'Palermo', 'Parma', 'Pavia', 'Perugia', 'Pesaro e Urbino', 'Pescara', 'Piacenza', 'Pisa', 'Pistoia', 'Pordenone', 'Potenza', 'Prato', 'Ragusa', 'Ravenna', 'Reggio Calabria', 'Reggio Emilia', 'Rieti', 'Rimini', 'Roma', 'Rovigo', 'Salerno', 'Sassari', 'Savona', 'Siena', 'Siracusa', 'Sondrio', 'Taranto', 'Teramo', 'Terni', 'Torino', 'Trapani', 'Trento', 'Treviso', 'Trieste', 'Udine', 'Varese', 'Venezia', 'Verbano-Cusio-Ossola', 'Vercelli', 'Verona', 'Vibo Valentia', 'Vicenza', 'Viterbo'
];
$auditFilters = [];
$auditEventFilter = $_GET['audit_event'] ?? 'all';
$auditUserFilter = trim((string) ($_GET['audit_user'] ?? ''));
$auditSearchTerm = trim((string) ($_GET['audit_q'] ?? ''));
if ($auditEventFilter !== 'all') {
    $auditFilters['event_type'] = $auditEventFilter;
}
if ($auditUserFilter !== '') {
    $auditFilters['user'] = $auditUserFilter;
}
if ($auditSearchTerm !== '') {
    $auditFilters['search'] = $auditSearchTerm;
}
$auditLogsPerPage = 50;
$auditLogsPage = max(1, (int) ($_GET['audit_page'] ?? 1));
$auditLogsTotal = ap_count_audit_logs($auditFilters);
$auditLogsPages = max(1, (int) ceil($auditLogsTotal / $auditLogsPerPage));
if ($auditLogsPage > $auditLogsPages) {
    $auditLogsPage = $auditLogsPages;
}
$auditFiltersPaginated = $auditFilters;
$auditFiltersPaginated['limit'] = $auditLogsPerPage;
$auditFiltersPaginated['offset'] = ($auditLogsPage - 1) * $auditLogsPerPage;
$auditLogs = ap_get_audit_logs($auditFiltersPaginated);
$buildAuditLogsPageUrl = function (int $page) use ($auditSearchTerm, $auditEventFilter, $auditUserFilter, $adminUrl) {
    $query = ['audit_page' => max(1, $page)];
    if ($auditSearchTerm !== '') {
        $query['audit_q'] = $auditSearchTerm;
    }
    if ($auditEventFilter !== 'all') {
        $query['audit_event'] = $auditEventFilter;
    }
    if ($auditUserFilter !== '') {
        $query['audit_user'] = $auditUserFilter;
    }
    return $adminUrl($query);
};
$auditEventTypes = [
    'error' => ['label' => 'Errore', 'color' => 'danger'],
    'warning' => ['label' => 'Avviso', 'color' => 'warning'],
    'info' => ['label' => 'Info', 'color' => 'info'],
    'admin_action' => ['label' => 'Azione admin', 'color' => 'primary'],
    'user_action' => ['label' => 'Azione utente', 'color' => 'secondary'],
    'system' => ['label' => 'Sistema', 'color' => 'dark'],
];

// Analytics data for admin dashboard
$analyticsSummary = ap_get_analytics_summary();
$conversionFunnel = ap_get_conversion_funnel();
?>
<section class="admin-section section-padding">
    <div class="container-xxl">
        <div class="section-heading mb-5">
            <p class="eyebrow mb-2">Area admin</p>
            <h2 class="mb-1">Panoramica ecommerce</h2>
            <p class="text-muted">Gestisci catalogo prodotti, disponibilità e ordini in tempo reale.</p>
        </div>
        <?php if ($section === 'dashboard'): ?>
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card">
                    <p class="stat-label">Ordini totali</p>
                    <h3><?php echo (int) ($insights['total_orders'] ?? 0); ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <p class="stat-label">Fatturato complessivo</p>
                    <h3><?php echo ap_price_format($totalRevenue); ?></h3>
                    <small class="text-muted">Ultimi 30gg: <?php echo ap_price_format($recentRevenue); ?></small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <p class="stat-label">Ticket medio</p>
                    <h3><?php echo ap_price_format($averageOrder); ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <p class="stat-label">Prodotti attivi</p>
                    <h3><?php echo $activeProducts; ?></h3>
                </div>
            </div>
        </div>
        <div class="admin-card mb-5">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="mb-0">Spedizioni da evadere</h4>
                        <span class="badge bg-light text-dark"><?php echo $pendingShipmentsCount; ?></span>
                    </div>
                    <?php if (empty($shippingQueue)): ?>
                        <p class="text-muted small mb-0">Nessuna spedizione in sospeso.</p>
                    <?php else: ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($shippingQueue as $queueOrder): ?>
                                <li class="border rounded-3 p-3 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-semibold">#<?php echo (int) $queueOrder['id']; ?> · <?php echo htmlspecialchars($queueOrder['customer_name'], ENT_QUOTES); ?></span>
                                        <span class="small text-muted"><?php echo htmlspecialchars($queueOrder['shipping_method'] ?? 'standard', ENT_QUOTES); ?></span>
                                    </div>
                                    <div class="small text-muted">Totale <?php echo ap_price_format((int) $queueOrder['total_cents']); ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="mb-0">Pagamenti in attesa</h4>
                        <span class="badge bg-light text-dark"><?php echo $awaitingPaymentsCount; ?></span>
                    </div>
                    <?php if (empty($paymentAlerts)): ?>
                        <p class="text-muted small mb-0">Tutti i pagamenti sono allineati.</p>
                    <?php else: ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($paymentAlerts as $pendingOrder): ?>
                                <li class="border rounded-3 p-3 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-semibold">#<?php echo (int) $pendingOrder['id']; ?> · <?php echo htmlspecialchars($pendingOrder['customer_name'], ENT_QUOTES); ?></span>
                                        <span class="badge bg-warning-subtle text-warning text-uppercase"><?php echo htmlspecialchars($pendingOrder['payment_method'] ?? 'manual', ENT_QUOTES); ?></span>
                                    </div>
                                    <div class="small text-muted">Richiede follow-up · <?php echo ap_price_format((int) $pendingOrder['total_cents']); ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($section === 'annunci'): ?>
        <div class="admin-card mb-5" id="annunci">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0"><?php echo $announcementEditing ? 'Modifica news topbar' : 'Barra news in evidenza'; ?></h4>
                    <small class="text-muted">Messaggi che scorrono sopra la topbar pubblica</small>
                </div>
                <?php if ($announcementEditing): ?>
                    <a class="small" href="<?php echo htmlspecialchars($adminUrl([], 'annunci'), ENT_QUOTES); ?>">Annulla modifica</a>
                <?php endif; ?>
            </div>
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-5">
                    <form method="post" class="h-100 d-flex flex-column">
                        <input type="hidden" name="ap_action" value="admin_save_announcement">
                        <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl([], 'annunci'), ENT_QUOTES); ?>">
                        <?php if ($announcementEditing): ?>
                            <input type="hidden" name="announcement_id" value="<?php echo (int) $announcementEditing['id']; ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label" for="announcement_message">Messaggio</label>
                            <textarea class="form-control" id="announcement_message" name="message" rows="3" maxlength="280" placeholder="Es. Nuova promo SPID attiva per tutto il weekend" required><?php echo htmlspecialchars($announcementEditing['message'] ?? '', ENT_QUOTES); ?></textarea>
                            <small class="text-muted">Max 280 caratteri · usa il selettore qui sotto per inserire icone neon</small>
                        </div>
                        <div class="mb-4" data-announcement-icon-picker data-target="#announcement_message">
                            <label class="form-label">Selettore icone</label>
                            <div class="input-group">
                                <select class="form-select" aria-label="Icona da inserire" data-icon-select>
                                    <option value="">Scegli un'icona</option>
                                    <?php foreach ($newsIconOptions as $symbol => $label): ?>
                                        <option value="<?php echo htmlspecialchars($symbol, ENT_QUOTES); ?>"><?php echo htmlspecialchars($symbol . ' · ' . $label, ENT_QUOTES); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-outline-primary" type="button" data-insert-icon>Aggiungi</button>
                            </div>
                            <small class="text-muted">Il simbolo viene convertito automaticamente in &lt;span data-icon&gt;.</small>
                        </div>
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="announcement_active" name="is_active" <?php echo isset($announcementEditing['is_active']) ? ((int) $announcementEditing['is_active'] === 1 ? 'checked' : '') : 'checked'; ?>>
                            <label class="form-check-label" for="announcement_active">Visibile sul sito</label>
                        </div>
                        <button class="btn btn-primary w-100 mt-auto" type="submit"><?php echo $announcementEditing ? 'Aggiorna news' : 'Pubblica news'; ?></button>
                    </form>
                </div>
                <div class="col-lg-7">
                    <?php if (empty($announcementsAdmin)): ?>
                        <p class="text-muted mb-0">Ancora nessuna news impostata.</p>
                    <?php else: ?>
                        <div class="admin-news-slider" data-news-slider>
                            <button class="btn btn-outline-secondary btn-sm rounded-circle admin-slider-nav" type="button" aria-label="News precedente" data-news-slider-prev disabled>&lsaquo;</button>
                            <div class="admin-news-slider__viewport" data-news-slider-viewport>
                                <div class="admin-news-slider__track">
                                    <?php foreach ($announcementSlides as $slide): ?>
                                        <div class="admin-news-slide">
                                            <ul class="list-unstyled mb-0">
                                                <?php foreach ($slide as $news): ?>
                                                    <?php
                                                        $timestamp = $news['updated_at'] ?: $news['created_at'];
                                                        $publishedLabel = $timestamp ? date('d/m H:i', strtotime((string) $timestamp)) : '-';
                                                    ?>
                                                    <li class="border rounded-3 p-3 mb-2 d-flex flex-column flex-md-row justify-content-between gap-3">
                                                        <div class="flex-grow-1">
                                                            <div class="fw-semibold"><?php echo ap_format_announcement_message((string) $news['message']); ?></div>
                                                            <div class="small text-muted">Pubblicata il <?php echo $publishedLabel; ?></div>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2 text-nowrap">
                                                            <span class="badge <?php echo (int) $news['is_active'] === 1 ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'; ?>">
                                                                <?php echo (int) $news['is_active'] === 1 ? 'Visibile' : 'Nascosta'; ?>
                                                            </span>
                                                            <a class="btn btn-sm btn-outline-secondary" href="<?php echo htmlspecialchars($adminUrl(['announcement' => (int) $news['id']], 'annunci'), ENT_QUOTES); ?>">Modifica</a>
                                                            <form method="post" onsubmit="return confirm('Rimuovere definitivamente questa news?');" class="mb-0">
                                                                <input type="hidden" name="ap_action" value="admin_delete_announcement">
                                                                <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl([], 'annunci'), ENT_QUOTES); ?>">
                                                                <input type="hidden" name="announcement_id" value="<?php echo (int) $news['id']; ?>">
                                                                <button class="btn btn-sm btn-danger" type="submit">Elimina</button>
                                                            </form>
                                                        </div>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <button class="btn btn-outline-secondary btn-sm rounded-circle admin-slider-nav" type="button" aria-label="News successiva" data-news-slider-next>&rsaquo;</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($section === 'faq-bot'): ?>
        <div class="admin-card mb-5" id="faq-bot">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0"><?php echo $faqEditing ? 'Modifica risposta bot' : 'FAQ Chatbot'; ?></h4>
                    <small class="text-muted">Gestisci le risposte informative per il widget in basso a destra</small>
                </div>
                <?php if ($faqEditing): ?>
                    <a class="small" href="<?php echo htmlspecialchars($adminUrl([], 'faq-bot'), ENT_QUOTES); ?>">Annulla modifica</a>
                <?php endif; ?>
            </div>
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-5">
                    <form method="post" class="h-100 d-flex flex-column">
                        <input type="hidden" name="ap_action" value="admin_save_faq">
                        <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl([], 'faq-bot'), ENT_QUOTES); ?>">
                        <?php if ($faqEditing): ?>
                            <input type="hidden" name="faq_id" value="<?php echo (int) $faqEditing['id']; ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label" for="faq_question">Domanda</label>
                            <input class="form-control" type="text" id="faq_question" name="question" maxlength="255" required value="<?php echo htmlspecialchars($faqEditing['question'] ?? '', ENT_QUOTES); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="faq_answer">Risposta</label>
                            <textarea class="form-control" id="faq_answer" name="answer" rows="4" required><?php echo htmlspecialchars($faqEditing['answer'] ?? '', ENT_QUOTES); ?></textarea>
                            <small class="text-muted">Puoi usare più paragrafi, verranno mostrati nel bot.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="faq_category">Categoria (facoltativa)</label>
                            <input class="form-control" type="text" id="faq_category" name="category" value="<?php echo htmlspecialchars($faqEditing['category'] ?? '', ENT_QUOTES); ?>" placeholder="Es. Servizi digitali">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="faq_keywords">Parole chiave</label>
                            <input class="form-control" type="text" id="faq_keywords" name="keywords" value="<?php echo htmlspecialchars($faqEditing['keywords'] ?? '', ENT_QUOTES); ?>" placeholder="Es. SPID, firma digitale">
                            <small class="text-muted">Usate per migliorare la ricerca interna.</small>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label" for="faq_sort">Ordine</label>
                                <input class="form-control" type="number" id="faq_sort" name="sort_order" value="<?php echo htmlspecialchars((string) ($faqEditing['sort_order'] ?? 0), ENT_QUOTES); ?>" min="0">
                            </div>
                            <div class="col-sm-6 d-flex align-items-center">
                                <div class="form-check form-switch mt-3 mt-sm-0">
                                    <input class="form-check-input" type="checkbox" id="faq_active" name="is_active" <?php echo isset($faqEditing['is_active']) ? ((int) $faqEditing['is_active'] === 1 ? 'checked' : '') : 'checked'; ?>>
                                    <label class="form-check-label" for="faq_active">Visibile ai clienti</label>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary w-100 mt-auto" type="submit"><?php echo $faqEditing ? 'Aggiorna risposta' : 'Aggiungi FAQ'; ?></button>
                    </form>
                </div>
                <div class="col-lg-7">
                    <?php if (empty($faqsAdmin)): ?>
                        <p class="text-muted mb-0">Nessuna FAQ registrata. Aggiungi le prime domande per attivare il bot.</p>
                    <?php else: ?>
                        <div class="admin-news-slider admin-faq-slider" data-news-slider>
                            <button class="btn btn-outline-secondary btn-sm rounded-circle admin-slider-nav" type="button" aria-label="FAQ precedenti" data-news-slider-prev disabled>&lsaquo;</button>
                            <div class="admin-news-slider__viewport" data-news-slider-viewport>
                                <div class="admin-news-slider__track">
                                    <?php foreach ($faqSlides as $slide): ?>
                                        <div class="admin-news-slide">
                                            <ul class="list-unstyled mb-0">
                                                <?php foreach ($slide as $faq): ?>
                                                    <li class="border rounded-3 p-3 mb-3">
                                                        <article class="admin-faq-item d-flex flex-column gap-2">
                                                            <div>
                                                                <strong><?php echo htmlspecialchars($faq['question'], ENT_QUOTES); ?></strong>
                                                                <div class="small text-muted">
                                                                    <?php if (!empty($faq['category'])): ?>
                                                                        <span><?php echo htmlspecialchars($faq['category'], ENT_QUOTES); ?></span> ·
                                                                    <?php endif; ?>
                                                                    Ordine <?php echo (int) $faq['sort_order']; ?>
                                                                </div>
                                                            </div>
                                                            <p class="small text-muted mb-0"><?php echo nl2br(htmlspecialchars(mb_strimwidth($faq['answer'], 0, 220, '…'), ENT_QUOTES)); ?></p>
                                                            <div class="d-flex align-items-center gap-2 text-nowrap">
                                                                <span class="badge <?php echo (int) $faq['is_active'] === 1 ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'; ?>">
                                                                    <?php echo (int) $faq['is_active'] === 1 ? 'Attiva' : 'Nascosta'; ?>
                                                                </span>
                                                                <a class="btn btn-sm btn-outline-secondary" href="<?php echo htmlspecialchars($adminUrl(['faq' => (int) $faq['id']], 'faq-bot'), ENT_QUOTES); ?>">Modifica</a>
                                                                <form method="post" onsubmit="return confirm('Rimuovere questa FAQ dal bot?');" class="mb-0">
                                                                    <input type="hidden" name="ap_action" value="admin_delete_faq">
                                                                    <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl([], 'faq-bot'), ENT_QUOTES); ?>">
                                                                    <input type="hidden" name="faq_id" value="<?php echo (int) $faq['id']; ?>">
                                                                    <button class="btn btn-sm btn-danger" type="submit">Elimina</button>
                                                                </form>
                                                            </div>
                                                        </article>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <button class="btn btn-outline-secondary btn-sm rounded-circle admin-slider-nav" type="button" aria-label="FAQ successive" data-news-slider-next>&rsaquo;</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($section === 'dashboard'): ?>
        <div class="admin-card mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-0">Carrelli abbandonati</h4>
                    <small class="text-muted">Promemoria automatici dopo 3 ore di inattività</small>
                </div>
                <span class="badge bg-light text-dark">Da contattare <?php echo $abandonedPendingCount; ?></span>
            </div>
            <?php if (empty($abandonedCarts)): ?>
                <p class="text-muted mb-0">Nessun carrello salvato al momento.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Articoli</th>
                                <th>Valore</th>
                                <th>Ultima attività</th>
                                <th>Stato</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($abandonedCarts as $cartRow): ?>
                                <?php
                                    $itemsSnapshot = json_decode((string) ($cartRow['cart_snapshot'] ?? '[]'), true) ?: [];
                                    $itemCount = count($itemsSnapshot);
                                    $emailLabel = $cartRow['email'] ?: 'Sessione ' . substr($cartRow['session_id'], 0, 8);
                                    $lastActivity = $cartRow['last_activity'] ? date('d/m H:i', strtotime((string) $cartRow['last_activity'])) : '-';
                                    $statusPending = empty($cartRow['notified_at']);
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($emailLabel, ENT_QUOTES); ?></strong>
                                        <?php if (!empty($cartRow['user_id'])): ?>
                                            <div class="small text-muted">User #<?php echo (int) $cartRow['user_id']; ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $itemCount; ?> prodotti</td>
                                    <td>
                                        <?php echo ap_price_format((int) ($cartRow['total_cents'] ?? 0)); ?>
                                        <?php if ((int) ($cartRow['discount_cents'] ?? 0) > 0): ?>
                                            <div class="small text-success">- <?php echo ap_price_format((int) $cartRow['discount_cents']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $lastActivity; ?></td>
                                    <td>
                                        <span class="badge <?php echo $statusPending ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success'; ?>">
                                            <?php echo $statusPending ? 'In attesa' : 'Promemoria inviato'; ?>
                                        </span>
                                        <?php if (!empty($cartRow['notified_at'])): ?>
                                            <div class="small text-muted"><?php echo date('d/m H:i', strtotime((string) $cartRow['notified_at'])); ?></div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if ($section === 'catalogo'): ?>
        <div class="row g-4 align-items-stretch">
            <div class="col-12 col-lg-5">
                <div class="admin-card h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0"><?php echo $editingProduct ? 'Modifica prodotto' : 'Nuovo prodotto'; ?></h4>
                        <?php if ($editingProduct): ?>
                            <a class="small" href="<?php echo htmlspecialchars($adminUrl([], 'catalogo'), ENT_QUOTES); ?>">Annulla modifica</a>
                        <?php endif; ?>
                    </div>
                    <form method="post" class="product-form" enctype="multipart/form-data">
                        <input type="hidden" name="ap_action" value="admin_save_product">
                        <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl([], 'catalogo'), ENT_QUOTES); ?>">
                        <?php if ($editingProduct): ?>
                            <input type="hidden" name="product_id" value="<?php echo (int) $editingProduct['id']; ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label" for="prod_name">Nome</label>
                            <input class="form-control" type="text" id="prod_name" name="name" required value="<?php echo htmlspecialchars($editingProduct['name'] ?? '', ENT_QUOTES); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_price">Prezzo (€)</label>
                            <input class="form-control" type="number" step="0.01" id="prod_price" name="price" required value="<?php echo isset($editingProduct['price_cents']) ? number_format($editingProduct['price_cents'] / 100, 2, '.', '') : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_slug">Slug</label>
                            <input class="form-control" type="text" id="prod_slug" name="slug" value="<?php echo htmlspecialchars($editingProduct['slug'] ?? '', ENT_QUOTES); ?>" placeholder="sim-dati-europa-100gb">
                            <small class="text-muted">Lascia vuoto per generarlo automaticamente.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_category">Categoria shop</label>
                            <select class="form-select" id="prod_category" name="category_key">
                                <option value="">Seleziona categoria</option>
                                <?php foreach ($categoryOptions as $key => $label): ?>
                                    <option value="<?php echo htmlspecialchars($key, ENT_QUOTES); ?>" <?php echo ($editingProduct['category_key'] ?? '') === $key ? 'selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Seleziona la categoria appropriata per il prodotto.</small>
                        </div>
                        <?php $editingFulfillment = strtolower((string) ($editingProduct['fulfillment_type'] ?? 'digital')); ?>
                        <div class="mb-3">
                            <label class="form-label" for="prod_fulfillment">Tipologia evasione</label>
                            <select class="form-select" id="prod_fulfillment" name="fulfillment_type" required>
                                <option value="digital" <?php echo $editingFulfillment === 'digital' ? 'selected' : ''; ?>>Servizio digitale</option>
                                <option value="physical" <?php echo $editingFulfillment === 'physical' ? 'selected' : ''; ?>>Prodotto fisico / spedizione</option>
                            </select>
                            <small class="text-muted">Determina badge e workflow logistico, predefinito digitale.</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="prod_sku">SKU</label>
                                    <input class="form-control" type="text" id="prod_sku" name="sku" value="<?php echo htmlspecialchars($editingProduct['sku'] ?? '', ENT_QUOTES); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="prod_stock">Stock</label>
                                    <input class="form-control" type="number" id="prod_stock" name="stock" value="<?php echo htmlspecialchars((string) ($editingProduct['stock'] ?? 0), ENT_QUOTES); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_image">URL immagine</label>
                            <input class="form-control" type="text" id="prod_image" name="image_url" value="<?php echo htmlspecialchars($editingProduct['image_url'] ?? '', ENT_QUOTES); ?>" placeholder="assets/img/og-image.jpg">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_image_file">Carica immagine</label>
                            <input class="form-control" type="file" id="prod_image_file" name="image_file" accept="image/png,image/jpeg,image/webp">
                            <small class="text-muted">Seleziona un file per sostituire l'immagine attuale.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_description">Descrizione</label>
                            <textarea class="form-control" id="prod_description" name="description" rows="4"><?php echo htmlspecialchars($editingProduct['description'] ?? '', ENT_QUOTES); ?></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Campi personalizzati (per prodotti digitali)</label>
                            <small class="text-muted d-block mb-3">Definisci i campi che il cliente deve compilare per evadere l'ordine.</small>
                            <div id="custom-fields-container">
                                <?php foreach ($customFields as $field): ?>
                                    <div class="custom-field-item border rounded p-3 mb-3" data-field-id="<?php echo (int) $field['id']; ?>">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label">Nome campo</label>
                                                <input class="form-control" type="text" name="custom_fields[<?php echo (int) $field['id']; ?>][name]" value="<?php echo htmlspecialchars($field['field_name'], ENT_QUOTES); ?>" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Etichetta</label>
                                                <input class="form-control" type="text" name="custom_fields[<?php echo (int) $field['id']; ?>][label]" value="<?php echo htmlspecialchars($field['field_label'], ENT_QUOTES); ?>" required>
                                            </div>
                                            <div class="col-md-2 field-options-container">
                                                <label class="form-label">Tipo</label>
                                                <select class="form-select field-type-select" name="custom_fields[<?php echo (int) $field['id']; ?>][type]">
                                                    <option value="text" <?php echo $field['field_type'] === 'text' ? 'selected' : ''; ?>>Testo</option>
                                                    <option value="textarea" <?php echo $field['field_type'] === 'textarea' ? 'selected' : ''; ?>>Area testo</option>
                                                    <option value="select" <?php echo $field['field_type'] === 'select' ? 'selected' : ''; ?>>Selezione</option>
                                                    <option value="checkbox" <?php echo $field['field_type'] === 'checkbox' ? 'selected' : ''; ?>>Checkbox</option>
                                                    <option value="provincia" <?php echo $field['field_type'] === 'provincia' ? 'selected' : ''; ?>>Provincia</option>
                                                    <option value="comune" <?php echo $field['field_type'] === 'comune' ? 'selected' : ''; ?>>Comune</option>
                                                    <option value="cap" <?php echo $field['field_type'] === 'cap' ? 'selected' : ''; ?>>CAP</option>
                                                    <option value="citta" <?php echo $field['field_type'] === 'citta' ? 'selected' : ''; ?>>Città</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2 field-options-container">
                                                <label class="form-label">Opzioni (per select)</label>
                                                <input class="form-control" type="text" name="custom_fields[<?php echo (int) $field['id']; ?>][options]" value="<?php echo htmlspecialchars($field['field_options'] ?? '', ENT_QUOTES); ?>" placeholder="Opzione1,Opzione2">
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">Obbligatorio</label>
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" name="custom_fields[<?php echo (int) $field['id']; ?>][required]" <?php echo (int) $field['is_required'] === 1 ? 'checked' : ''; ?>>
                                                </div>
                                            </div>
                                            <div class="col-md-1 text-end">
                                                <button type="button" class="btn btn-sm btn-danger remove-field">Rimuovi</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-outline-primary" id="add-custom-field">Aggiungi campo</button>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="prod_active" name="is_active" <?php echo isset($editingProduct['is_active']) ? ((int) $editingProduct['is_active'] === 1 ? 'checked' : '') : 'checked'; ?>>
                            <label class="form-check-label" for="prod_active">Prodotto visibile nello shop</label>
                        </div>
                        <button class="btn btn-primary w-100" type="submit"><?php echo $editingProduct ? 'Aggiorna' : 'Pubblica prodotto'; ?></button>
                    </form>
                </div>
            </div>
            <div class="col-12 col-lg-7">
                <div class="admin-card h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h4 class="mb-0">Catalogo prodotti</h4>
                            <span class="badge bg-light text-dark"><?php echo $productsTotal; ?> prodotti</span>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a class="btn btn-primary btn-sm" href="<?php echo htmlspecialchars($adminUrl(['section' => 'edit_product']), ENT_QUOTES); ?>">Nuovo prodotto</a>
                        </div>
                    </div>
                    
                    <!-- Form di ricerca e filtri -->
                    <form class="row g-2 align-items-end mb-3" method="get" action="<?php echo htmlspecialchars($adminBasePath, ENT_QUOTES); ?>">
                        <input type="hidden" name="section" value="catalogo">
                        <input type="hidden" name="products_page" value="1">
                        <div class="col-12 col-lg-3">
                            <label class="form-label" for="product_q">Cerca prodotti</label>
                            <input class="form-control" type="search" id="product_q" name="product_q" value="<?php echo htmlspecialchars($productSearchTerm, ENT_QUOTES); ?>" placeholder="Nome, SKU...">
                        </div>
                        <div class="col-12 col-lg-2">
                            <label class="form-label" for="product_status">Stato</label>
                            <select class="form-select" id="product_status" name="product_status">
                                <option value="all">Tutti</option>
                                <option value="active" <?php echo $productStatusFilter === 'active' ? 'selected' : ''; ?>>Attivi</option>
                                <option value="inactive" <?php echo $productStatusFilter === 'inactive' ? 'selected' : ''; ?>>Nascosti</option>
                            </select>
                        </div>
                        <div class="col-12 col-lg-2">
                            <label class="form-label" for="product_category">Categoria</label>
                            <select class="form-select" id="product_category" name="product_category">
                                <option value="all">Tutte</option>
                                <?php foreach ($categoryOptions as $key => $label): ?>
                                    <option value="<?php echo htmlspecialchars($key, ENT_QUOTES); ?>" <?php echo $productCategoryFilter === $key ? 'selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-lg-2">
                            <label class="form-label" for="product_fulfillment">Tipo</label>
                            <select class="form-select" id="product_fulfillment" name="product_fulfillment">
                                <option value="all">Tutti</option>
                                <option value="digital" <?php echo $productFulfillmentFilter === 'digital' ? 'selected' : ''; ?>>Digitali</option>
                                <option value="physical" <?php echo $productFulfillmentFilter === 'physical' ? 'selected' : ''; ?>>Fisici</option>
                            </select>
                        </div>
                        <div class="col-12 col-lg-2">
                            <label class="form-label" for="sort">Ordina per</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="created_at" <?php echo $productSortBy === 'created_at' ? 'selected' : ''; ?>>Data creazione</option>
                                <option value="name" <?php echo $productSortBy === 'name' ? 'selected' : ''; ?>>Nome</option>
                                <option value="price_cents" <?php echo $productSortBy === 'price_cents' ? 'selected' : ''; ?>>Prezzo</option>
                                <option value="stock" <?php echo $productSortBy === 'stock' ? 'selected' : ''; ?>>Stock</option>
                                <option value="is_active" <?php echo $productSortBy === 'is_active' ? 'selected' : ''; ?>>Stato</option>
                            </select>
                        </div>
                        <div class="col-12 col-lg-1">
                            <label class="form-label" for="order">Ordine</label>
                            <select class="form-select" id="order" name="order">
                                <option value="desc" <?php echo $productSortOrder === 'desc' ? 'selected' : ''; ?>>↓</option>
                                <option value="asc" <?php echo $productSortOrder === 'asc' ? 'selected' : ''; ?>>↑</option>
                            </select>
                        </div>
                        <div class="col-12 col-lg-2 d-flex gap-2 align-items-end">
                            <button class="btn btn-outline-secondary flex-grow-1" type="submit">Filtra</button>
                            <a class="btn btn-link text-nowrap p-0" href="<?php echo htmlspecialchars($adminUrl(['section' => 'catalogo']), ENT_QUOTES); ?>">Reset</a>
                        </div>
                    </form>
                    
                    <?php if (empty($products)): ?>
                        <p class="text-muted mb-0">Ancora nessun prodotto nel catalogo.</p>
                    <?php else: ?>
                        <!-- Controlli bulk -->
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-3 gap-2">
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-sm btn-outline-secondary" type="button" id="selectAllProducts">Seleziona tutti</button>
                                <button class="btn btn-sm btn-outline-secondary" type="button" id="deselectAllProducts">Deseleziona</button>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-sm btn-success" type="button" id="bulkActivateProducts" disabled>Attiva selezionati</button>
                                <button class="btn btn-sm btn-warning" type="button" id="bulkDeactivateProducts" disabled>Disattiva selezionati</button>
                                <button class="btn btn-sm btn-danger" type="button" id="bulkDeleteProducts" disabled data-bs-toggle="modal" data-bs-target="#bulkDeleteProductsModal">Elimina selezionati</button>
                            </div>
                        </div>
                        
                        <div class="table-responsive flex-grow-1">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th width="40">
                                            <input class="form-check-input" type="checkbox" id="productMasterCheckbox">
                                        </th>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Prezzo</th>
                                        <th>Categoria</th>
                                        <th>Stato</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td>
                                                <input class="form-check-input product-checkbox" type="checkbox" value="<?php echo (int) $product['id']; ?>" data-product-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>">
                                            </td>
                                            <td>#<?php echo (int) $product['id']; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></strong>
                                                <?php if (!empty($product['sku'])): ?>
                                                    <div class="small text-muted">SKU: <?php echo htmlspecialchars($product['sku'], ENT_QUOTES); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo ap_price_format((int) $product['price_cents']); ?></td>
                                            <td><?php echo htmlspecialchars($categoryOptions[$product['category_key']] ?? ($product['category_key'] ?? '-'), ENT_QUOTES); ?></td>
                                            <td>
                                                <span class="badge <?php echo (int) $product['is_active'] === 1 ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'; ?>">
                                                    <?php echo (int) $product['is_active'] === 1 ? 'Attivo' : 'Nascosto'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-outline-primary" href="<?php echo htmlspecialchars($adminUrl(['section' => 'edit_product', 'id' => (int) $product['id']]), ENT_QUOTES); ?>">Modifica</a>
                                                <form method="post" class="d-inline ms-1">
                                                    <input type="hidden" name="ap_action" value="admin_duplicate_product">
                                                    <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl(['section' => 'catalogo']), ENT_QUOTES); ?>">
                                                    <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                                    <button class="btn btn-sm btn-outline-secondary" type="submit" title="Duplica prodotto">Duplica</button>
                                                </form>
                                                <button class="btn btn-sm btn-danger ms-2" type="button" data-bs-toggle="modal" data-bs-target="#deleteProductModal" data-product-id="<?php echo (int) $product['id']; ?>" data-product-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>">Elimina</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginazione -->
                        <?php if ($productsPages > 1): ?>
                            <nav class="mt-3">
                                <ul class="pagination pagination-sm mb-0 flex-wrap">
                                    <?php $prevPage = max(1, $productsPage - 1); ?>
                                    <li class="page-item <?php echo $productsPage === 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="<?php echo htmlspecialchars($buildProductsPageUrl($prevPage), ENT_QUOTES); ?>" aria-label="Pagina precedente">&laquo;</a>
                                    </li>
                                    <?php for ($pageNumber = 1; $pageNumber <= $productsPages; $pageNumber++): ?>
                                        <li class="page-item <?php echo $pageNumber === $productsPage ? 'active' : ''; ?>">
                                            <a class="page-link" href="<?php echo htmlspecialchars($buildProductsPageUrl($pageNumber), ENT_QUOTES); ?>"><?php echo $pageNumber; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <?php $nextPage = min($productsPages, $productsPage + 1); ?>
                                    <li class="page-item <?php echo $productsPage === $productsPages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="<?php echo htmlspecialchars($buildProductsPageUrl($nextPage), ENT_QUOTES); ?>" aria-label="Pagina successiva">&raquo;</a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
            <?php endif; ?>
        <?php if ($section === 'edit_product'): ?>
        <div class="row g-4 align-items-stretch">
            <div class="col-12">
                <div class="admin-card h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0"><?php echo $editingProduct ? 'Modifica prodotto' : 'Nuovo prodotto'; ?></h4>
                        <a class="small" href="<?php echo htmlspecialchars($adminUrl([], 'catalogo'), ENT_QUOTES); ?>">Annulla modifica</a>
                    </div>
                    <form method="post" class="product-form" enctype="multipart/form-data">
                        <input type="hidden" name="ap_action" value="admin_save_product">
                        <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl([], 'catalogo'), ENT_QUOTES); ?>">
                        <?php if ($editingProduct): ?>
                            <input type="hidden" name="product_id" value="<?php echo (int) $editingProduct['id']; ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label" for="prod_name">Nome</label>
                            <input class="form-control" type="text" id="prod_name" name="name" required value="<?php echo htmlspecialchars($editingProduct['name'] ?? '', ENT_QUOTES); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_price">Prezzo (€)</label>
                            <input class="form-control" type="number" step="0.01" id="prod_price" name="price" required value="<?php echo isset($editingProduct['price_cents']) ? number_format($editingProduct['price_cents'] / 100, 2, '.', '') : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_slug">Slug</label>
                            <input class="form-control" type="text" id="prod_slug" name="slug" value="<?php echo htmlspecialchars($editingProduct['slug'] ?? '', ENT_QUOTES); ?>" placeholder="sim-dati-europa-100gb">
                            <small class="text-muted">Lascia vuoto per generarlo automaticamente.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_category">Categoria shop</label>
                            <select class="form-select" id="prod_category" name="category_key">
                                <option value="">Seleziona categoria</option>
                                <?php foreach ($categoryOptions as $key => $label): ?>
                                    <option value="<?php echo htmlspecialchars($key, ENT_QUOTES); ?>" <?php echo ($editingProduct['category_key'] ?? '') === $key ? 'selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Seleziona la categoria appropriata per il prodotto.</small>
                        </div>
                        <?php $editingFulfillment = strtolower((string) ($editingProduct['fulfillment_type'] ?? 'digital')); ?>
                        <div class="mb-3">
                            <label class="form-label" for="prod_fulfillment">Tipologia evasione</label>
                            <select class="form-select" id="prod_fulfillment" name="fulfillment_type" required>
                                <option value="digital" <?php echo $editingFulfillment === 'digital' ? 'selected' : ''; ?>>Servizio digitale</option>
                                <option value="physical" <?php echo $editingFulfillment === 'physical' ? 'selected' : ''; ?>>Prodotto fisico / spedizione</option>
                            </select>
                            <small class="text-muted">Determina badge e workflow logistico, predefinito digitale.</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="prod_sku">SKU</label>
                                    <input class="form-control" type="text" id="prod_sku" name="sku" value="<?php echo htmlspecialchars($editingProduct['sku'] ?? '', ENT_QUOTES); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="prod_stock">Stock</label>
                                    <input class="form-control" type="number" id="prod_stock" name="stock" value="<?php echo htmlspecialchars((string) ($editingProduct['stock'] ?? 0), ENT_QUOTES); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_image">URL immagine</label>
                            <input class="form-control" type="text" id="prod_image" name="image_url" value="<?php echo htmlspecialchars($editingProduct['image_url'] ?? '', ENT_QUOTES); ?>" placeholder="assets/img/og-image.jpg">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_image_file">Carica immagine</label>
                            <input class="form-control" type="file" id="prod_image_file" name="image_file" accept="image/png,image/jpeg,image/webp">
                            <small class="text-muted">Seleziona un file per sostituire l'immagine attuale.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_description">Descrizione</label>
                            <textarea class="form-control" id="prod_description" name="description" rows="4"><?php echo htmlspecialchars($editingProduct['description'] ?? '', ENT_QUOTES); ?></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Campi personalizzati (per prodotti digitali)</label>
                            <small class="text-muted d-block mb-3">Definisci i campi che il cliente deve compilare per evadere l'ordine.</small>
                            <div id="custom-fields-container">
                                <?php foreach ($customFields as $field): ?>
                                    <div class="custom-field-item border rounded p-3 mb-3" data-field-id="<?php echo (int) $field['id']; ?>">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label">Nome campo</label>
                                                <input class="form-control" type="text" name="custom_fields[<?php echo (int) $field['id']; ?>][name]" value="<?php echo htmlspecialchars($field['field_name'], ENT_QUOTES); ?>" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Etichetta</label>
                                                <input class="form-control" type="text" name="custom_fields[<?php echo (int) $field['id']; ?>][label]" value="<?php echo htmlspecialchars($field['field_label'], ENT_QUOTES); ?>" required>
                                            </div>
                                            <div class="col-md-2 field-options-container">
                                                <label class="form-label">Tipo</label>
                                                <select class="form-select field-type-select" name="custom_fields[<?php echo (int) $field['id']; ?>][type]">
                                                    <option value="text" <?php echo $field['field_type'] === 'text' ? 'selected' : ''; ?>>Testo</option>
                                                    <option value="textarea" <?php echo $field['field_type'] === 'textarea' ? 'selected' : ''; ?>>Area testo</option>
                                                    <option value="select" <?php echo $field['field_type'] === 'select' ? 'selected' : ''; ?>>Selezione</option>
                                                    <option value="checkbox" <?php echo $field['field_type'] === 'checkbox' ? 'selected' : ''; ?>>Checkbox</option>
                                                    <option value="provincia" <?php echo $field['field_type'] === 'provincia' ? 'selected' : ''; ?>>Provincia</option>
                                                    <option value="comune" <?php echo $field['field_type'] === 'comune' ? 'selected' : ''; ?>>Comune</option>
                                                    <option value="cap" <?php echo $field['field_type'] === 'cap' ? 'selected' : ''; ?>>CAP</option>
                                                    <option value="citta" <?php echo $field['field_type'] === 'citta' ? 'selected' : ''; ?>>Città</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2 field-options-container">
                                                <label class="form-label">Opzioni (per select)</label>
                                                <input class="form-control" type="text" name="custom_fields[<?php echo (int) $field['id']; ?>][options]" value="<?php echo htmlspecialchars($field['field_options'] ?? '', ENT_QUOTES); ?>" placeholder="Opzione1,Opzione2">
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">Obbligatorio</label>
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" name="custom_fields[<?php echo (int) $field['id']; ?>][required]" <?php echo (int) $field['is_required'] === 1 ? 'checked' : ''; ?>>
                                                </div>
                                            </div>
                                            <div class="col-md-1 text-end">
                                                <button type="button" class="btn btn-sm btn-danger remove-field">Rimuovi</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-outline-primary" id="add-custom-field">Aggiungi campo</button>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="prod_active" name="is_active" <?php echo isset($editingProduct['is_active']) ? ((int) $editingProduct['is_active'] === 1 ? 'checked' : '') : 'checked'; ?>>
                            <label class="form-check-label" for="prod_active">Prodotto visibile nello shop</label>
                        </div>
                        <button class="btn btn-primary w-100" type="submit"><?php echo $editingProduct ? 'Aggiorna' : 'Pubblica prodotto'; ?></button>
                    </form>
                </div>
            </div>
        </div>
            <?php endif; ?>
        <?php if ($section === 'ordini'): ?>
        <div class="admin-card">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
                <div>
                    <h4 class="mb-0">Ordini</h4>
                    <span class="badge bg-light text-dark"><?php echo $ordersTotal; ?> risultati</span>
                </div>
                <form class="row g-2 align-items-center flex-grow-1" method="get" action="<?php echo htmlspecialchars($adminBasePath, ENT_QUOTES); ?>">
                    <input type="hidden" name="orders_page" value="1">
                    <div class="col-lg-4">
                        <input class="form-control" type="search" name="q" value="<?php echo htmlspecialchars($searchTerm, ENT_QUOTES); ?>" placeholder="Cerca ID, email o nome">
                    </div>
                    <div class="col-lg-3">
                        <select class="form-select" name="status">
                            <option value="all">Tutti gli stati</option>
                            <?php foreach ($orderStatuses as $key => $label): ?>
                                <option value="<?php echo $key; ?>" <?php echo $statusFilter === $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <select class="form-select" name="shipping">
                            <option value="all">Tutte le spedizioni</option>
                            <?php foreach ($shippingStatuses as $key => $label): ?>
                                <option value="<?php echo $key; ?>" <?php echo $shippingFilter === $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-2 d-flex gap-2 align-items-center">
                        <button class="btn btn-outline-secondary flex-grow-1" type="submit">Filtra</button>
                        <a class="btn btn-link text-nowrap p-0" href="<?php echo htmlspecialchars($adminUrl(), ENT_QUOTES); ?>">Reset</a>
                    </div>
                </form>
            </div>
            <?php if (empty($orders)): ?>
                    <p class="text-muted mb-0">Ancora nessun ordine.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Totale</th>
                                <th>Pagamento</th>
                                <th>Spedizione</th>
                                <th class="d-none d-lg-table-cell">Enterprise</th>
                                <th>Allegati digitali</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <?php
                                    $addons = ap_order_meta_array($order['addons'] ?? null);
                                    $opsChannels = ap_order_meta_array($order['ops_channels'] ?? null);
                                    $attachments = ap_order_procurement_files($order['procurement_files'] ?? null);
                                    $slaBadges = [
                                        'core' => 'Assistenza standard 24h',
                                        'priority' => 'Gestione prioritaria 12h',
                                        'mission' => 'Supporto critico 4h',
                                    ];
                                    $windowLabels = [
                                        'standard' => '08:00-18:00',
                                        'early' => '06:00-09:00',
                                        'late' => '18:00-22:00',
                                    ];
                                    $addonLabels = array_map(static function ($value) {
                                        return ucwords(str_replace('-', ' ', (string) $value));
                                    }, $addons);
                                    $opsLabels = array_map(static function ($value) {
                                        return ucfirst((string) $value);
                                    }, $opsChannels);
                                    $orderItems = ap_fetch_order_items((int) $order['id']);
                                    $digitalProducts = array_filter($orderItems, function($item) {
                                        $product = ap_find_product((int) $item['product_id']);
                                        return $product && strtolower((string) $product['fulfillment_type']) === 'digital';
                                    });
                                ?>
                                <tr>
                                    <td>#<?php echo (int) $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name'] ?? '', ENT_QUOTES); ?></td>
                                    <td>
                                        <?php echo ap_price_format((int) $order['total_cents']); ?>
                                        <?php if ((int) ($order['discount_cents'] ?? 0) > 0): ?>
                                            <?php $couponLabel = !empty($order['coupon_code']) ? 'Coupon ' . htmlspecialchars($order['coupon_code'], ENT_QUOTES) : 'Coupon'; ?>
                                            <div class="small text-success"><?php echo $couponLabel; ?> · -<?php echo ap_price_format((int) $order['discount_cents']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-dot status-<?php echo htmlspecialchars($order['payment_status'] ?? 'pending', ENT_QUOTES); ?>"><?php echo ucfirst($order['payment_status'] ?? 'pending'); ?></span>
                                        <div class="small text-muted"><?php echo strtoupper($order['payment_method'] ?? '-'); ?></div>
                                    </td>
                                    <td>
                                        <span class="status-dot status-<?php echo htmlspecialchars($order['shipping_status'] ?? 'preparing', ENT_QUOTES); ?>"><?php echo $shippingStatuses[$order['shipping_status'] ?? 'preparing'] ?? 'In preparazione'; ?></span>
                                        <?php if (!empty($order['tracking_code'])): ?>
                                            <div class="small text-muted">Tracking: <?php echo htmlspecialchars($order['tracking_code'], ENT_QUOTES); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <?php if (!empty($order['po_number'])): ?>
                                            <div class="small">PO: <?php echo htmlspecialchars($order['po_number'], ENT_QUOTES); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($order['cost_center'])): ?>
                                            <div class="small text-muted">Centro: <?php echo htmlspecialchars($order['cost_center'], ENT_QUOTES); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($order['sla_plan'])): ?>
                                            <span class="badge bg-primary-subtle text-primary me-1 mt-1 d-inline-block"><?php echo htmlspecialchars($slaBadges[$order['sla_plan']] ?? strtoupper($order['sla_plan']), ENT_QUOTES); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($order['delivery_window'])): ?>
                                            <span class="badge bg-light text-dark mt-1 d-inline-block">Slot <?php echo htmlspecialchars($windowLabels[$order['delivery_window']] ?? $order['delivery_window'], ENT_QUOTES); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($addons)): ?>
                                            <div class="small mt-2">
                                                Add-on: <?php echo htmlspecialchars(implode(', ', $addonLabels), ENT_QUOTES); ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($opsChannels)): ?>
                                            <div class="small text-muted">Ops: <?php echo htmlspecialchars(implode(', ', $opsLabels), ENT_QUOTES); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($order['contract_ref'])): ?>
                                            <div class="small">Contratto: <?php echo htmlspecialchars($order['contract_ref'], ENT_QUOTES); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($attachments)): ?>
                                            <div class="small mt-2">Documenti:
                                                <?php foreach ($attachments as $file): ?>
                                                    <a class="d-block" href="<?php echo htmlspecialchars($file['path'], ENT_QUOTES); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($file['name'] ?? basename($file['path']), ENT_QUOTES); ?></a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($digitalProducts)): ?>
                                            <?php foreach ($digitalProducts as $item): ?>
                                                <?php $product = ap_find_product((int) $item['product_id']); ?>
                                                <div class="mb-2">
                                                    <strong><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></strong>
                                                    <?php if (!empty($item['digital_file_path'])): ?>
                                                        <div class="small text-success">Allegato: <a href="?page=download&order_id=<?php echo (int) $order['id']; ?>&product_id=<?php echo (int) $item['product_id']; ?>" target="_blank">Scarica</a></div>
                                                    <?php else: ?>
                                                        <form method="post" enctype="multipart/form-data" class="d-flex gap-2">
                                                            <input type="hidden" name="ap_action" value="admin_upload_digital_file">
                                                            <input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>">
                                                            <input type="hidden" name="product_id" value="<?php echo (int) $item['product_id']; ?>">
                                                            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl(), ENT_QUOTES); ?>">
                                                            <input class="form-control form-control-sm" type="file" name="digital_file" accept=".pdf,.doc,.docx,.jpg,.png" required>
                                                            <button class="btn btn-sm btn-outline-primary" type="submit">Carica</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="post" class="row g-2 align-items-center">
                                            <input type="hidden" name="ap_action" value="admin_update_order">
                                            <input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>">
                                            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl(), ENT_QUOTES); ?>">
                                            <div class="col-md-6">
                                                <label class="visually-hidden" for="order-status-<?php echo (int) $order['id']; ?>">Stato ordine</label>
                                                <select class="form-select form-select-sm" id="order-status-<?php echo (int) $order['id']; ?>" name="status">
                                                    <?php foreach ($orderStatuses as $key => $label): ?>
                                                        <option value="<?php echo $key; ?>" <?php echo ($order['status'] === $key) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="visually-hidden" for="ship-status-<?php echo (int) $order['id']; ?>">Stato spedizione</label>
                                                <select class="form-select form-select-sm" id="ship-status-<?php echo (int) $order['id']; ?>" name="shipping_status">
                                                    <?php foreach ($shippingStatuses as $key => $label): ?>
                                                        <option value="<?php echo $key; ?>" <?php echo (($order['shipping_status'] ?? 'preparing') === $key) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="visually-hidden" for="ship-method-<?php echo (int) $order['id']; ?>">Metodo spedizione</label>
                                                <select class="form-select form-select-sm" id="ship-method-<?php echo (int) $order['id']; ?>" name="shipping_method">
                                                    <?php foreach ($shippingMethods as $key => $option): ?>
                                                        <option value="<?php echo $key; ?>" <?php echo (($order['shipping_method'] ?? 'standard') === $key) ? 'selected' : ''; ?>><?php echo $option['label']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="visually-hidden" for="tracking-<?php echo (int) $order['id']; ?>">Tracking</label>
                                                <input class="form-control form-control-sm" type="text" id="tracking-<?php echo (int) $order['id']; ?>" name="tracking_code" placeholder="Tracking" value="<?php echo htmlspecialchars($order['tracking_code'] ?? '', ENT_QUOTES); ?>">
                                            </div>
                                            <div class="col-12 text-end">
                                                <button class="btn btn-sm btn-outline-secondary" type="submit">Aggiorna</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($ordersPages > 1): ?>
                    <nav class="mt-3">
                        <ul class="pagination pagination-sm mb-0 flex-wrap">
                            <?php $prevPage = max(1, $ordersPage - 1); ?>
                            <li class="page-item <?php echo $ordersPage === 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo htmlspecialchars($buildOrdersPageUrl($prevPage), ENT_QUOTES); ?>" aria-label="Pagina precedente">&laquo;</a>
                            </li>
                            <?php for ($pageNumber = 1; $pageNumber <= $ordersPages; $pageNumber++): ?>
                                <li class="page-item <?php echo $pageNumber === $ordersPage ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo htmlspecialchars($buildOrdersPageUrl($pageNumber), ENT_QUOTES); ?>"><?php echo $pageNumber; ?></a>
                                </li>
                            <?php endfor; ?>
                            <?php $nextPage = min($ordersPages, $ordersPage + 1); ?>
                            <li class="page-item <?php echo $ordersPage === $ordersPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo htmlspecialchars($buildOrdersPageUrl($nextPage), ENT_QUOTES); ?>" aria-label="Pagina successiva">&raquo;</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if ($section === 'utenti'): ?>
        <div class="admin-card">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
                <div>
                    <h4 class="mb-0">Utenti</h4>
                    <span class="badge bg-light text-dark"><?php echo $usersTotal; ?> risultati</span>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-primary btn-sm" href="<?php echo htmlspecialchars($adminUrl(['section' => 'utenti', 'user' => 'new']), ENT_QUOTES); ?>">Nuovo utente</a>
                </div>
                <form class="row g-2 align-items-center flex-grow-1" method="get" action="<?php echo htmlspecialchars($adminBasePath, ENT_QUOTES); ?>">
                    <input type="hidden" name="section" value="utenti">
                    <input type="hidden" name="users_page" value="1">
                    <div class="col-lg-4">
                        <input class="form-control" type="search" name="user_q" value="<?php echo htmlspecialchars($userSearchTerm, ENT_QUOTES); ?>" placeholder="Cerca nome o email">
                    </div>
                    <div class="col-lg-3">
                        <select class="form-select" name="user_status">
                            <option value="all">Tutti gli stati</option>
                            <option value="active" <?php echo $userStatusFilter === 'active' ? 'selected' : ''; ?>>Attivi</option>
                            <option value="inactive" <?php echo $userStatusFilter === 'inactive' ? 'selected' : ''; ?>>Disattivati</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <select class="form-select" name="user_role">
                            <option value="all">Tutti i ruoli</option>
                            <option value="customer" <?php echo $userRoleFilter === 'customer' ? 'selected' : ''; ?>>Cliente</option>
                            <option value="admin" <?php echo $userRoleFilter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    <div class="col-lg-2 d-flex gap-2 align-items-center">
                        <button class="btn btn-outline-secondary flex-grow-1" type="submit">Filtra</button>
                        <a class="btn btn-link text-nowrap p-0" href="<?php echo htmlspecialchars($adminUrl(['section' => 'utenti']), ENT_QUOTES); ?>">Reset</a>
                    </div>
                </form>
            </div>
            <?php if (empty($users)): ?>
                <p class="text-muted mb-0">Nessun utente trovato.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Ruolo</th>
                                <th>Stato</th>
                                <th>Registrato</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>#<?php echo (int) $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?></td>
                                    <td><?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?></td>
                                    <td>
                                        <span class="badge <?php echo $user['role'] === 'admin' ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary'; ?>">
                                            <?php echo $user['role'] === 'admin' ? 'Admin' : 'Cliente'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo (int) $user['is_active'] === 1 ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'; ?>">
                                            <?php echo (int) $user['is_active'] === 1 ? 'Attivo' : 'Disattivato'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime((string) $user['created_at'])); ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-primary" href="<?php echo htmlspecialchars($adminUrl(['section' => 'utenti', 'user' => (int) $user['id']]), ENT_QUOTES); ?>">Modifica</a>
                                        <button class="btn btn-sm btn-danger ms-2" type="button" data-bs-toggle="modal" data-bs-target="#deleteUserModal" data-user-id="<?php echo (int) $user['id']; ?>" data-user-name="<?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?>" data-user-email="<?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>">Elimina</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($usersPages > 1): ?>
                    <nav class="mt-3">
                        <ul class="pagination pagination-sm mb-0 flex-wrap">
                            <?php $prevPage = max(1, $usersPage - 1); ?>
                            <li class="page-item <?php echo $usersPage === 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo htmlspecialchars($buildUsersPageUrl($prevPage), ENT_QUOTES); ?>" aria-label="Pagina precedente">&laquo;</a>
                            </li>
                            <?php for ($pageNumber = 1; $pageNumber <= $usersPages; $pageNumber++): ?>
                                <li class="page-item <?php echo $pageNumber === $usersPage ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo htmlspecialchars($buildUsersPageUrl($pageNumber), ENT_QUOTES); ?>"><?php echo $pageNumber; ?></a>
                                </li>
                            <?php endfor; ?>
                            <?php $nextPage = min($usersPages, $usersPage + 1); ?>
                            <li class="page-item <?php echo $usersPage === $usersPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo htmlspecialchars($buildUsersPageUrl($nextPage), ENT_QUOTES); ?>" aria-label="Pagina successiva">&raquo;</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if ($userEditingId && ($editingUser || $userEditingId === 'new')): ?>
        <div class="row g-4 align-items-stretch">
            <div class="col-12">
                <div class="admin-card h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0"><?php echo $editingUser ? 'Modifica utente' : 'Nuovo utente'; ?></h4>
                        <a class="small" href="<?php echo htmlspecialchars($adminUrl(['section' => 'utenti']), ENT_QUOTES); ?>">Annulla modifica</a>
                    </div>
                    <form method="post" class="user-form">
                        <input type="hidden" name="ap_action" value="admin_save_user">
                        <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl(['section' => 'utenti']), ENT_QUOTES); ?>">
                        <?php if ($editingUser): ?>
                            <input type="hidden" name="user_id" value="<?php echo (int) $editingUser['id']; ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label" for="user_name">Nome</label>
                            <input class="form-control" type="text" id="user_name" name="name" required value="<?php echo htmlspecialchars($editingUser['name'] ?? '', ENT_QUOTES); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="user_email">Email</label>
                            <input class="form-control" type="email" id="user_email" name="email" required value="<?php echo htmlspecialchars($editingUser['email'] ?? '', ENT_QUOTES); ?>">
                        </div>
                        <?php if (!$editingUser): ?>
                        <div class="mb-3">
                            <label class="form-label" for="user_password">Password</label>
                            <input class="form-control" type="password" id="user_password" name="password" required>
                        </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label" for="user_role">Ruolo</label>
                            <select class="form-select" id="user_role" name="role">
                                <option value="customer" <?php echo ($editingUser['role'] ?? 'customer') === 'customer' ? 'selected' : ''; ?>>Cliente</option>
                                <option value="admin" <?php echo ($editingUser['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="user_active" name="is_active" <?php echo isset($editingUser['is_active']) ? ((int) $editingUser['is_active'] === 1 ? 'checked' : '') : 'checked'; ?>>
                            <label class="form-check-label" for="user_active">Utente attivo</label>
                        </div>
                        <button class="btn btn-primary w-100" type="submit"><?php echo $editingUser ? 'Aggiorna' : 'Crea utente'; ?></button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($section === 'coupons'): ?>
        <div class="admin-card mt-5" id="coupons">
            <div class="row g-4 align-items-start">
                <div class="col-lg-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0"><?php echo $editingCoupon ? 'Modifica coupon' : 'Nuovo coupon'; ?></h4>
                        <?php if ($editingCoupon): ?>
                            <a class="small" href="<?php echo htmlspecialchars($adminUrl([], 'coupons'), ENT_QUOTES); ?>">Annulla modifica</a>
                        <?php endif; ?>
                    </div>
                    <?php
                        $couponAmountValue = $editingCoupon ? number_format(((int) ($editingCoupon['amount_cents'] ?? 0)) / 100, 2, '.', '') : '';
                        $couponMinValue = $editingCoupon ? number_format(((int) ($editingCoupon['min_total_cents'] ?? 0)) / 100, 2, '.', '') : '';
                        $couponStartsValue = ($editingCoupon && !empty($editingCoupon['starts_at'])) ? date('Y-m-d\TH:i', strtotime((string) $editingCoupon['starts_at'])) : '';
                        $couponEndsValue = ($editingCoupon && !empty($editingCoupon['ends_at'])) ? date('Y-m-d\TH:i', strtotime((string) $editingCoupon['ends_at'])) : '';
                        $couponCodeValue = $editingCoupon ? (string) $editingCoupon['code'] : ap_generate_unique_coupon_code();
                    ?>
                    <form method="post" class="coupon-form">
                        <input type="hidden" name="ap_action" value="admin_save_coupon">
                        <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl([], 'coupons'), ENT_QUOTES); ?>">
                        <?php if ($editingCoupon): ?>
                            <input type="hidden" name="coupon_id" value="<?php echo (int) $editingCoupon['id']; ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label" for="coupon_code_admin">Codice</label>
                            <input class="form-control" type="text" id="coupon_code_admin" name="code" required value="<?php echo htmlspecialchars($couponCodeValue, ENT_QUOTES); ?>" placeholder="PB2024">
                            <?php if (!$editingCoupon): ?>
                                <small class="text-muted">Generato automaticamente per assicurare l'univocità, modificabile se necessario.</small>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="coupon_desc_admin">Descrizione</label>
                            <input class="form-control" type="text" id="coupon_desc_admin" name="description" value="<?php echo htmlspecialchars($editingCoupon['description'] ?? '', ENT_QUOTES); ?>" placeholder="Visibile solo allo staff">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="coupon_type_admin">Tipo</label>
                                <select class="form-select" id="coupon_type_admin" name="type">
                                    <option value="fixed" <?php echo (($editingCoupon['type'] ?? 'fixed') === 'fixed') ? 'selected' : ''; ?>>Importo fisso</option>
                                    <option value="percent" <?php echo (($editingCoupon['type'] ?? '') === 'percent') ? 'selected' : ''; ?>>Percentuale</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="coupon_value_fixed">Valore fisso (€)</label>
                                <input class="form-control" type="number" step="0.01" min="0" id="coupon_value_fixed" name="amount" value="<?php echo $couponAmountValue; ?>">
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label" for="coupon_value_percent">Sconto (%)</label>
                                <input class="form-control" type="number" min="0" max="100" id="coupon_value_percent" name="percent" value="<?php echo htmlspecialchars((string) ($editingCoupon['percent'] ?? 0), ENT_QUOTES); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="coupon_min_total">Ordine minimo (€)</label>
                                <input class="form-control" type="number" step="0.01" min="0" id="coupon_min_total" name="min_total" value="<?php echo $couponMinValue; ?>">
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label" for="coupon_max_usage">Limite utilizzi</label>
                                <input class="form-control" type="number" min="0" id="coupon_max_usage" name="max_redemptions" value="<?php echo htmlspecialchars((string) ($editingCoupon['max_redemptions'] ?? ''), ENT_QUOTES); ?>" placeholder="Illimitato">
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="coupon_active" name="is_active" <?php echo isset($editingCoupon['is_active']) ? ((int) $editingCoupon['is_active'] === 1 ? 'checked' : '') : 'checked'; ?>>
                                    <label class="form-check-label" for="coupon_active">Coupon attivo</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label" for="coupon_start">Valido dal</label>
                                <input class="form-control" type="datetime-local" id="coupon_start" name="starts_at" value="<?php echo $couponStartsValue; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="coupon_end">Valido fino al</label>
                                <input class="form-control" type="datetime-local" id="coupon_end" name="ends_at" value="<?php echo $couponEndsValue; ?>">
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">Compila solo il campo relativo al tipo selezionato.</small>
                        <button class="btn btn-primary w-100 mt-3" type="submit"><?php echo $editingCoupon ? 'Aggiorna coupon' : 'Crea coupon'; ?></button>
                    </form>
                </div>
                <div class="col-lg-7">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Archivio coupon</h4>
                        <span class="badge bg-light text-dark">Totale <?php echo count($coupons); ?></span>
                    </div>
                    <?php if (empty($coupons)): ?>
                        <p class="text-muted mb-0">Ancora nessun codice promo. Creane uno per attivare campagne.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Codice</th>
                                        <th>Tipo</th>
                                        <th>Valore</th>
                                        <th>Minimo</th>
                                        <th>Utilizzi</th>
                                        <th>Stato</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($coupons as $couponRow): ?>
                                        <?php
                                            $isActive = (int) ($couponRow['is_active'] ?? 0) === 1;
                                            $valueLabel = ($couponRow['type'] ?? 'fixed') === 'percent'
                                                ? ((int) $couponRow['percent']) . '%'
                                                : ap_price_format((int) ($couponRow['amount_cents'] ?? 0));
                                            $minLabel = (int) ($couponRow['min_total_cents'] ?? 0) > 0
                                                ? ap_price_format((int) $couponRow['min_total_cents'])
                                                : 'Nessun minimo';
        
                                            $usageCount = (int) ($couponRow['redemptions_count'] ?? 0);
                                            $usageLimit = $couponRow['max_redemptions'] ?? null;
                                            $usageLabel = $usageLimit ? sprintf('%d / %d', $usageCount, (int) $usageLimit) : (string) $usageCount;
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($couponRow['code'], ENT_QUOTES); ?></strong>
                                                <?php if (!empty($couponRow['description'])): ?>
                                                    <div class="small text-muted"><?php echo htmlspecialchars($couponRow['description'], ENT_QUOTES); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars(ucfirst((string) $couponRow['type']), ENT_QUOTES); ?></td>
                                            <td><?php echo htmlspecialchars($valueLabel, ENT_QUOTES); ?></td>
                                            <td><?php echo htmlspecialchars($minLabel, ENT_QUOTES); ?></td>
                                            <td><?php echo htmlspecialchars($usageLabel, ENT_QUOTES); ?></td>
                                            <td>
                                                <span class="badge <?php echo $isActive ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'; ?>"><?php echo $isActive ? 'Attivo' : 'Pausa'; ?></span>
                                                <?php if (!empty($couponRow['ends_at'])): ?>
                                                    <div class="small text-muted">Fino al <?php echo date('d/m/Y H:i', strtotime((string) $couponRow['ends_at'])); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <a class="btn btn-sm btn-outline-primary" href="<?php echo htmlspecialchars($adminUrl(['coupon' => (int) $couponRow['id']], 'coupons'), ENT_QUOTES); ?>">Modifica</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($section === 'impostazioni'): ?>
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-0">Impostazioni sito</h4>
                    <small class="text-muted">Configura le impostazioni generali del sito ecommerce</small>
                </div>
            </div>
            <form method="post" class="settings-form">
                <input type="hidden" name="ap_action" value="admin_save_settings">
                <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl(['section' => 'impostazioni']), ENT_QUOTES); ?>">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h5>Generali</h5>
                        <div class="mb-3">
                            <label class="form-label" for="site_name">Nome sito</label>
                            <input class="form-control" type="text" id="site_name" name="settings[site_name]" value="<?php echo htmlspecialchars(ap_get_setting('site_name', 'Agenzia Plinio'), ENT_QUOTES); ?>">
                            <input type="hidden" name="setting_types[site_name]" value="string">
                            <input type="hidden" name="setting_descriptions[site_name]" value="Nome del sito mostrato nell'header">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="site_description">Descrizione sito</label>
                            <textarea class="form-control" id="site_description" name="settings[site_description]" rows="3"><?php echo htmlspecialchars(ap_get_setting('site_description', 'Servizi digitali per pratiche amministrative'), ENT_QUOTES); ?></textarea>
                            <input type="hidden" name="setting_types[site_description]" value="string">
                            <input type="hidden" name="setting_descriptions[site_description]" value="Descrizione del sito per meta tag">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="contact_email">Email contatto</label>
                            <input class="form-control" type="email" id="contact_email" name="settings[contact_email]" value="<?php echo htmlspecialchars(ap_get_setting('contact_email', 'info@agenziaplinio.it'), ENT_QUOTES); ?>">
                            <input type="hidden" name="setting_types[contact_email]" value="string">
                            <input type="hidden" name="setting_descriptions[contact_email]" value="Email principale per contatti">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="contact_phone">Telefono contatto</label>
                            <input class="form-control" type="tel" id="contact_phone" name="settings[contact_phone]" value="<?php echo htmlspecialchars(ap_get_setting('contact_phone', '+39 123 456 7890'), ENT_QUOTES); ?>">
                            <input type="hidden" name="setting_types[contact_phone]" value="string">
                            <input type="hidden" name="setting_descriptions[contact_phone]" value="Numero di telefono per contatti">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>Ecommerce</h5>
                        <div class="mb-3">
                            <label class="form-label" for="currency">Valuta</label>
                            <select class="form-select" id="currency" name="settings[currency]">
                                <option value="EUR" <?php echo ap_get_setting('currency', 'EUR') === 'EUR' ? 'selected' : ''; ?>>Euro (€)</option>
                                <option value="USD" <?php echo ap_get_setting('currency', 'EUR') === 'USD' ? 'selected' : ''; ?>>Dollaro ($)</option>
                            </select>
                            <input type="hidden" name="setting_types[currency]" value="string">
                            <input type="hidden" name="setting_descriptions[currency]" value="Valuta utilizzata nel sito">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="tax_rate">IVA (%)</label>
                            <input class="form-control" type="number" step="0.01" min="0" max="100" id="tax_rate" name="settings[tax_rate]" value="<?php echo htmlspecialchars((string) ap_get_setting('tax_rate', 22), ENT_QUOTES); ?>">
                            <input type="hidden" name="setting_types[tax_rate]" value="int">
                            <input type="hidden" name="setting_descriptions[tax_rate]" value="Aliquota IVA applicata">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="free_shipping_threshold">Soglia spedizione gratuita (€)</label>
                            <input class="form-control" type="number" step="0.01" min="0" id="free_shipping_threshold" name="settings[free_shipping_threshold]" value="<?php echo htmlspecialchars((string) ap_get_setting('free_shipping_threshold', 0), ENT_QUOTES); ?>">
                            <input type="hidden" name="setting_types[free_shipping_threshold]" value="int">
                            <input type="hidden" name="setting_descriptions[free_shipping_threshold]" value="Importo minimo per spedizione gratuita (0 = disabilitata)">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="maintenance_mode" name="settings[maintenance_mode]" value="1" <?php echo ap_get_setting('maintenance_mode', false) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="maintenance_mode">Modalità manutenzione</label>
                            <input type="hidden" name="setting_types[maintenance_mode]" value="bool">
                            <input type="hidden" name="setting_descriptions[maintenance_mode]" value="Abilita modalità manutenzione per il sito pubblico">
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button class="btn btn-primary" type="submit">Salva impostazioni</button>
                </div>
            </form>
        </div>
        <?php endif; ?>
        <?php if ($section === 'statistiche'): ?>
        <?php $stats = ap_get_advanced_stats(); ?>
        <div class="admin-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">Statistiche avanzate</h4>
                    <small class="text-muted">Analisi dettagliata delle performance ecommerce</small>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">Ultimo aggiornamento: <?php echo date('d/m/Y H:i'); ?></small>
                    <small class="text-muted">Periodo: ultimi 12 mesi</small>
                </div>
            </div>
            
            <!-- Metriche principali -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="stat-card">
                        <p class="stat-label">Clienti totali</p>
                        <h3><?php echo number_format($stats['total_customers']); ?></h3>
                        <small class="text-muted">Attivi negli ultimi 30gg: <?php echo number_format($stats['active_customers']); ?></small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <p class="stat-label">Tasso conversione</p>
                        <h3><?php echo number_format($stats['conversion_rate'], 1); ?>%</h3>
                        <small class="text-muted">Sessioni → Ordini (30gg)</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <p class="stat-label">Sessioni abbandonate</p>
                        <h3><?php echo number_format($stats['total_sessions_30d']); ?></h3>
                        <small class="text-muted">Ordini: <?php echo number_format($stats['total_orders_30d']); ?></small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <p class="stat-label">Stati ordini</p>
                        <div class="d-flex gap-2 mt-2">
                            <?php foreach ($stats['order_statuses'] as $status => $count): ?>
                                <span class="badge bg-light text-dark"><?php echo ucfirst($status); ?>: <?php echo $count; ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Grafico ricavi mensili -->
            <div class="row g-4 mb-5">
                <div class="col-12">
                    <div class="admin-card">
                        <h5 class="mb-3">Ricavi mensili (ultimi 12 mesi)</h5>
                        <canvas id="revenueChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Prodotti più venduti -->
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="admin-card">
                        <h5 class="mb-3">Prodotti più venduti</h5>
                        <?php if (empty($stats['top_products'])): ?>
                            <p class="text-muted">Nessun dato disponibile</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Prodotto</th>
                                            <th>Venduti</th>
                                            <th>Ricavo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($stats['top_products'] as $product): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></td>
                                                <td><?php echo number_format($product['total_sold']); ?></td>
                                                <td><?php echo ap_price_format($product['total_revenue']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="admin-card">
                        <h5 class="mb-3">Ticket medio mensile</h5>
                        <canvas id="avgOrderChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($section === 'analytics'): ?>
        <div class="admin-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">Analytics Dashboard</h4>
                    <small class="text-muted">Analisi dettagliata del comportamento degli utenti e performance ecommerce</small>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">Ultimo aggiornamento: <?php echo date('d/m/Y H:i'); ?></small>
                    <small class="text-muted">Periodo: ultimi 30 giorni</small>
                </div>
            </div>

            <!-- Metriche principali -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="stat-card">
                        <p class="stat-label">Sessioni totali</p>
                        <h3><?php echo number_format($analyticsSummary['total_sessions'] ?? 0); ?></h3>
                        <small class="text-muted">Ultimi 30 giorni</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <p class="stat-label">Visualizzazioni prodotti</p>
                        <h3><?php echo number_format($analyticsSummary['product_views'] ?? 0); ?></h3>
                        <small class="text-muted">Interazioni con catalogo</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <p class="stat-label">Aggiunte al carrello</p>
                        <h3><?php echo number_format($analyticsSummary['add_to_cart'] ?? 0); ?></h3>
                        <small class="text-muted">Conversioni dal catalogo</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <p class="stat-label">Ordini completati</p>
                        <h3><?php echo number_format($analyticsSummary['orders_completed'] ?? 0); ?></h3>
                        <small class="text-muted">Transazioni riuscite</small>
                    </div>
                </div>
            </div>

            <!-- Conversion Funnel -->
            <div class="row g-4 mb-5">
                <div class="col-12">
                    <div class="admin-card">
                        <h5 class="mb-3">Conversion Funnel (ultimi 30 giorni)</h5>
                        <div class="conversion-funnel">
                            <?php
                            $funnelSteps = [
                                ['label' => 'Sessioni', 'value' => $conversionFunnel['sessions'] ?? 0, 'color' => 'primary'],
                                ['label' => 'Visualizzazioni prodotti', 'value' => $conversionFunnel['product_views'] ?? 0, 'color' => 'info'],
                                ['label' => 'Aggiunte al carrello', 'value' => $conversionFunnel['add_to_cart'] ?? 0, 'color' => 'warning'],
                                ['label' => 'Ordini completati', 'value' => $conversionFunnel['orders_completed'] ?? 0, 'color' => 'success']
                            ];
                            $maxValue = max(array_column($funnelSteps, 'value'));
                            ?>
                            <?php foreach ($funnelSteps as $step): ?>
                                <div class="funnel-step">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold"><?php echo htmlspecialchars($step['label'], ENT_QUOTES); ?></span>
                                        <span class="badge bg-<?php echo $step['color']; ?>"><?php echo number_format($step['value']); ?></span>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-<?php echo $step['color']; ?>" role="progressbar"
                                             style="width: <?php echo $maxValue > 0 ? ($step['value'] / $maxValue * 100) : 0; ?>%"
                                             aria-valuenow="<?php echo $step['value']; ?>" aria-valuemin="0" aria-valuemax="<?php echo $maxValue; ?>">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products & User Actions -->
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="admin-card">
                        <h5 class="mb-3">Prodotti più visualizzati</h5>
                        <?php if (empty($analyticsSummary['top_products'])): ?>
                            <p class="text-muted">Nessun dato disponibile</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Prodotto</th>
                                            <th>Visualizzazioni</th>
                                            <th>Conversion Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($analyticsSummary['top_products'], 0, 10) as $product): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></td>
                                                <td><?php echo number_format($product['views']); ?></td>
                                                <td><?php echo $product['views'] > 0 ? number_format(($product['orders'] / $product['views']) * 100, 1) : 0; ?>%</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="admin-card">
                        <h5 class="mb-3">Attività utenti (ultimi 30 giorni)</h5>
                        <div class="user-activity-list">
                            <?php
                            $activities = [
                                ['label' => 'Aggiunte alla wishlist', 'value' => $analyticsSummary['wishlist_adds'] ?? 0, 'icon' => '❤️'],
                                ['label' => 'Recensioni lasciate', 'value' => $analyticsSummary['reviews_submitted'] ?? 0, 'icon' => '⭐'],
                                ['label' => 'Carrelli abbandonati', 'value' => $analyticsSummary['abandoned_carts'] ?? 0, 'icon' => '🛒'],
                                ['label' => 'Notifiche lette', 'value' => $analyticsSummary['notifications_read'] ?? 0, 'icon' => '📧']
                            ];
                            ?>
                            <?php foreach ($activities as $activity): ?>
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <span class="me-3"><?php echo $activity['icon']; ?></span>
                                        <span><?php echo htmlspecialchars($activity['label'], ENT_QUOTES); ?></span>
                                    </div>
                                    <span class="badge bg-light text-dark"><?php echo number_format($activity['value']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Traffic Sources & Device Types -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="admin-card">
                        <h5 class="mb-3">Sorgenti di traffico</h5>
                        <canvas id="trafficSourcesChart" width="400" height="200"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="admin-card">
                        <h5 class="mb-3">Dispositivi utilizzati</h5>
                        <canvas id="deviceTypesChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($section === 'sicurezza'): ?>
        <?php 
            $securityLogs = ap_get_security_logs(100, 0);
            $eventTypes = [
                'login_success' => ['label' => 'Accesso riuscito', 'color' => 'success'],
                'login_failed' => ['label' => 'Accesso fallito', 'color' => 'danger'],
                'logout' => ['label' => 'Disconnessione', 'color' => 'secondary'],
                'password_change' => ['label' => 'Cambio password', 'color' => 'info'],
                'user_created' => ['label' => 'Utente creato', 'color' => 'primary'],
                'user_updated' => ['label' => 'Utente modificato', 'color' => 'warning'],
                'admin_action' => ['label' => 'Azione admin', 'color' => 'dark'],
                'security_alert' => ['label' => 'Allerta sicurezza', 'color' => 'danger'],
            ];
        ?>
        <div class="admin-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">Log di Sicurezza</h4>
                    <small class="text-muted">Monitoraggio attività e accessi al sistema</small>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block"><?php echo $securityLogs['total']; ?> eventi totali</small>
                    <small class="text-muted">Ultimo aggiornamento: <?php echo date('d/m/Y H:i'); ?></small>
                </div>
            </div>
            
            <!-- Filtri e ricerca -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <select class="form-select" id="eventFilter">
                        <option value="">Tutti gli eventi</option>
                        <?php foreach ($eventTypes as $key => $config): ?>
                            <option value="<?php echo $key; ?>"><?php echo $config['label']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="userFilter" placeholder="Filtra per utente/email">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="ipFilter" placeholder="Filtra per IP">
                </div>
            </div>
            
            <!-- Tabella log -->
            <div class="table-responsive">
                <table class="table table-sm align-middle" id="securityLogsTable">
                    <thead>
                        <tr>
                            <th>Data/Ora</th>
                            <th>Evento</th>
                            <th>Utente</th>
                            <th>IP</th>
                            <th>Dettagli</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($securityLogs['logs'])): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Nessun evento di sicurezza registrato
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($securityLogs['logs'] as $log): ?>
                                <tr data-event-type="<?php echo htmlspecialchars($log['event_type'], ENT_QUOTES); ?>">
                                    <td>
                                        <div class="small"><?php echo date('d/m/Y', strtotime($log['created_at'])); ?></div>
                                        <div class="small text-muted"><?php echo date('H:i:s', strtotime($log['created_at'])); ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $eventTypes[$log['event_type']]['color'] ?? 'secondary'; ?>">
                                            <?php echo $eventTypes[$log['event_type']]['label'] ?? ucfirst(str_replace('_', ' ', $log['event_type'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($log['user_name'])): ?>
                                            <strong><?php echo htmlspecialchars($log['user_name'], ENT_QUOTES); ?></strong>
                                            <div class="small text-muted"><?php echo htmlspecialchars($log['user_email'], ENT_QUOTES); ?></div>
                                        <?php elseif (!empty($log['user_id'])): ?>
                                            <span class="text-muted">User #<?php echo $log['user_id']; ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <code class="small"><?php echo htmlspecialchars($log['ip_address'], ENT_QUOTES); ?></code>
                                    </td>
                                    <td>
                                        <div><?php echo htmlspecialchars($log['description'], ENT_QUOTES); ?></div>
                                        <?php if (!empty($log['metadata'])): ?>
                                            <details class="mt-1">
                                                <summary class="small text-muted" style="cursor: pointer;">Dettagli tecnici</summary>
                                                <pre class="small mt-1"><code><?php echo htmlspecialchars(json_encode(json_decode($log['metadata'], true), JSON_PRETTY_PRINT), ENT_QUOTES); ?></code></pre>
                                            </details>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginazione se necessario -->
            <?php if ($securityLogs['total'] > 100): ?>
                <nav class="mt-3">
                    <ul class="pagination pagination-sm mb-0 justify-content-center">
                        <li class="page-item disabled">
                            <span class="page-link">Prima pagina (ultimi 100 eventi)</span>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if ($section === 'audit'): ?>
        <div class="admin-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">Audit Log</h4>
                    <small class="text-muted">Monitoraggio errori e attività del sistema</small>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block"><?php echo $auditLogsTotal; ?> eventi totali</small>
                    <small class="text-muted">Ultimo aggiornamento: <?php echo date('d/m/Y H:i'); ?></small>
                </div>
            </div>
            
            <!-- Filtri e ricerca -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <select class="form-select" id="auditEventFilter">
                        <option value="all">Tutti gli eventi</option>
                        <?php foreach ($auditEventTypes as $key => $config): ?>
                            <option value="<?php echo $key; ?>" <?php echo $auditEventFilter === $key ? 'selected' : ''; ?>><?php echo $config['label']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="auditUserFilter" placeholder="Filtra per utente" value="<?php echo htmlspecialchars($auditUserFilter, ENT_QUOTES); ?>">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="auditSearchFilter" placeholder="Ricerca descrizione" value="<?php echo htmlspecialchars($auditSearchTerm, ENT_QUOTES); ?>">
                </div>
            </div>
            
            <!-- Tabella audit log -->
            <div class="table-responsive">
                <table class="table table-sm align-middle" id="auditLogsTable">
                    <thead>
                        <tr>
                            <th>Data/Ora</th>
                            <th>Evento</th>
                            <th>Utente</th>
                            <th>IP</th>
                            <th>Descrizione</th>
                            <th>Metadata</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($auditLogs)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Nessun evento audit registrato
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($auditLogs as $log): ?>
                                <tr data-event-type="<?php echo htmlspecialchars($log['event_type'], ENT_QUOTES); ?>">
                                    <td>
                                        <div class="small"><?php echo date('d/m/Y', strtotime($log['created_at'])); ?></div>
                                        <div class="small text-muted"><?php echo date('H:i:s', strtotime($log['created_at'])); ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $auditEventTypes[$log['event_type']]['color'] ?? 'secondary'; ?>">
                                            <?php echo $auditEventTypes[$log['event_type']]['label'] ?? ucfirst(str_replace('_', ' ', $log['event_type'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($log['user_name'])): ?>
                                            <strong><?php echo htmlspecialchars($log['user_name'], ENT_QUOTES); ?></strong>
                                            <div class="small text-muted"><?php echo htmlspecialchars($log['user_email'], ENT_QUOTES); ?></div>
                                        <?php elseif (!empty($log['user_id'])): ?>
                                            <span class="text-muted">User #<?php echo $log['user_id']; ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <code class="small"><?php echo htmlspecialchars($log['ip_address'], ENT_QUOTES); ?></code>
                                    </td>
                                    <td>
                                        <div><?php echo htmlspecialchars($log['description'], ENT_QUOTES); ?></div>
                                    </td>
                                    <td>
                                        <?php if (!empty($log['metadata'])): ?>
                                            <details class="mt-1">
                                                <summary class="small text-muted" style="cursor: pointer;">Dettagli</summary>
                                                <pre class="small mt-1"><code><?php echo htmlspecialchars(json_encode(json_decode($log['metadata'], true), JSON_PRETTY_PRINT), ENT_QUOTES); ?></code></pre>
                                            </details>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginazione -->
            <?php if ($auditLogsPages > 1): ?>
                <nav class="mt-3">
                    <ul class="pagination pagination-sm mb-0 justify-content-center">
                        <?php $prevPage = max(1, $auditLogsPage - 1); ?>
                        <li class="page-item <?php echo $auditLogsPage === 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo htmlspecialchars($buildAuditLogsPageUrl($prevPage), ENT_QUOTES); ?>" aria-label="Pagina precedente">&laquo;</a>
                        </li>
                        <?php for ($pageNumber = 1; $pageNumber <= $auditLogsPages; $pageNumber++): ?>
                            <li class="page-item <?php echo $pageNumber === $auditLogsPage ? 'active' : ''; ?>">
                                <a class="page-link" href="<?php echo htmlspecialchars($buildAuditLogsPageUrl($pageNumber), ENT_QUOTES); ?>"><?php echo $pageNumber; ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php $nextPage = min($auditLogsPages, $auditLogsPage + 1); ?>
                        <li class="page-item <?php echo $auditLogsPage === $auditLogsPages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo htmlspecialchars($buildAuditLogsPageUrl($nextPage), ENT_QUOTES); ?>" aria-label="Pagina successiva">&raquo;</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if ($section === 'sicurezza'): ?>
        <script>
            // Filtri per i log di sicurezza
            document.addEventListener('DOMContentLoaded', function() {
                const eventFilter = document.getElementById('eventFilter');
                const userFilter = document.getElementById('userFilter');
                const ipFilter = document.getElementById('ipFilter');
                const table = document.getElementById('securityLogsTable');
                const rows = table.querySelectorAll('tbody tr');
                
                function filterLogs() {
                    const eventValue = eventFilter.value.toLowerCase();
                    const userValue = userFilter.value.toLowerCase();
                    const ipValue = ipFilter.value.toLowerCase();
                    
                    rows.forEach(row => {
                        if (row.cells.length < 5) return; // Skip header or empty rows
                        
                        const eventType = row.dataset.eventType || '';
                        const userText = row.cells[2].textContent.toLowerCase();
                        const ipText = row.cells[3].textContent.toLowerCase();
                        
                        const matchesEvent = !eventValue || eventType === eventValue;
                        const matchesUser = !userValue || userText.includes(userValue);
                        const matchesIp = !ipValue || ipText.includes(ipValue);
                        
                        row.style.display = (matchesEvent && matchesUser && matchesIp) ? '' : 'none';
                    });
                }
                
                eventFilter.addEventListener('change', filterLogs);
                userFilter.addEventListener('input', filterLogs);
                ipFilter.addEventListener('input', filterLogs);
            });
        </script>
        <?php endif; ?>
        <?php if ($section === 'audit'): ?>
        <script>
            // Filtri per i log di audit
            document.addEventListener('DOMContentLoaded', function() {
                const eventFilter = document.getElementById('auditEventFilter');
                const userFilter = document.getElementById('auditUserFilter');
                const searchFilter = document.getElementById('auditSearchFilter');
                
                function applyFilters() {
                    const eventValue = eventFilter.value;
                    const userValue = userFilter.value.toLowerCase();
                    const searchValue = searchFilter.value.toLowerCase();
                    
                    const params = new URLSearchParams(window.location.search);
                    params.set('section', 'audit');
                    
                    if (eventValue !== 'all') {
                        params.set('audit_event', eventValue);
                    } else {
                        params.delete('audit_event');
                    }
                    
                    if (userValue !== '') {
                        params.set('audit_user', userValue);
                    } else {
                        params.delete('audit_user');
                    }
                    
                    if (searchValue !== '') {
                        params.set('audit_q', searchValue);
                    } else {
                        params.delete('audit_q');
                    }
                    
                    window.location.href = '?' + params.toString();
                }
                
                eventFilter.addEventListener('change', applyFilters);
                userFilter.addEventListener('input', function() {
                    clearTimeout(this.timeout);
                    this.timeout = setTimeout(applyFilters, 500);
                });
                searchFilter.addEventListener('input', function() {
                    clearTimeout(this.timeout);
                    this.timeout = setTimeout(applyFilters, 500);
                });
            });
        </script>
        <?php endif; ?>
        <script>
            (function () {
                const typeField = document.getElementById('coupon_type_admin');
                if (!typeField) {
                    return;
                }
                const fixedField = document.getElementById('coupon_value_fixed');
                const percentField = document.getElementById('coupon_value_percent');
                const toggleFields = () => {
                    const type = typeField.value;
                    if (fixedField) {
                        fixedField.parentElement.style.opacity = type === 'fixed' ? '1' : '0.5';
                    }
                    if (percentField) {
                        percentField.parentElement.style.opacity = type === 'percent' ? '1' : '0.5';
                    }
                };
                typeField.addEventListener('change', toggleFields);
                toggleFields();
            })();
        </script>
        <script>
            (function () {
                let fieldCounter = 0;
                const container = document.getElementById('custom-fields-container');
                const addBtn = document.getElementById('add-custom-field');
                if (!container || !addBtn) {
                    return;
                }

                const createFieldHTML = (id) => `
                    <div class="custom-field-item border rounded p-3 mb-3" data-field-id="${id}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Nome campo</label>
                                <input class="form-control" type="text" name="custom_fields[${id}][name]" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Etichetta</label>
                                <input class="form-control" type="text" name="custom_fields[${id}][label]" required>
                            </div>
                            <div class="col-md-2 field-options-container">
                                <label class="form-label">Tipo</label>
                                <select class="form-select field-type-select" name="custom_fields[${id}][type]">
                                    <option value="text">Testo</option>
                                    <option value="textarea">Area testo</option>
                                    <option value="select">Selezione</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="provincia">Provincia</option>
                                    <option value="comune">Comune</option>
                                    <option value="cap">CAP</option>
                                    <option value="citta">Città</option>
                                </select>
                            </div>
                            <div class="col-md-2 field-options-container">
                                <label class="form-label">Opzioni (per select)</label>
                                <input class="form-control" type="text" name="custom_fields[${id}][options]" placeholder="Opzione1,Opzione2">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Obbligatorio</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="custom_fields[${id}][required]">
                                </div>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-sm btn-danger remove-field">Rimuovi</button>
                            </div>
                        </div>
                    </div>
                `;

                const updateFieldOptions = (container) => {
                    const typeSelect = container.querySelector('.field-type-select');
                    const optionsContainer = container.querySelector('.field-options-container');
                    const update = () => {
                        const type = typeSelect.value;
                        let label = 'Valore predefinito';
                        let input = '';
                        if (type === 'select') {
                            label = 'Opzioni (per select)';
                            input = `<input class="form-control" type="text" name="custom_fields[${container.dataset.fieldId}][options]" placeholder="Opzione1,Opzione2" value="${optionsContainer.querySelector('input') ? optionsContainer.querySelector('input').value : ''}">`;
                        } else if (type === 'provincia') {
                            label = 'Provincia selezionata';
                            const currentValue = optionsContainer.querySelector('input')?.value || '';
                            const provinces = <?php echo json_encode($italianProvinces); ?>;
                            let optionsHtml = '<option value="">Seleziona provincia</option>';
                            provinces.forEach(prov => {
                                const selected = prov === currentValue ? ' selected' : '';
                                optionsHtml += `<option value="${prov}"${selected}>${prov}</option>`;
                            });
                            input = `<select class="form-select" name="custom_fields[${container.dataset.fieldId}][options]">${optionsHtml}</select>`;
                        } else {
                            input = `<input class="form-control" type="text" name="custom_fields[${container.dataset.fieldId}][options]" placeholder="Valore" value="${optionsContainer.querySelector('input') ? optionsContainer.querySelector('input').value : ''}">`;
                        }
                        optionsContainer.innerHTML = `<label class="form-label">${label}</label>${input}`;
                    };
                    typeSelect.addEventListener('change', update);
                    update(); // initial
                };

                // Update existing fields
                document.querySelectorAll('.custom-field-item').forEach(updateFieldOptions);

                addBtn.addEventListener('click', () => {
                    container.insertAdjacentHTML('beforeend', createFieldHTML(`new_${fieldCounter++}`));
                    updateFieldOptions(container.lastElementChild);
                });

                container.addEventListener('click', (e) => {
                    if (e.target.classList.contains('remove-field')) {
                        e.target.closest('.custom-field-item').remove();
                    }
                });
            })();
        </script>
        <script>
            // Handle delete product modal
            const deleteProductModal = document.getElementById('deleteProductModal');
            if (deleteProductModal) {
                deleteProductModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const productId = button.getAttribute('data-product-id');
                    const productName = button.getAttribute('data-product-name');
                    
                    const productNameElement = document.getElementById('productName');
                    const productIdInput = document.getElementById('deleteProductId');
                    
                    if (productNameElement) {
                        productNameElement.textContent = productName;
                    }
                    if (productIdInput) {
                        productIdInput.value = productId;
                    }
                });
            }
        </script>
        <script>
            // Bulk actions for products
            document.addEventListener('DOMContentLoaded', function() {
                const masterCheckbox = document.getElementById('productMasterCheckbox');
                const productCheckboxes = document.querySelectorAll('.product-checkbox');
                const selectAllBtn = document.getElementById('selectAllProducts');
                const deselectAllBtn = document.getElementById('deselectAllProducts');
                const bulkActivateBtn = document.getElementById('bulkActivateProducts');
                const bulkDeactivateBtn = document.getElementById('bulkDeactivateProducts');
                const bulkDeleteBtn = document.getElementById('bulkDeleteProducts');
                const bulkDeleteModal = document.getElementById('bulkDeleteProductsModal');
                const bulkDeleteForm = document.getElementById('bulkDeleteProductsForm');
                const bulkDeleteProductIds = document.getElementById('bulkDeleteProductIds');
                const bulkDeleteProductList = document.getElementById('bulkDeleteProductList');
                
                function updateBulkButtons() {
                    const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
                    const hasSelection = checkedBoxes.length > 0;
                    
                    if (bulkActivateBtn) bulkActivateBtn.disabled = !hasSelection;
                    if (bulkDeactivateBtn) bulkDeactivateBtn.disabled = !hasSelection;
                    if (bulkDeleteBtn) bulkDeleteBtn.disabled = !hasSelection;
                    
                    // Update master checkbox state
                    if (masterCheckbox) {
                        const totalCheckboxes = productCheckboxes.length;
                        const checkedCount = checkedBoxes.length;
                        
                        masterCheckbox.checked = checkedCount === totalCheckboxes && totalCheckboxes > 0;
                        masterCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCheckboxes;
                    }
                }
                
                function getSelectedProductIds() {
                    return Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
                }
                
                function getSelectedProductNames() {
                    return Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.getAttribute('data-product-name'));
                }
                
                // Master checkbox handler
                if (masterCheckbox) {
                    masterCheckbox.addEventListener('change', function() {
                        productCheckboxes.forEach(cb => {
                            cb.checked = this.checked;
                        });
                        updateBulkButtons();
                    });
                }
                
                // Individual checkboxes handler
                productCheckboxes.forEach(cb => {
                    cb.addEventListener('change', updateBulkButtons);
                });
                
                // Select all button
                if (selectAllBtn) {
                    selectAllBtn.addEventListener('click', function() {
                        productCheckboxes.forEach(cb => {
                            cb.checked = true;
                        });
                        updateBulkButtons();
                    });
                }
                
                // Deselect all button
                if (deselectAllBtn) {
                    deselectAllBtn.addEventListener('click', function() {
                        productCheckboxes.forEach(cb => {
                            cb.checked = false;
                        });
                        updateBulkButtons();
                    });
                }
                
                // Bulk activate
                if (bulkActivateBtn) {
                    bulkActivateBtn.addEventListener('click', function() {
                        const selectedIds = getSelectedProductIds();
                        if (selectedIds.length === 0) return;
                        
                        const form = document.createElement('form');
                        form.method = 'post';
                        form.style.display = 'none';
                        
                        const actionInput = document.createElement('input');
                        actionInput.type = 'hidden';
                        actionInput.name = 'ap_action';
                        actionInput.value = 'admin_bulk_update_products';
                        form.appendChild(actionInput);
                        
                        const redirectInput = document.createElement('input');
                        redirectInput.type = 'hidden';
                        redirectInput.name = 'redirect_to';
                        redirectInput.value = '<?php echo htmlspecialchars($adminUrl(['section' => 'catalogo']), ENT_QUOTES); ?>';
                        form.appendChild(redirectInput);
                        
                        const statusInput = document.createElement('input');
                        statusInput.type = 'hidden';
                        statusInput.name = 'status';
                        statusInput.value = 'active';
                        form.appendChild(statusInput);
                        
                        selectedIds.forEach(id => {
                            const idInput = document.createElement('input');
                            idInput.type = 'hidden';
                            idInput.name = 'product_ids[]';
                            idInput.value = id;
                            form.appendChild(idInput);
                        });
                        
                        document.body.appendChild(form);
                        form.submit();
                    });
                }
                
                // Bulk deactivate
                if (bulkDeactivateBtn) {
                    bulkDeactivateBtn.addEventListener('click', function() {
                        const selectedIds = getSelectedProductIds();
                        if (selectedIds.length === 0) return;
                        
                        const form = document.createElement('form');
                        form.method = 'post';
                        form.style.display = 'none';
                        
                        const actionInput = document.createElement('input');
                        actionInput.type = 'hidden';
                        actionInput.name = 'ap_action';
                        actionInput.value = 'admin_bulk_update_products';
                        form.appendChild(actionInput);
                        
                        const redirectInput = document.createElement('input');
                        redirectInput.type = 'hidden';
                        redirectInput.name = 'redirect_to';
                        redirectInput.value = '<?php echo htmlspecialchars($adminUrl(['section' => 'catalogo']), ENT_QUOTES); ?>';
                        form.appendChild(redirectInput);
                        
                        const statusInput = document.createElement('input');
                        statusInput.type = 'hidden';
                        statusInput.name = 'status';
                        statusInput.value = 'inactive';
                        form.appendChild(statusInput);
                        
                        selectedIds.forEach(id => {
                            const idInput = document.createElement('input');
                            idInput.type = 'hidden';
                            idInput.name = 'product_ids[]';
                            idInput.value = id;
                            form.appendChild(idInput);
                        });
                        
                        document.body.appendChild(form);
                        form.submit();
                    });
                }
                
                // Bulk delete modal handler
                if (bulkDeleteModal) {
                    bulkDeleteModal.addEventListener('show.bs.modal', function() {
                        const selectedIds = getSelectedProductIds();
                        const selectedNames = getSelectedProductNames();
                        
                        if (bulkDeleteProductIds) {
                            bulkDeleteProductIds.value = selectedIds.join(',');
                        }
                        
                        if (bulkDeleteProductList) {
                            bulkDeleteProductList.innerHTML = '<strong>Prodotti selezionati:</strong><ul class="mb-0 mt-1">';
                            selectedNames.forEach(name => {
                                bulkDeleteProductList.innerHTML += '<li>' + name + '</li>';
                            });
                            bulkDeleteProductList.innerHTML += '</ul>';
                        }
                    });
                }
                
                // Initialize button states
                updateBulkButtons();
            });
        </script>
        <script>
            // Handle delete user modal
            const deleteUserModal = document.getElementById('deleteUserModal');
            if (deleteUserModal) {
                deleteUserModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const userId = button.getAttribute('data-user-id');
                    const userName = button.getAttribute('data-user-name');
                    const userEmail = button.getAttribute('data-user-email');
                    
                    const userNameElement = document.getElementById('userName');
                    const userEmailElement = document.getElementById('userEmail');
                    const userIdInput = document.getElementById('deleteUserId');
                    
                    if (userNameElement) {
                        userNameElement.textContent = userName;
                    }
                    if (userEmailElement) {
                        userEmailElement.textContent = userEmail;
                    }
                    if (userIdInput) {
                        userIdInput.value = userId;
                    }
                });
            }
            
            // Handle confirm delete user button
            const confirmDeleteUserBtn = document.getElementById('confirmDeleteUserBtn');
            if (confirmDeleteUserBtn) {
                confirmDeleteUserBtn.addEventListener('click', function() {
                    const userIdInput = document.getElementById('deleteUserId');
                    if (userIdInput && userIdInput.value) {
                        document.getElementById('deleteUserForm').submit();
                    }
                });
            }
        </script>
        <?php if ($section === 'statistiche'): ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart');
            if (revenueCtx) {
                const revenueData = <?php echo json_encode(array_values($stats['revenue_by_month'])); ?>;
                const revenueLabels = <?php echo json_encode(array_keys($stats['revenue_by_month'])); ?>;
                
                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: revenueLabels.map(label => {
                            const date = new Date(label + '-01');
                            return date.toLocaleDateString('it-IT', { year: 'numeric', month: 'short' });
                        }),
                        datasets: [{
                            label: 'Ricavi (€)',
                            data: revenueData.map(item => item.revenue / 100),
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: 'Ordini',
                            data: revenueData.map(item => item.orders),
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            yAxisID: 'y1',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Ricavi (€)'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Ordini'
                                },
                                grid: {
                                    drawOnChartArea: false,
                                },
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        if (context.datasetIndex === 0) {
                                            return 'Ricavi: €' + context.parsed.y.toFixed(2);
                                        } else {
                                            return 'Ordini: ' + context.parsed.y;
                                        }
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            // Average Order Value Chart
            const avgOrderCtx = document.getElementById('avgOrderChart');
            if (avgOrderCtx) {
                const avgOrderData = <?php echo json_encode(array_values($stats['avg_order_value'])); ?>;
                const avgOrderLabels = <?php echo json_encode(array_keys($stats['avg_order_value'])); ?>;
                
                new Chart(avgOrderCtx, {
                    type: 'bar',
                    data: {
                        labels: avgOrderLabels.map(label => {
                            const date = new Date(label + '-01');
                            return date.toLocaleDateString('it-IT', { year: 'numeric', month: 'short' });
                        }),
                        datasets: [{
                            label: 'Ticket medio (€)',
                            data: avgOrderData.map(value => value / 100),
                            backgroundColor: '#ffc107',
                            borderColor: '#e0a800',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Euro (€)'
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Ticket medio: €' + context.parsed.y.toFixed(2);
                                    }
                                }
                            }
                        }
                    }
                });
            }
        </script>
        <?php endif; ?>
        <?php if ($section === 'analytics'): ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Traffic Sources Chart
            const trafficSourcesCtx = document.getElementById('trafficSourcesChart');
            if (trafficSourcesCtx) {
                const trafficData = <?php echo json_encode($analyticsSummary['traffic_sources'] ?? ['Diretto' => 40, 'Google' => 30, 'Social' => 20, 'Altro' => 10]); ?>;
                
                new Chart(trafficSourcesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(trafficData),
                        datasets: [{
                            data: Object.values(trafficData),
                            backgroundColor: [
                                '#007bff', // Direct
                                '#28a745', // Google
                                '#dc3545', // Social
                                '#ffc107', // Other
                                '#6c757d', // Additional colors if needed
                                '#17a2b8'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            // Device Types Chart
            const deviceTypesCtx = document.getElementById('deviceTypesChart');
            if (deviceTypesCtx) {
                const deviceData = <?php echo json_encode($analyticsSummary['device_types'] ?? ['Desktop' => 60, 'Mobile' => 35, 'Tablet' => 5]); ?>;
                
                new Chart(deviceTypesCtx, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(deviceData),
                        datasets: [{
                            data: Object.values(deviceData),
                            backgroundColor: [
                                '#007bff', // Desktop
                                '#28a745', // Mobile
                                '#ffc107'  // Tablet
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        </script>
        <?php endif; ?>
    </div>
</section>

<!-- Delete Product Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteProductModalLabel">Conferma eliminazione</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body">
                <p>Sei sicuro di voler eliminare definitivamente il prodotto <strong id="productName"></strong>?</p>
                <p class="text-muted small">Questa azione non può essere annullata.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <form method="post" id="deleteProductForm" class="d-inline">
                    <input type="hidden" name="ap_action" value="admin_delete_product">
                    <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl(['section' => 'catalogo']), ENT_QUOTES); ?>">
                    <input type="hidden" name="product_id" id="deleteProductId">
                    <button type="submit" class="btn btn-danger">Elimina definitivamente</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Products Modal -->
<div class="modal fade" id="bulkDeleteProductsModal" tabindex="-1" aria-labelledby="bulkDeleteProductsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkDeleteProductsModalLabel">Conferma eliminazione multipla</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <div class="modal-body">
                <p>Sei sicuro di voler eliminare definitivamente i prodotti selezionati?</p>
                <div id="bulkDeleteProductList" class="small text-muted"></div>
                <p class="text-danger small mt-2">Questa azione non può essere annullata.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <form method="post" id="bulkDeleteProductsForm" class="d-inline">
                    <input type="hidden" name="ap_action" value="admin_bulk_delete_products">
                    <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl(['section' => 'catalogo']), ENT_QUOTES); ?>">
                    <input type="hidden" name="product_ids" id="bulkDeleteProductIds">
                    <button type="submit" class="btn btn-danger">Elimina definitivamente</button>
                </form>
            </div>
        </div>
    </div>
</div>
