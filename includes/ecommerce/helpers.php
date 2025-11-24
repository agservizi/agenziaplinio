<?php
declare(strict_types=1);


require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../cache.php';

function ap_price_format(int $cents): string
{
    $value = $cents / 100;
    return number_format($value, 2, ',', '.') . ' €';
}

function ap_flash(string $message, string $type = 'info'): void
{
    $_SESSION['ap_flash'][] = [
        'message' => $message,
        'type' => $type,
    ];
}

function ap_random_token(int $length = 32): string
{
    $bytes = max(1, (int) ceil($length / 2));
    return substr(bin2hex(random_bytes($bytes)), 0, $length);
}

function ap_slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/i', '-', $value) ?? '';
    return trim($value, '-') ?: ap_random_token(8);
}

function ap_fetch_products(array $filters = [], bool $onlyActive = true): array
{
    $cacheKey = 'products_' . md5(serialize([$filters, $onlyActive]));
    $cached = ap_cache_get($cacheKey, 300);
    if ($cached !== null) {
        return $cached;
    }
    $pdo = ap_db();
    $sql = 'SELECT * FROM products WHERE 1=1';
    $params = [];

    if ($onlyActive) {
        $sql .= ' AND is_active = 1';
    }

    if (!empty($filters['ids']) && is_array($filters['ids'])) {
        $ids = array_values(array_filter(array_map('intval', $filters['ids']), static fn ($value) => $value > 0));
        if ($ids) {
            $placeholders = [];
            foreach ($ids as $index => $id) {
                $key = ':id' . $index;
                $placeholders[] = $key;
                $params[$key] = $id;
            }
            $sql .= ' AND id IN (' . implode(',', $placeholders) . ')';
        }
    }

    if (!empty($filters['search'])) {
        $sql .= ' AND (name LIKE :search OR description LIKE :search OR sku LIKE :search)';
        $params[':search'] = '%' . $filters['search'] . '%';
    }

    if (!empty($filters['in_stock'])) {
        $sql .= ' AND stock > 0';
    }

    if (isset($filters['price_min']) && $filters['price_min'] !== null) {
        $sql .= ' AND price_cents >= :price_min';
        $params[':price_min'] = (int) $filters['price_min'];
    }

    if (isset($filters['price_max']) && $filters['price_max'] !== null) {
        $sql .= ' AND price_cents <= :price_max';
        $params[':price_max'] = (int) $filters['price_max'];
    }

    $sort = $filters['sort'] ?? 'newest';
    $orderBy = match ($sort) {
        'price_asc' => 'ORDER BY price_cents ASC, name ASC',
        'price_desc' => 'ORDER BY price_cents DESC, name ASC',
        'alpha' => 'ORDER BY name ASC',
        'stock' => 'ORDER BY stock DESC, name ASC',
        default => 'ORDER BY created_at DESC',
    };

    if (!empty($filters['limit'])) {
        $limit = max(1, (int) $filters['limit']);
        $orderBy .= ' LIMIT ' . $limit;
    }

    $stmt = $pdo->prepare($sql . ' ' . $orderBy);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    ap_cache_set($cacheKey, $results, 300);
    return $results;
}

function ap_find_product(int $id): ?array
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch();
    return $product ?: null;
}

function ap_find_product_by_slug(string $slug): ?array
{
    $slug = trim($slug);
    if ($slug === '') {
        return null;
    }
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT * FROM products WHERE slug = :slug LIMIT 1');
    $stmt->execute([':slug' => $slug]);
    $product = $stmt->fetch();
    return $product ?: null;
}

function ap_save_product(array $data, ?int $id = null): bool
{
    $pdo = ap_db();
    $categoryKey = $data['category_key'] ?? null;
    if ($categoryKey !== null) {
        $categoryKey = trim((string) $categoryKey) ?: null;
    }
    $fulfillmentType = $data['fulfillment_type'] ?? 'digital';
    if (!in_array($fulfillmentType, ['digital', 'physical'], true)) {
        $fulfillmentType = 'digital';
    }
    if ($id) {
        $stmt = $pdo->prepare('UPDATE products SET name = :name, slug = :slug, description = :description, price_cents = :price, sku = :sku, category_key = :category_key, fulfillment_type = :fulfillment_type, stock = :stock, image_url = :image, is_active = :active WHERE id = :id');
        $result = $stmt->execute([
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':description' => $data['description'],
            ':price' => $data['price_cents'],
            ':sku' => $data['sku'],
            ':category_key' => $categoryKey,
            ':fulfillment_type' => $fulfillmentType,
            ':stock' => $data['stock'],
            ':image' => $data['image_url'],
            ':active' => $data['is_active'],
            ':id' => $id,
        ]);
        if ($result && $stmt->rowCount() > 0) {
            ap_cache_forget_prefix('products_');
            return true;
        } else {
            return false; // Product not found or no changes
        }
    }
    $stmt = $pdo->prepare('INSERT INTO products (name, slug, description, price_cents, sku, category_key, fulfillment_type, stock, image_url, is_active) VALUES (:name, :slug, :description, :price, :sku, :category_key, :fulfillment_type, :stock, :image, :active)');
    $result = $stmt->execute([
        ':name' => $data['name'],
        ':slug' => $data['slug'],
        ':description' => $data['description'],
        ':price' => $data['price_cents'],
        ':sku' => $data['sku'],
        ':category_key' => $categoryKey,
        ':fulfillment_type' => $fulfillmentType,
        ':stock' => $data['stock'],
        ':image' => $data['image_url'],
        ':active' => $data['is_active'],
    ]);
    if ($result) {
        ap_cache_forget_prefix('products_');
    }
    return $result;
}

function ap_delete_product(int $id): bool
{
    $pdo = ap_db();
    $pdo->beginTransaction();
    try {
        // Elimina prima i custom fields
        $stmt = $pdo->prepare('DELETE FROM product_custom_fields WHERE product_id = :id');
        $stmt->execute([':id' => $id]);

        // Elimina gli order_items (eliminazione forzata)
        $stmt = $pdo->prepare('DELETE FROM order_items WHERE product_id = :id');
        $stmt->execute([':id' => $id]);

        // Infine elimina il prodotto
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
        $result = $stmt->execute([':id' => $id]);

        if ($result) {
            ap_cache_forget_prefix('products_');
            $pdo->commit();
            return true;
        } else {
            $pdo->rollBack();
            return false;
        }
    } catch (Throwable $exception) {
        $pdo->rollBack();
        error_log('Errore eliminazione prodotto forzata: ' . $exception->getMessage());
        return false;
    }
}

function ap_product_category_map(): array
{
    return [
        'anagrafe' => [
            'label' => 'Certificati Anagrafe',
            'parent' => 'certificazioni-documenti',
            'keywords' => ['anagrafe', 'residenza', 'stato famiglia', 'storico familiare'],
            'highlights' => ['Certificati anagrafici ufficiali', 'Richiesta online veloce', 'Consegna digitale immediata'],
        ],
        'stato-civile' => [
            'label' => 'Stato Civile',
            'parent' => 'certificazioni-documenti',
            'keywords' => ['stato civile', 'nascita', 'matrimonio', 'morte', 'divorzio'],
            'highlights' => ['Certificati di nascita, matrimonio, divorzio o morte', 'Richiesta in pochi minuti', 'Documenti ufficiali certificati'],
        ],
        'giudiziarie' => [
            'label' => 'Giudiziarie',
            'parent' => 'certificazioni-documenti',
            'keywords' => ['giudiziario', 'casellario', 'carichi pendenti'],
            'highlights' => ['Casellario Giudiziale e Carichi Pendenti', 'Richiesta sicura e riservata', 'Documentazione legale affidabile'],
        ],
        'camerali' => [
            'label' => 'Certificati Camerali',
            'parent' => 'certificazioni-documenti',
            'keywords' => ['camera commercio', 'visura', 'iscrizione', 'certificato camerale'],
            'highlights' => ['Certificati ordinari o storici per aziende', 'Ditte individuali e società', 'Ideali per pratiche commerciali e amministrative'],
        ],
        'catastali' => [
            'label' => 'Visure Catastali',
            'parent' => 'certificazioni-documenti',
            'keywords' => ['catasto', 'visura catastale', 'proprietà', 'planimetria'],
            'highlights' => ['Dati catastali di immobili e terreni', 'Proprietà, rendite e planimetrie', 'Documenti per compravendite'],
        ],
        'ipotecarie' => [
            'label' => 'Visure Ipotecarie',
            'parent' => 'certificazioni-documenti',
            'keywords' => ['ipoteca', 'visura ipotecaria', 'pignoramento', 'trascrizione'],
            'highlights' => ['Verifica ipoteche, pignoramenti e trascrizioni', 'Su immobili o terreni', 'Documentazione completa e aggiornata'],
        ],
        'pra' => [
            'label' => 'Visure PRA (veicoli)',
            'parent' => 'certificazioni-documenti',
            'keywords' => ['pra', 'veicoli', 'auto', 'moto', 'immatricolazione', 'pubblico registro automobilistico'],
            'highlights' => ['Visure auto, moto e veicoli complete', 'Dati proprietario e situazione giuridica', 'Storico veicoli dettagliato'],
        ],
        'aziendali' => [
            'label' => 'Certificati Aziendali',
            'parent' => 'certificazioni-documenti',
            'keywords' => ['azienda', 'certificato aziendale', 'impresa', 'società', 'partita iva'],
            'highlights' => ['Documenti ufficiali per imprese e società', 'Ideali per gare, contratti e pratiche fiscali', 'Certificati di iscrizione e attività'],
        ],
        'privati' => [
            'label' => 'Certificati per Privati',
            'parent' => 'certificazioni-documenti',
            'keywords' => ['privato', 'certificato personale', 'scolastico', 'comunale', 'anagrafico'],
            'highlights' => ['Documenti su misura per esigenze personali', 'Certificati scolastici, comunali, anagrafici', 'Supporto completo per pratiche private'],
        ],
        'cambio-residenza' => [
            'label' => 'Cambio Residenza',
            'parent' => 'pratiche-online',
            'keywords' => ['cambio residenza', 'domicilio', 'trasferimento', 'anagrafe'],
            'highlights' => ['Procedura completa di cambio residenza', 'Senza code e moduli complicati', 'Assistenza passo-passo'],
        ],
        'pratiche-anagrafiche' => [
            'label' => 'Pratiche Anagrafiche',
            'parent' => 'pratiche-online',
            'keywords' => ['pratica anagrafica', 'anagrafe', 'aggiornamento', 'dichiarazione'],
            'highlights' => ['Richieste, aggiornamenti e dichiarazioni', 'Presso l\'anagrafe comunale', 'Gestione digitale completa'],
        ],
        'veicoli' => [
            'label' => 'Pratiche Veicoli (PRA)',
            'parent' => 'pratiche-online',
            'keywords' => ['veicoli', 'auto', 'moto', 'pra', 'immatricolazione', 'passaggio proprietà'],
            'highlights' => ['Passaggi di proprietà, visure e richieste', 'Presso il Pubblico Registro Automobilistico', 'Immatricolazioni e aggiornamenti'],
        ],
        'uffici-pubblici' => [
            'label' => 'Richieste agli Uffici Comunali',
            'parent' => 'pratiche-online',
            'keywords' => ['ufficio comunale', 'comune', 'richiesta', 'segnalazione', 'certificazione'],
            'highlights' => ['Invio e gestione di domande, segnalazioni', 'Presso il tuo Comune', 'Certificazioni e pratiche comunali'],
        ],
        'enti-pubblici' => [
            'label' => 'Domande verso Enti Pubblici',
            'parent' => 'pratiche-online',
            'keywords' => ['ente pubblico', 'inps', 'agenzia entrate', 'richiesta', 'pratica'],
            'highlights' => ['Assistenza nella presentazione di richieste', 'A INPS, Agenzia delle Entrate e altri enti', 'Gestione completa delle pratiche'],
        ],
        'documenti-rapidi' => [
            'label' => 'Recupero Documenti',
            'parent' => 'pratiche-online',
            'keywords' => ['recupero documento', 'duplicato', 'smarrito', 'urgente'],
            'highlights' => ['Recupero di copie, certificati o documenti', 'Smarrirti o non più disponibili', 'Servizio express e urgente'],
        ],
        'ricerca-certificati' => [
            'label' => 'Ricerca Certificati',
            'parent' => 'pratiche-online',
            'keywords' => ['ricerca certificato', 'certificato archiviato', 'non reperibile'],
            'highlights' => ['Troviamo e otteniamo certificati', 'Anche non facilmente reperibili o archiviati', 'Ricerca specializzata'],
        ],
        'deleghe-prenotazioni' => [
            'label' => 'Deleghe e Prenotazioni Digitali',
            'parent' => 'pratiche-online',
            'keywords' => ['delega', 'prenotazione', 'appuntamento', 'digitale'],
            'highlights' => ['Compiliamo e gestiamo deleghe', 'Appuntamenti e prenotazioni presso enti', 'Servizio completamente digitale'],
        ],
        'servizi-premium' => [
            'label' => 'Servizi Premium',
            'parent' => 'servizi-digitali',
            'keywords' => [],
            'highlights' => ['Setup white-glove', 'Monitoraggio stato in tempo reale', 'Account manager dedicato'],
        ],
    ];
}

function ap_product_category_parent_map(): array
{
    return [
        'certificazioni-documenti' => [
            'label' => 'Certificazioni & Documenti',
            'description' => 'Richiedi certificati e documenti ufficiali in modo semplice e veloce, senza file e senza burocrazia. Li reperiamo noi per te e te li consegniamo direttamente online.',
            'parent' => 'servizi-digitali',
        ],
        'pratiche-online' => [
            'label' => 'Pratiche Online',
            'description' => 'Gestiamo per te pratiche e richieste verso comuni, enti pubblici e uffici, risparmiandoti tempo e spostamenti.',
            'parent' => 'servizi-digitali',
        ],
        'servizi-digitali' => [
            'label' => 'Servizi Digitali',
            'description' => 'Servizi digitali completi per pratiche amministrative, certificati e documenti ufficiali.',
            'parent' => null,
        ],
    ];
}

function ap_product_category_parent(string $key): ?string
{
    $map = ap_product_category_map();
    return $map[$key]['parent'] ?? null;
}

function ap_product_category_parent_description(string $parentKey): ?string
{
    $parentMap = ap_product_category_parent_map();
    return $parentMap[$parentKey]['description'] ?? null;
}

function ap_product_category_options(): array
{
    $map = ap_product_category_map();
    $parentMap = ap_product_category_parent_map();
    $options = [];
    foreach ($map as $key => $meta) {
        $parentKey = $meta['parent'] ?? null;
        $parentLabel = $parentKey && isset($parentMap[$parentKey]) ? $parentMap[$parentKey]['label'] : null;
        $label = $parentLabel ? $parentLabel . ' > ' . $meta['label'] : $meta['label'];
        $options[$key] = $label;
    }
    return $options;
}

function ap_product_category_key(array $product): string
{
    $map = ap_product_category_map();
    $stored = trim((string) ($product['category_key'] ?? ''));
    if ($stored !== '' && isset($map[$stored])) {
        return $stored;
    }
    $haystack = strtolower(($product['name'] ?? '') . ' ' . ($product['description'] ?? '') . ' ' . ($product['sku'] ?? ''));
    foreach ($map as $key => $meta) {
        foreach ($meta['keywords'] as $keyword) {
            if ($keyword !== '' && str_contains($haystack, $keyword)) {
                return $key;
            }
        }
    }
    return 'servizi-premium';
}

function ap_product_category_label(array $product): string
{
    $map = ap_product_category_map();
    $key = ap_product_category_key($product);
    return $map[$key]['label'] ?? 'Servizi premium';
}

function ap_product_fulfillment_type(array $product): string
{
    $stored = strtolower(trim((string) ($product['fulfillment_type'] ?? '')));
    if (in_array($stored, ['digital', 'physical'], true)) {
        return $stored;
    }
    $physicalKeys = ['logistica-spedizioni'];
    $key = ap_product_category_key($product);
    if (in_array($key, $physicalKeys, true)) {
        return 'physical';
    }
    return 'digital';
}

function ap_product_fulfillment_label(array $product): string
{
    return ap_product_fulfillment_type($product) === 'physical'
        ? 'Prodotto fisico'
        : 'Servizio digitale';
}

function ap_product_is_new(array $product, int $days = 30): bool
{
    if (empty($product['created_at'])) {
        return false;
    }
    $created = strtotime((string) $product['created_at']);
    if ($created === false) {
        return false;
    }
    $threshold = time() - ($days * 86400);
    return $created >= $threshold;
}

function ap_product_badges(array $product): array
{
    $badges = [];
    if (ap_product_is_new($product)) {
        $badges[] = ['label' => 'Novità', 'variant' => 'success'];
    }
    $stock = (int) ($product['stock'] ?? 0);
    if ($stock === 0) {
        $badges[] = ['label' => 'Sold out', 'variant' => 'secondary'];
    } elseif ($stock <= 5) {
        $badges[] = ['label' => 'Ultimi pezzi', 'variant' => 'warning'];
    }
    if (($product['price_cents'] ?? 0) >= 20000) {
        $badges[] = ['label' => 'Enterprise', 'variant' => 'info'];
    }
    return $badges;
}

function ap_product_highlights(array $product): array
{
    $map = ap_product_category_map();
    $key = ap_product_category_key($product);
    $highlights = $map[$key]['highlights'] ?? [];
    if ($highlights) {
        return $highlights;
    }
    return ['Setup white-glove', 'Monitoraggio stato in tempo reale', 'Account manager dedicato'];
}

function ap_shop_metrics(array $products): array
{
    $total = count($products);
    $available = 0;
    $lowStock = 0;
    $outOfStock = 0;
    $totalPrice = 0;
    foreach ($products as $product) {
        $stock = (int) ($product['stock'] ?? 0);
        $price = (int) ($product['price_cents'] ?? 0);
        $totalPrice += $price;
        if ($stock > 0) {
            $available++;
            if ($stock <= 5) {
                $lowStock++;
            }
        } else {
            $outOfStock++;
        }
    }
    $averagePrice = $total > 0 ? (int) round($totalPrice / $total) : 0;
    return [
        'total' => $total,
        'available' => $available,
        'low_stock' => $lowStock,
        'sold_out' => $outOfStock,
        'average_price' => $averagePrice,
    ];
}

function ap_fetch_orders(?int $userId = null, array $filters = []): array
{
    $pdo = ap_db();
    $query = ap_orders_base_query($userId, $filters);
    $sql = $query['sql'];
    $params = $query['params'];
    $sql .= ' ORDER BY orders.created_at DESC';
    if (!empty($filters['limit']) && (int) $filters['limit'] > 0) {
        $limit = max(1, (int) $filters['limit']);
        $sql .= ' LIMIT ' . $limit;
        if (isset($filters['offset']) && (int) $filters['offset'] > 0) {
            $sql .= ' OFFSET ' . max(0, (int) $filters['offset']);
        }
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function ap_count_orders(?int $userId = null, array $filters = []): int
{
    $pdo = ap_db();
    $query = ap_orders_base_query($userId, $filters);
    $sql = 'SELECT COUNT(*) AS total FROM (' . $query['sql'] . ') AS orders_wrapped';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($query['params']);
    return (int) ($stmt->fetch()['total'] ?? 0);
}

function ap_orders_base_query(?int $userId, array $filters): array
{
    $conditions = [];
    $params = [];

    if ($userId) {
        $conditions[] = 'orders.user_id = :user';
        $params[':user'] = $userId;
    }

    if (!empty($filters['status'])) {
        $conditions[] = 'orders.status = :status';
        $params[':status'] = $filters['status'];
    }

    if (!empty($filters['shipping_status'])) {
        $conditions[] = 'orders.shipping_status = :shipping_status';
        $params[':shipping_status'] = $filters['shipping_status'];
    }

    if (!empty($filters['payment_status'])) {
        $conditions[] = 'orders.payment_status = :payment_status';
        $params[':payment_status'] = $filters['payment_status'];
    }

    if (!empty($filters['search'])) {
        $search = trim((string) $filters['search']);
        if ($search !== '') {
            $fragments = ['users.name LIKE :search', 'users.email LIKE :search'];
            $params[':search'] = '%' . $search . '%';
            if (ctype_digit($search)) {
                $fragments[] = 'orders.id = :search_id';
                $params[':search_id'] = (int) $search;
            }
            $conditions[] = '(' . implode(' OR ', $fragments) . ')';
        }
    }

    $sql = 'SELECT orders.*, users.name AS customer_name, users.email AS customer_email FROM orders INNER JOIN users ON users.id = orders.user_id';
    if ($conditions) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    return ['sql' => $sql, 'params' => $params];
}

function ap_fetch_order_items(int $orderId): array
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT order_items.*, products.name FROM order_items INNER JOIN products ON products.id = order_items.product_id WHERE order_id = :order');
    $stmt->execute([':order' => $orderId]);
    return $stmt->fetchAll();
}

function ap_fetch_order_item(int $orderId, int $productId): ?array
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT order_items.*, products.name FROM order_items INNER JOIN products ON products.id = order_items.product_id WHERE order_id = :order AND product_id = :product LIMIT 1');
    $stmt->execute([':order' => $orderId, ':product' => $productId]);
    $item = $stmt->fetch();
    return $item ?: null;
}

function ap_fetch_order(int $orderId): ?array
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT orders.*, users.email AS customer_email FROM orders INNER JOIN users ON users.id = orders.user_id WHERE orders.id = :id LIMIT 1');
    $stmt->execute([':id' => $orderId]);
    $order = $stmt->fetch();
    return $order ?: null;
}

function ap_fetch_order_bundle(int $orderId): ?array
{
    $order = ap_fetch_order($orderId);
    if (!$order) {
        return null;
    }
    $items = ap_fetch_order_items($orderId);
    return ['order' => $order, 'items' => $items];
}

function ap_order_meta_array(null|string $value): array
{
    if ($value === null || $value === '') {
        return [];
    }
    $decoded = json_decode((string) $value, true);
    return is_array($decoded) ? $decoded : [];
}

function ap_order_procurement_files(null|string $value): array
{
    $files = ap_order_meta_array($value);
    return array_values(array_filter($files, static function ($row) {
        return is_array($row) && !empty($row['path']);
    }));
}

function ap_create_order(int $userId, array $items, array $payload): int
{
    $pdo = ap_db();
    $pdo->beginTransaction();
    try {
        $orderStmt = $pdo->prepare('INSERT INTO orders (user_id, shipping_address_id, status, total_cents, shipping_name, shipping_address, shipping_method, shipping_status, shipping_cost_cents, shipping_notes, contact_phone, po_number, cost_center, delivery_window, sla_plan, addons, ops_channels, contract_ref, procurement_files, coupon_code, coupon_id, discount_cents) VALUES (:user, :address_id, :status, :total, :name, :address, :shipping_method, :shipping_status, :shipping_cost, :notes, :phone, :po, :cost_center, :delivery_window, :sla_plan, :addons, :ops_channels, :contract_ref, :procurement_files, :coupon_code, :coupon_id, :discount_cents)');
        $orderStmt->execute([
            ':user' => $userId,
            ':address_id' => $payload['shipping_address_id'] ?? null,
            ':status' => 'pending',
            ':total' => $payload['total_cents'],
            ':name' => $payload['shipping_name'],
            ':address' => $payload['shipping_address'],
            ':shipping_method' => $payload['shipping_method'] ?? 'standard',
            ':shipping_status' => 'preparing',
            ':shipping_cost' => $payload['shipping_cost_cents'] ?? 0,
            ':notes' => $payload['shipping_notes'] ?? null,
            ':phone' => $payload['contact_phone'] ?? null,
            ':po' => $payload['po_number'] ?? null,
            ':cost_center' => $payload['cost_center'] ?? null,
            ':delivery_window' => $payload['delivery_window'] ?? null,
            ':sla_plan' => $payload['sla_plan'] ?? null,
            ':addons' => !empty($payload['addons']) ? json_encode(array_values($payload['addons']), JSON_UNESCAPED_UNICODE) : null,
            ':ops_channels' => !empty($payload['ops_channels']) ? json_encode(array_values($payload['ops_channels']), JSON_UNESCAPED_UNICODE) : null,
            ':contract_ref' => $payload['contract_ref'] ?? null,
            ':procurement_files' => !empty($payload['procurement_files']) ? json_encode(array_values($payload['procurement_files']), JSON_UNESCAPED_UNICODE) : null,
            ':coupon_code' => $payload['coupon_code'] ?? null,
            ':coupon_id' => $payload['coupon_id'] ?? null,
            ':discount_cents' => max(0, (int) ($payload['discount_cents'] ?? 0)),
        ]);
        $orderId = (int) $pdo->lastInsertId();

        $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price_cents) VALUES (:order, :product, :qty, :price)');
        foreach ($items as $item) {
            $itemStmt->execute([
                ':order' => $orderId,
                ':product' => $item['product_id'],
                ':qty' => $item['quantity'],
                ':price' => $item['price_cents'],
            ]);
            $stockStmt = $pdo->prepare('UPDATE products SET stock = GREATEST(stock - :qty, 0) WHERE id = :product');
            $stockStmt->execute([
                ':qty' => $item['quantity'],
                ':product' => $item['product_id'],
            ]);
        }
        if (!empty($payload['coupon_id'])) {
            $usageStmt = $pdo->prepare('UPDATE coupons SET redemptions_count = redemptions_count + 1 WHERE id = :id');
            $usageStmt->execute([':id' => (int) $payload['coupon_id']]);
        }
        $pdo->commit();
        return $orderId;
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

function ap_record_payment(int $orderId, array $payload): void
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('INSERT INTO payments (order_id, provider, status, amount_cents, transaction_ref, meta) VALUES (:order, :provider, :status, :amount, :ref, :meta)');
    $stmt->execute([
        ':order' => $orderId,
        ':provider' => $payload['provider'] ?? 'manual',
        ':status' => $payload['status'] ?? 'pending',
        ':amount' => $payload['amount_cents'] ?? 0,
        ':ref' => $payload['transaction_ref'] ?? null,
        ':meta' => json_encode($payload['meta'] ?? [], JSON_UNESCAPED_UNICODE),
    ]);
    if (!empty($payload['order_status'])) {
        $update = $pdo->prepare('UPDATE orders SET status = :status WHERE id = :order');
        $update->execute([':status' => $payload['order_status'], ':order' => $orderId]);
    }
    if (!empty($payload['payment_status'])) {
        $update = $pdo->prepare('UPDATE orders SET payment_method = :method, payment_status = :payment WHERE id = :order');
        $update->execute([
            ':method' => $payload['provider'] ?? 'manual',
            ':payment' => $payload['payment_status'],
            ':order' => $orderId,
        ]);
    }
}

function ap_find_order_id_by_transaction_ref(string $reference): ?int
{
    $trimmed = trim($reference);
    if ($trimmed === '') {
        return null;
    }
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT order_id FROM payments WHERE transaction_ref = :ref ORDER BY id DESC LIMIT 1');
    $stmt->execute([':ref' => $trimmed]);
    $orderId = $stmt->fetchColumn();
    return $orderId ? (int) $orderId : null;
}

function ap_update_shipping(int $orderId, array $data): void
{
    $pdo = ap_db();
    $current = ap_fetch_order($orderId) ?: [];
    $stmt = $pdo->prepare('UPDATE orders SET shipping_status = :status, tracking_code = :tracking, shipping_method = :method WHERE id = :order');
    $stmt->execute([
        ':status' => $data['shipping_status'] ?? ($current['shipping_status'] ?? 'preparing'),
        ':tracking' => $data['tracking_code'] ?? ($current['tracking_code'] ?? null),
        ':method' => $data['shipping_method'] ?? ($current['shipping_method'] ?? 'standard'),
        ':order' => $orderId,
    ]);
}

function ap_shipping_methods(): array
{
    return [
        'standard' => ['label' => 'Standard 48h', 'price_cents' => 490],
        'express' => ['label' => 'Express 24h', 'price_cents' => 990],
    ];
}

function ap_abandoned_cart_session_id(): string
{
    $sessionId = session_id();
    if ($sessionId === '') {
        $sessionId = $_SESSION['ap_session_fallback'] ?? bin2hex(random_bytes(16));
        $_SESSION['ap_session_fallback'] = $sessionId;
    }
    return $sessionId;
}

function ap_abandoned_cart_save_session(array $payload): void
{
    $sessionId = ap_abandoned_cart_session_id();
    $pdo = ap_db();
    $snapshot = json_encode($payload['items'], JSON_UNESCAPED_UNICODE);
    $stmt = $pdo->prepare('INSERT INTO abandoned_carts (session_id, user_id, email, cart_snapshot, total_cents, coupon_code, discount_cents, last_activity, notified_at) VALUES (:session_id, :user_id, :email, :snapshot, :total, :coupon, :discount, :last_activity, NULL) ON DUPLICATE KEY UPDATE user_id = VALUES(user_id), email = VALUES(email), cart_snapshot = VALUES(cart_snapshot), total_cents = VALUES(total_cents), coupon_code = VALUES(coupon_code), discount_cents = VALUES(discount_cents), last_activity = VALUES(last_activity), notified_at = NULL');
    $stmt->execute([
        ':session_id' => $sessionId,
        ':user_id' => $payload['user_id'],
        ':email' => $payload['email'],
        ':snapshot' => $snapshot,
        ':total' => $payload['total_cents'],
        ':coupon' => $payload['coupon_code'],
        ':discount' => $payload['discount_cents'],
        ':last_activity' => date('Y-m-d H:i:s'),
    ]);
}

function ap_abandoned_cart_clear_session(?string $sessionId = null): void
{
    $session = $sessionId ?? ap_abandoned_cart_session_id();
    $pdo = ap_db();
    $stmt = $pdo->prepare('DELETE FROM abandoned_carts WHERE session_id = :session');
    $stmt->execute([':session' => $session]);
}

function ap_process_abandoned_carts(): void
{
    static $processed = false;
    if ($processed) {
        return;
    }
    $processed = true;
    $pdo = ap_db();
    $threshold = (new DateTime('-3 hours'))->format('Y-m-d H:i:s');
    $stmt = $pdo->prepare('SELECT * FROM abandoned_carts WHERE notified_at IS NULL AND email IS NOT NULL AND last_activity <= :threshold LIMIT 20');
    $stmt->execute([':threshold' => $threshold]);
    $carts = $stmt->fetchAll();
    if ($carts) {
        foreach ($carts as $cart) {
            try {
                ap_notify_abandoned_cart($cart);
            } catch (Throwable $exception) {
                continue;
            }
            $update = $pdo->prepare('UPDATE abandoned_carts SET notified_at = NOW() WHERE id = :id');
            $update->execute([':id' => $cart['id']]);
        }
    }
    $cleanupThreshold = (new DateTime('-30 days'))->format('Y-m-d H:i:s');
    $cleanup = $pdo->prepare('DELETE FROM abandoned_carts WHERE last_activity <= :cutoff AND (notified_at IS NOT NULL OR total_cents = 0)');
    $cleanup->execute([':cutoff' => $cleanupThreshold]);
}

function ap_fetch_abandoned_carts(int $limit = 10): array
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT * FROM abandoned_carts ORDER BY last_activity DESC LIMIT :limit');
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function ap_find_coupon_by_id(int $couponId): ?array
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT * FROM coupons WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $couponId]);
    $coupon = $stmt->fetch();
    return $coupon ?: null;
}

function ap_find_coupon_by_code(string $code): ?array
{
    $normalized = strtoupper(trim($code));
    if ($normalized === '') {
        return null;
    }
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT * FROM coupons WHERE UPPER(code) = :code LIMIT 1');
    $stmt->execute([':code' => $normalized]);
    $coupon = $stmt->fetch();
    return $coupon ?: null;
}

function ap_generate_unique_coupon_code(int $length = 10, int $maxAttempts = 12): string
{
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $alphabetLength = strlen($alphabet);
    $length = max(6, min(16, $length));
    $attempt = 0;
    do {
        $attempt++;
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $index = random_int(0, $alphabetLength - 1);
            $code .= $alphabet[$index];
        }
        if (!ap_find_coupon_by_code($code)) {
            return $code;
        }
    } while ($attempt < $maxAttempts);
    $fallback = strtoupper(substr('AP' . ap_random_token($length + 4), 0, $length));
    if ($fallback !== '') {
        return $fallback;
    }
    return strtoupper(substr('PL' . ap_random_token($length + 4), 0, $length));
}

function ap_fetch_coupons(array $filters = []): array
{
    $pdo = ap_db();
    $conditions = [];
    $params = [];
    if (isset($filters['is_active'])) {
        $conditions[] = 'is_active = :active';
        $params[':active'] = (int) $filters['is_active'];
    }
    $sql = 'SELECT * FROM coupons';
    if ($conditions) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }
    $sql .= ' ORDER BY created_at DESC';
    if (!empty($filters['limit'])) {
        $sql .= ' LIMIT ' . (int) $filters['limit'];
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function ap_coupon_validate(array $coupon, int $subtotalCents): ?string
{
    if ($subtotalCents <= 0) {
        return 'Il carrello è vuoto.';
    }
    if ((int) ($coupon['is_active'] ?? 0) !== 1) {
        return 'Questo coupon non è attivo.';
    }
    $now = time();
    if (!empty($coupon['starts_at'])) {
        $startsAt = strtotime((string) $coupon['starts_at']);
        if ($startsAt !== false && $now < $startsAt) {
            return 'Il coupon non è ancora valido.';
        }
    }
    if (!empty($coupon['ends_at'])) {
        $endsAt = strtotime((string) $coupon['ends_at']);
        if ($endsAt !== false && $now > $endsAt) {
            return 'Il coupon è scaduto.';
        }
    }
    $minTotal = (int) ($coupon['min_total_cents'] ?? 0);
    if ($minTotal > 0 && $subtotalCents < $minTotal) {
        $formatted = ap_price_format($minTotal);
        return 'Ordine minimo per usare il coupon: ' . $formatted . '.';
    }
    $maxRedemptions = $coupon['max_redemptions'] ?? null;
    if ($maxRedemptions !== null && $maxRedemptions !== '') {
        if ((int) $coupon['redemptions_count'] >= (int) $maxRedemptions) {
            return 'Il coupon ha esaurito gli utilizzi disponibili.';
        }
    }
    $discount = ap_coupon_discount($coupon, $subtotalCents);
    if ($discount <= 0) {
        return 'Il coupon non genera alcun vantaggio su questo ordine.';
    }
    return null;
}

function ap_coupon_discount(array $coupon, int $subtotalCents): int
{
    $subtotal = max(0, $subtotalCents);
    $type = $coupon['type'] ?? 'fixed';
    if ($type === 'percent') {
        $percent = (int) max(0, min(100, (int) ($coupon['percent'] ?? 0)));
        $discount = (int) floor($subtotal * $percent / 100);
    } else {
        $discount = (int) ($coupon['amount_cents'] ?? 0);
    }
    return max(0, min($discount, $subtotal));
}

function ap_save_coupon(array $data, ?int $couponId = null): ?int
{
    $payload = [
        ':code' => strtoupper(trim($data['code'] ?? '')),
        ':description' => $data['description'] ?? null,
        ':type' => $data['type'] ?? 'fixed',
        ':amount' => (int) max(0, $data['amount_cents'] ?? 0),
        ':percent' => (int) max(0, min(100, $data['percent'] ?? 0)),
        ':min_total' => (int) max(0, $data['min_total_cents'] ?? 0),
        ':max_redemptions' => $data['max_redemptions'] !== null ? (int) $data['max_redemptions'] : null,
        ':starts_at' => $data['starts_at'] ?? null,
        ':ends_at' => $data['ends_at'] ?? null,
        ':active' => isset($data['is_active']) ? (int) $data['is_active'] : 1,
    ];
    $pdo = ap_db();
    if ($couponId) {
        $payload[':id'] = $couponId;
        $stmt = $pdo->prepare('UPDATE coupons SET code = :code, description = :description, type = :type, amount_cents = :amount, percent = :percent, min_total_cents = :min_total, max_redemptions = :max_redemptions, starts_at = :starts_at, ends_at = :ends_at, is_active = :active WHERE id = :id');
        $stmt->execute($payload);
        return $couponId;
    }
    $stmt = $pdo->prepare('INSERT INTO coupons (code, description, type, amount_cents, percent, min_total_cents, max_redemptions, starts_at, ends_at, is_active) VALUES (:code, :description, :type, :amount, :percent, :min_total, :max_redemptions, :starts_at, :ends_at, :active)');
    $stmt->execute($payload);
    return (int) $pdo->lastInsertId();
}

function ap_fetch_addresses(int $userId): array
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT * FROM addresses WHERE user_id = :user ORDER BY is_default DESC, created_at DESC');
    $stmt->execute([':user' => $userId]);
    return $stmt->fetchAll();
}

function ap_find_address(int $userId, int $addressId): ?array
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT * FROM addresses WHERE id = :id AND user_id = :user LIMIT 1');
    $stmt->execute([':id' => $addressId, ':user' => $userId]);
    $address = $stmt->fetch();
    return $address ?: null;
}

function ap_save_address(int $userId, array $data, ?int $addressId = null): ?int
{
    $pdo = ap_db();
    $payload = [
        ':user' => $userId,
        ':label' => $data['label'],
        ':recipient' => $data['recipient_name'],
        ':street' => $data['street'],
        ':city' => $data['city'],
        ':province' => $data['province'],
        ':postal' => $data['postal_code'],
        ':country' => $data['country'],
        ':phone' => $data['phone'],
        ':default' => $data['is_default'] ?? 0,
    ];

    if ($addressId) {
        $payload[':id'] = $addressId;
        $stmt = $pdo->prepare('UPDATE addresses SET label = :label, recipient_name = :recipient, street = :street, city = :city, province = :province, postal_code = :postal, country = :country, phone = :phone, is_default = :default WHERE id = :id AND user_id = :user');
        $stmt->execute($payload);
        if (!empty($data['is_default'])) {
            ap_set_default_address($userId, $addressId);
        }
        return $addressId;
    }

    if (!empty($data['is_default'])) {
        ap_clear_default_addresses($userId);
    }

    $stmt = $pdo->prepare('INSERT INTO addresses (user_id, label, recipient_name, street, city, province, postal_code, country, phone, is_default) VALUES (:user, :label, :recipient, :street, :city, :province, :postal, :country, :phone, :default)');
    $stmt->execute($payload);
    $newId = (int) $pdo->lastInsertId();
    if (!empty($data['is_default']) && $newId) {
        ap_set_default_address($userId, $newId);
    }
    return $newId ?: null;
}

function ap_delete_address(int $userId, int $addressId): void
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('DELETE FROM addresses WHERE id = :id AND user_id = :user');
    $stmt->execute([':id' => $addressId, ':user' => $userId]);
}

function ap_set_default_address(int $userId, int $addressId): void
{
    ap_clear_default_addresses($userId);
    $pdo = ap_db();
    $stmt = $pdo->prepare('UPDATE addresses SET is_default = 1 WHERE id = :id AND user_id = :user');
    $stmt->execute([':id' => $addressId, ':user' => $userId]);
}

function ap_clear_default_addresses(int $userId): void
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('UPDATE addresses SET is_default = 0 WHERE user_id = :user');
    $stmt->execute([':user' => $userId]);
}

function ap_default_address(int $userId): ?array
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT * FROM addresses WHERE user_id = :user ORDER BY is_default DESC, created_at DESC LIMIT 1');
    $stmt->execute([':user' => $userId]);
    $address = $stmt->fetch();
    return $address ?: null;
}

function ap_format_address(array $address): string
{
    $parts = [];
    $parts[] = trim($address['street']);
    $cityLine = trim($address['postal_code'] . ' ' . $address['city']);
    if (!empty($address['province'])) {
        $cityLine .= ' (' . strtoupper($address['province']) . ')';
    }
    $parts[] = trim($cityLine);
    $parts[] = trim($address['country']);
    return implode("\n", array_filter($parts));
}

function ap_admin_insights(): array
{
    $pdo = ap_db();
    $totalOrders = (int) ($pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn() ?? 0);
    $totalRevenue = (int) ($pdo->query('SELECT COALESCE(SUM(total_cents), 0) FROM orders')->fetchColumn() ?? 0);
    $recentRevenue = (int) ($pdo->query('SELECT COALESCE(SUM(total_cents), 0) FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)')->fetchColumn() ?? 0);
    $pendingShipments = (int) ($pdo->query('SELECT COUNT(*) FROM orders WHERE shipping_status IN ("preparing", "in_transit")')->fetchColumn() ?? 0);
    $awaitingPayments = (int) ($pdo->query('SELECT COUNT(*) FROM orders WHERE payment_status IN ("pending", "failed")')->fetchColumn() ?? 0);
    $avgOrder = $totalOrders > 0 ? (int) round($totalRevenue / $totalOrders) : 0;

    return [
        'total_orders' => $totalOrders,
        'total_revenue' => $totalRevenue,
        'recent_revenue' => $recentRevenue,
        'average_order' => $avgOrder,
        'pending_shipments' => $pendingShipments,
        'awaiting_payments' => $awaitingPayments,
    ];
}

function ap_handle_product_upload(?array $file): ?string
{
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }
    $tmp = $file['tmp_name'] ?? null;
    if (!$tmp || !is_uploaded_file($tmp)) {
        return null;
    }
    $finfo = class_exists('finfo') ? new finfo(FILEINFO_MIME_TYPE) : null;
    $mime = $finfo ? $finfo->file($tmp) : null;
    $allowed = [
        'image/jpeg' => '.jpg',
        'image/png' => '.png',
        'image/webp' => '.webp',
    ];
    if (!$mime || !isset($allowed[$mime])) {
        ap_flash('Formato immagine non supportato. Usa JPG, PNG o WebP.', 'error');
        return null;
    }
    $targetDir = __DIR__ . '/../../uploads/products';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }
    $filename = date('YmdHis') . '-' . ap_random_token(8) . $allowed[$mime];
    $destination = $targetDir . '/' . $filename;
    if (!move_uploaded_file($tmp, $destination)) {
        ap_flash('Impossibile caricare l\'immagine.', 'error');
        return null;
    }
    return 'uploads/products/' . $filename;
}

function ap_handle_procurement_uploads(?array $files): array
{
    if (!$files) {
        return [];
    }
    $normalize = static function (array $payload): array {
        if (!isset($payload['name'])) {
            return [];
        }
        if (is_array($payload['name'])) {
            $list = [];
            $count = count($payload['name']);
            for ($i = 0; $i < $count; $i++) {
                $list[] = [
                    'name' => $payload['name'][$i] ?? null,
                    'type' => $payload['type'][$i] ?? null,
                    'tmp_name' => $payload['tmp_name'][$i] ?? null,
                    'error' => $payload['error'][$i] ?? null,
                ];
            }
            return $list;
        }
        return [$payload];
    };
    $entries = $normalize($files);
    if (!$entries) {
        return [];
    }
    $allowed = [
        'application/pdf' => '.pdf',
        'application/msword' => '.doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
        'application/zip' => '.zip',
        'application/x-zip-compressed' => '.zip',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '.xlsx',
        'application/vnd.ms-excel' => '.xls',
    ];
    $targetDir = __DIR__ . '/../../uploads/procurement';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }
    $stored = [];
    foreach ($entries as $entry) {
        if (($entry['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            continue;
        }
        $tmp = $entry['tmp_name'] ?? null;
        if (!$tmp || !is_uploaded_file($tmp)) {
            continue;
        }
        $finfo = class_exists('finfo') ? new finfo(FILEINFO_MIME_TYPE) : null;
        $mime = $finfo ? $finfo->file($tmp) : ($entry['type'] ?? null);
        $extension = $mime && isset($allowed[$mime]) ? $allowed[$mime] : null;
        if (!$extension) {
            $ext = strtolower((string) pathinfo((string) ($entry['name'] ?? ''), PATHINFO_EXTENSION));
            $fallback = ['pdf', 'doc', 'docx', 'zip', 'xlsx', 'xls'];
            if (in_array($ext, $fallback, true)) {
                $extension = '.' . $ext;
            }
        }
        if (!$extension) {
            ap_flash('Formato file non supportato per gli allegati procurement.', 'error');
            continue;
        }
        $filename = date('YmdHis') . '-' . ap_random_token(8) . $extension;
        $destination = $targetDir . '/' . $filename;
        if (!move_uploaded_file($tmp, $destination)) {
            ap_flash('Impossibile caricare gli allegati procurement.', 'error');
            continue;
        }
        $stored[] = [
            'path' => 'uploads/procurement/' . $filename,
            'name' => (string) ($entry['name'] ?? $filename),
            'mime' => $mime,
        ];
    }
    return $stored;
}

function ap_cancel_order_and_restore_stock(int $orderId): void
{
    $pdo = ap_db();
    $items = ap_fetch_order_items($orderId);
    foreach ($items as $item) {
        $stmt = $pdo->prepare('UPDATE products SET stock = stock + :qty WHERE id = :product');
        $stmt->execute([
            ':qty' => (int) $item['quantity'],
            ':product' => (int) $item['product_id'],
        ]);
    }
    $delete = $pdo->prepare('DELETE FROM orders WHERE id = :order');
    $delete->execute([':order' => $orderId]);
}

function ap_klarna_config(): array
{
    $appUrl = rtrim(ap_env('APP_URL', 'http://127.0.0.1:8000') ?? 'http://127.0.0.1:8000', '/');
    return [
        'base_url' => rtrim(ap_env('KLARNA_API_BASE_URL', 'https://api.playground.klarna.com') ?? 'https://api.playground.klarna.com', '/'),
        'username' => ap_env('KLARNA_API_USERNAME', ''),
        'password' => ap_env('KLARNA_API_PASSWORD', ''),
        'terms_url' => ap_env('KLARNA_TERMS_URL', $appUrl . '/termini') ?? ($appUrl . '/termini'),
        'success_url' => ap_env('KLARNA_CHECKOUT_SUCCESS_URL', $appUrl . '/?page=account&payment=success') ?? ($appUrl . '/?page=account&payment=success'),
        'cancel_url' => ap_env('KLARNA_CHECKOUT_CANCEL_URL', $appUrl . '/?page=cart&payment=cancelled') ?? ($appUrl . '/?page=cart&payment=cancelled'),
        'notification_url' => ap_env('KLARNA_NOTIFICATION_URL', $appUrl . '/webhooks/klarna') ?? ($appUrl . '/webhooks/klarna'),
        'checkout_url' => $appUrl . '/?page=cart',
    ];
}

function ap_klarna_create_session(int $orderId, array $items, array $context): array
{
    if (!function_exists('curl_init')) {
        throw new RuntimeException('Estensione cURL non disponibile sul server.');
    }
    $config = ap_klarna_config();
    if (empty($config['username']) || empty($config['password'])) {
        throw new RuntimeException('Credenziali API Klarna mancanti.');
    }
    $orderLines = ap_klarna_order_lines(
        $items,
        (int) ($context['shipping_cost_cents'] ?? 0),
        (int) ($context['discount_cents'] ?? 0)
    );
    $address = ap_klarna_address_payload($context);
    $payload = array_filter([
        'purchase_country' => strtoupper($context['shipping_country'] ?? 'IT'),
        'purchase_currency' => 'EUR',
        'locale' => 'it-IT',
        'order_amount' => (int) ($context['total_cents'] ?? 0),
        'order_tax_amount' => 0,
        'order_lines' => $orderLines,
        'merchant_reference1' => (string) $orderId,
        'merchant_urls' => [
            'success' => $config['success_url'],
            'cancel' => $config['cancel_url'],
            'failure' => $config['cancel_url'],
            'back' => $config['checkout_url'],
            'notification' => $config['notification_url'],
            'terms' => $config['terms_url'],
        ],
        'billing_address' => $address,
        'shipping_address' => $address,
        'customer' => array_filter([
            'type' => 'person',
            'email' => $context['customer_email'] ?? null,
            'phone' => $context['contact_phone'] ?? null,
        ]),
    ]);

    $url = $config['base_url'] . '/hpp/v1/sessions';
    $ch = curl_init($url);
    $body = json_encode($payload, JSON_UNESCAPED_UNICODE);
    if ($body === false) {
        throw new RuntimeException('Impossibile serializzare i dati per Klarna.');
    }
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_USERPWD => $config['username'] . ':' . $config['password'],
        CURLOPT_POSTFIELDS => $body,
    ]);
    $response = curl_exec($ch);
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new RuntimeException('Errore di connessione verso Klarna: ' . $error);
    }
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $decoded = json_decode($response, true);
    if ($status >= 400 || !is_array($decoded)) {
        throw new RuntimeException('Risposta Klarna non valida (HTTP ' . $status . ').');
    }
    if (empty($decoded['session_url'])) {
        throw new RuntimeException('Klarna non ha restituito una sessione valida.');
    }
    $decoded['request'] = $payload;
    return $decoded;
}

function ap_klarna_order_lines(array $items, int $shippingCost, int $discountCents = 0): array
{
    $lines = [];
    foreach ($items as $item) {
        $lines[] = [
            'type' => 'physical',
            'reference' => (string) $item['product_id'],
            'name' => substr($item['name'], 0, 255),
            'quantity' => (int) $item['quantity'],
            'unit_price' => (int) $item['price_cents'],
            'total_amount' => (int) $item['subtotal_cents'],
            'total_tax_amount' => 0,
            'tax_rate' => 0,
        ];
    }
    if ($shippingCost > 0) {
        $lines[] = [
            'type' => 'shipping_fee',
            'reference' => 'SHIPPING',
            'name' => 'Spedizione',
            'quantity' => 1,
            'unit_price' => $shippingCost,
            'total_amount' => $shippingCost,
            'total_tax_amount' => 0,
            'tax_rate' => 0,
        ];
    }
    if ($discountCents > 0) {
        $lines[] = [
            'type' => 'discount',
            'reference' => 'DISCOUNT',
            'name' => 'Sconto applicato',
            'quantity' => 1,
            'unit_price' => -$discountCents,
            'total_amount' => -$discountCents,
            'total_tax_amount' => 0,
            'tax_rate' => 0,
        ];
    }
    return $lines;
}

function ap_klarna_address_payload(array $context): array
{
    $name = trim($context['shipping_name'] ?? 'Cliente Klarna');
    $parts = preg_split('/\s+/', $name, 2) ?: [];
    $given = $parts[0] ?? $name;
    $family = $parts[1] ?? $parts[0] ?? $name;
    return array_filter([
        'given_name' => $given,
        'family_name' => $family,
        'street_address' => $context['shipping_street'] ?? ($context['shipping_address'] ?? ''),
        'postal_code' => $context['shipping_postal_code'] ?? '',
        'city' => $context['shipping_city'] ?? '',
        'region' => $context['shipping_province'] ?? '',
        'country' => strtoupper($context['shipping_country'] ?? 'IT'),
        'email' => $context['customer_email'] ?? null,
        'phone' => $context['contact_phone'] ?? null,
    ]);
}

function ap_klarna_onsite_client_id(): string
{
    $clientId = ap_env('KLARNA_WEB_SDK_CLIENT_ID', '') ?? '';
    if ($clientId !== '') {
        return $clientId;
    }
    return ap_env('KLARNA_ONSITE_CLIENT_ID', '') ?? '';
}

function ap_klarna_messaging_enabled(): bool
{
    $enabled = filter_var(ap_env('KLARNA_ONSITE_ENABLED', 'false') ?? 'false', FILTER_VALIDATE_BOOLEAN);
    $clientId = ap_klarna_onsite_client_id();
    return $enabled && $clientId !== '';
}

function ap_klarna_messaging_placement(string $area): string
{
    $map = [
        'product' => ap_env('KLARNA_ONSITE_PLACEMENT_PRODUCT', 'credit-promotion-badge') ?? 'credit-promotion-badge',
        'cart' => ap_env('KLARNA_ONSITE_PLACEMENT_CART', 'credit-promotion-checkout') ?? 'credit-promotion-checkout',
    ];
    return $map[$area] ?? $map['product'];
}

function ap_render_klarna_messaging(?int $amountCents, ?string $placement = null, array $extraData = []): string
{
    if (!ap_klarna_messaging_enabled()) {
        return '';
    }
    $clientId = ap_klarna_onsite_client_id();
    if ($clientId === '') {
        return '';
    }
    $theme = ap_env('KLARNA_ONSITE_THEME', 'default') ?? 'default';
    $locale = ap_env('KLARNA_ONSITE_LOCALE', 'it-IT') ?? 'it-IT';
    $placement = $placement ?: ap_klarna_messaging_placement('product');
    $attributes = array_merge([
        'class' => 'klarna-placement',
        'data-key' => $placement,
        'data-locale' => $locale,
        'data-theme' => $theme,
        'data-purchase-amount' => $amountCents !== null ? (string) (int) $amountCents : null,
    ], $extraData);
    if ($attributes['data-purchase-amount'] === null) {
        unset($attributes['data-purchase-amount']);
    }
    $chunks = [];
    foreach ($attributes as $key => $value) {
        if ($value === null || $value === '') {
            continue;
        }
        $chunks[] = sprintf('%s="%s"', $key, htmlspecialchars((string) $value, ENT_QUOTES));
    }
    if (!$chunks) {
        return '';
    }
    return '<div ' . implode(' ', $chunks) . '></div>';
}

function ap_fetch_announcements(array $options = []): array
{
    $useCache = empty($options['disable_cache']);
    $normalized = [
        'include_inactive' => !empty($options['include_inactive']),
        'limit' => isset($options['limit']) ? max(1, (int) $options['limit']) : null,
    ];
    $cacheKey = $useCache ? 'announcements_' . md5(serialize($normalized)) : null;
    if ($useCache && $cacheKey) {
        $cached = ap_cache_get($cacheKey, 180);
        if ($cached !== null) {
            return $cached;
        }
    }
    $pdo = ap_db();
    $includeInactive = $normalized['include_inactive'];
    $limit = $normalized['limit'];
    $sql = 'SELECT * FROM announcements';
    $params = [];
    if (!$includeInactive) {
        $sql .= ' WHERE is_active = 1';
    }
    $sql .= ' ORDER BY created_at DESC';
    if ($limit) {
        $sql .= ' LIMIT ' . $limit;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    if ($useCache && $cacheKey) {
        ap_cache_set($cacheKey, $results, 180);
    }
    return $results;
}

function ap_active_announcements(): array
{
    return ap_fetch_announcements(['include_inactive' => false]);
}

function ap_find_announcement(int $id): ?array
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT * FROM announcements WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function ap_save_announcement(array $data, ?int $id = null): int
{
    $pdo = ap_db();
    $payload = [
        ':message' => $data['message'],
        ':active' => !empty($data['is_active']) ? 1 : 0,
    ];
    if ($id) {
        $stmt = $pdo->prepare('UPDATE announcements SET message = :message, is_active = :active WHERE id = :id');
        $payload[':id'] = $id;
        $stmt->execute($payload);
        ap_cache_forget_prefix('announcements_');
        return $id;
    }
    $stmt = $pdo->prepare('INSERT INTO announcements (message, is_active) VALUES (:message, :active)');
    $stmt->execute($payload);
    ap_cache_forget_prefix('announcements_');
    return (int) $pdo->lastInsertId();
}

function ap_delete_announcement(int $id): bool
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('DELETE FROM announcements WHERE id = :id');
    $result = $stmt->execute([':id' => $id]);
    if ($result) {
        ap_cache_forget_prefix('announcements_');
    }
    return $result;
}

function ap_format_announcement_message(string $message): string
{
    $allowedTags = ['strong', 'em', 'b', 'i', 'span'];
    $allowedAttributes = ['class', 'aria-hidden', 'data-icon', 'title'];
    $safe = strip_tags($message, '<strong><em><b><i><span>');
    $safe = preg_replace_callback('/<([a-z0-9]+)([^>]*)>/i', static function ($matches) use ($allowedTags, $allowedAttributes) {
        $tag = strtolower($matches[1]);
        if (!in_array($tag, $allowedTags, true)) {
            return $matches[0];
        }
        $attrs = trim($matches[2] ?? '');
        if ($attrs === '') {
            return '<' . $tag . '>';
        }
        preg_match_all('/([a-z0-9:-]+)\s*=\s*("|")(.*?)\2/i', $attrs, $attrMatches, PREG_SET_ORDER);
        $kept = [];
        foreach ($attrMatches as $attr) {
            $name = strtolower($attr[1]);
            if (in_array($name, $allowedAttributes, true)) {
                $value = htmlspecialchars($attr[3], ENT_QUOTES);
                $kept[] = sprintf('%s="%s"', $name, $value);
            }
        }
        $attrString = $kept ? ' ' . implode(' ', $kept) : '';
        return '<' . $tag . $attrString . '>';
    }, $safe);
    return $safe;
}

function ap_fetch_users(array $filters = []): array
{
    $pdo = ap_db();
    $sql = 'SELECT id, name, email, role, is_active, created_at FROM users';
    $params = [];
    $where = [];
    if (!empty($filters['role'])) {
        $where[] = 'role = :role';
        $params[':role'] = $filters['role'];
    }
    if (isset($filters['is_active'])) {
        $where[] = 'is_active = :active';
        $params[':active'] = $filters['is_active'] ? 1 : 0;
    }
    if (!empty($filters['search'])) {
        $where[] = '(name LIKE :search OR email LIKE :search)';
        $params[':search'] = '%' . $filters['search'] . '%';
    }
    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY created_at DESC';
    if (!empty($filters['limit'])) {
        $sql .= ' LIMIT ' . (int) $filters['limit'];
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function ap_count_users(array $filters = []): int
{
    $pdo = ap_db();
    $sql = 'SELECT COUNT(*) FROM users';
    $params = [];
    $where = [];
    if (!empty($filters['role'])) {
        $where[] = 'role = :role';
        $params[':role'] = $filters['role'];
    }
    if (isset($filters['is_active'])) {
        $where[] = 'is_active = :active';
        $params[':active'] = $filters['is_active'] ? 1 : 0;
    }
    if (!empty($filters['search'])) {
        $where[] = '(name LIKE :search OR email LIKE :search)';
        $params[':search'] = '%' . $filters['search'] . '%';
    }
    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

function ap_find_user(int $id): ?array
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT id, name, email, role, is_active, created_at FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function ap_save_user(array $data, ?int $id = null): int
{
    $pdo = ap_db();
    $payload = [
        ':name' => $data['name'],
        ':email' => $data['email'],
        ':role' => $data['role'] ?? 'customer',
        ':active' => !empty($data['is_active']) ? 1 : 0,
    ];
    if ($id) {
        $sql = 'UPDATE users SET name = :name, email = :email, role = :role, is_active = :active';
        if (!empty($data['password_hash'])) {
            $sql .= ', password_hash = :password';
            $payload[':password'] = $data['password_hash'];
        }
        $sql .= ' WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $payload[':id'] = $id;
        $stmt->execute($payload);
        return $id;
    }
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role, is_active) VALUES (:name, :email, :password, :role, :active)');
    $payload[':password'] = $data['password_hash'] ?? password_hash('TempPass123!', PASSWORD_BCRYPT);
    $stmt->execute($payload);
    return (int) $pdo->lastInsertId();
}

function ap_delete_user(int $id): bool
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
    return $stmt->execute([':id' => $id]);
}

function ap_get_setting(string $key, $default = null)
{
    static $cache = [];
    if (isset($cache[$key])) {
        return $cache[$key];
    }
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT setting_value, setting_type FROM settings WHERE setting_key = :key LIMIT 1');
    $stmt->execute([':key' => $key]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        $cache[$key] = $default;
        return $default;
    }
    $value = $row['setting_value'];
    $type = $row['setting_type'];
    switch ($type) {
        case 'int':
            $parsed = (int) $value;
            break;
        case 'bool':
            $parsed = $value === '1' || strtolower($value) === 'true';
            break;
        case 'json':
            $parsed = json_decode($value, true);
            break;
        default:
            $parsed = $value;
    }
    $cache[$key] = $parsed;
    return $parsed;
}

function ap_set_setting(string $key, $value, string $type = 'string', ?string $description = null): void
{
    $pdo = ap_db();
    $stringValue = null;
    switch ($type) {
        case 'int':
            $stringValue = (string) (int) $value;
            break;
        case 'bool':
            $stringValue = $value ? '1' : '0';
            break;
        case 'json':
            $stringValue = json_encode($value, JSON_UNESCAPED_UNICODE);
            break;
        default:
            $stringValue = (string) $value;
    }
    $stmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES (:key, :value, :type, :desc) ON DUPLICATE KEY UPDATE setting_value = :value, setting_type = :type, description = :desc');
    $stmt->execute([
        ':key' => $key,
        ':value' => $stringValue,
        ':type' => $type,
        ':desc' => $description,
    ]);
    // Clear cache
    static $cache = [];
    unset($cache[$key]);
}

function ap_fetch_settings(): array
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT * FROM settings ORDER BY setting_key ASC');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function ap_get_advanced_stats(): array
{
    $pdo = ap_db();
    
    // Revenue by month (last 12 months)
    $revenueByMonth = [];
    $stmt = $pdo->prepare("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            SUM(total_cents) as revenue,
            COUNT(*) as orders
        FROM orders 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
    ");
    $stmt->execute();
    $monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($monthlyData as $row) {
        $revenueByMonth[$row['month']] = [
            'revenue' => (int) $row['revenue'],
            'orders' => (int) $row['orders']
        ];
    }
    
    // Top products
    $topProducts = [];
    $stmt = $pdo->prepare("
        SELECT 
            p.name,
            SUM(oi.quantity) as total_sold,
            SUM(oi.price_cents * oi.quantity) as total_revenue
        FROM order_items oi
        JOIN products p ON p.id = oi.product_id
        JOIN orders o ON o.id = oi.order_id
        WHERE o.status IN ('processing', 'completed')
        GROUP BY p.id, p.name
        ORDER BY total_sold DESC
        LIMIT 10
    ");
    $stmt->execute();
    $topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Customer stats
    $totalCustomers = (int) ($pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn() ?? 0);
    $activeCustomers = (int) ($pdo->query("SELECT COUNT(DISTINCT user_id) FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn() ?? 0);
    
    // Order status distribution
    $orderStatuses = [];
    $stmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
    $stmt->execute();
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $orderStatuses[$row['status']] = (int) $row['count'];
    }
    
    // Average order value by month
    $avgOrderValue = [];
    foreach ($monthlyData as $row) {
        $avgOrderValue[$row['month']] = $row['orders'] > 0 ? (int) ($row['revenue'] / $row['orders']) : 0;
    }
    
    // Conversion rate (orders / sessions - approximate)
    $totalSessions = (int) ($pdo->query("SELECT COUNT(*) FROM abandoned_carts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn() ?? 0);
    $totalOrders30d = (int) ($pdo->query("SELECT COUNT(*) FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn() ?? 0);
    $conversionRate = $totalSessions > 0 ? round(($totalOrders30d / $totalSessions) * 100, 2) : 0;
    
    return [
        'revenue_by_month' => $revenueByMonth,
        'top_products' => $topProducts,
        'total_customers' => $totalCustomers,
        'active_customers' => $activeCustomers,
        'order_statuses' => $orderStatuses,
        'avg_order_value' => $avgOrderValue,
        'conversion_rate' => $conversionRate,
        'total_sessions_30d' => $totalSessions,
        'total_orders_30d' => $totalOrders30d,
    ];
}

function ap_get_security_logs(int $limit = 50, int $offset = 0): array
{
    $pdo = ap_db();

    // Get security logs with user info
    $stmt = $pdo->prepare("
        SELECT
            sl.*,
            u.name as user_name,
            u.email as user_email
        FROM security_logs sl
        LEFT JOIN users u ON u.id = sl.user_id
        ORDER BY sl.created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count
    $totalStmt = $pdo->query("SELECT COUNT(*) FROM security_logs");
    $total = (int) $totalStmt->fetchColumn();

    return [
        'logs' => $logs,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ];
}

function ap_log_security_event(string $event_type, string $description, ?int $user_id = null, array $metadata = []): void
{
    $pdo = ap_db();
    
    $stmt = $pdo->prepare("
        INSERT INTO security_logs (event_type, description, user_id, metadata, ip_address, user_agent, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $stmt->execute([
        $event_type,
        $description,
        $user_id,
        json_encode($metadata),
        $ip,
        $userAgent
    ]);
}

function ap_get_audit_logs(array $filters = [], int $limit = 50, int $offset = 0): array
{
    $pdo = ap_db();

    $conditions = [];
    $params = [];

    if (!empty($filters['event_type'])) {
        $conditions[] = 'al.event_type = :event_type';
        $params[':event_type'] = $filters['event_type'];
    }

    if (!empty($filters['user_id'])) {
        $conditions[] = 'al.user_id = :user_id';
        $params[':user_id'] = $filters['user_id'];
    }

    if (!empty($filters['search'])) {
        $conditions[] = '(al.description LIKE :search OR u.name LIKE :search OR u.email LIKE :search)';
        $params[':search'] = '%' . $filters['search'] . '%';
    }

    if (!empty($filters['date_from'])) {
        $conditions[] = 'al.created_at >= :date_from';
        $params[':date_from'] = $filters['date_from'];
    }

    if (!empty($filters['date_to'])) {
        $conditions[] = 'al.created_at <= :date_to';
        $params[':date_to'] = $filters['date_to'];
    }

    $sql = '
        SELECT
            al.*,
            u.name as user_name,
            u.email as user_email
        FROM audit_logs al
        LEFT JOIN users u ON u.id = al.user_id
    ';

    if ($conditions) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY al.created_at DESC LIMIT :limit OFFSET :offset';

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $logs;
}

function ap_count_audit_logs(array $filters = []): int
{
    $pdo = ap_db();

    $conditions = [];
    $params = [];

    if (!empty($filters['event_type'])) {
        $conditions[] = 'event_type = :event_type';
        $params[':event_type'] = $filters['event_type'];
    }

    if (!empty($filters['user_id'])) {
        $conditions[] = 'user_id = :user_id';
        $params[':user_id'] = $filters['user_id'];
    }

    if (!empty($filters['search'])) {
        $conditions[] = '(description LIKE :search)';
        $params[':search'] = '%' . $filters['search'] . '%';
    }

    if (!empty($filters['date_from'])) {
        $conditions[] = 'created_at >= :date_from';
        $params[':date_from'] = $filters['date_from'];
    }

    if (!empty($filters['date_to'])) {
        $conditions[] = 'created_at <= :date_to';
        $params[':date_to'] = $filters['date_to'];
    }

    $sql = 'SELECT COUNT(*) FROM audit_logs';

    if ($conditions) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

function ap_log_audit_event(string $event_type, string $description, ?int $user_id = null, array $metadata = []): void
{
    $pdo = ap_db();

    $stmt = $pdo->prepare("
        INSERT INTO audit_logs (event_type, description, user_id, metadata, ip_address, user_agent, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");

    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

    $stmt->execute([
        $event_type,
        $description,
        $user_id,
        json_encode($metadata, JSON_UNESCAPED_UNICODE),
        $ip,
        $userAgent
    ]);
}
