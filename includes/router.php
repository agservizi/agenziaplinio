<?php
$config = require __DIR__ . '/config.php';
$pages = $config['pages'];
$site = $config['site'];

$pageKey = isset($_GET['page']) ? strtolower(trim($_GET['page'])) : 'home';
if (!array_key_exists($pageKey, $pages)) {
    // Log 404 error
    ap_log_audit_event('site_error', 'Errore 404: Pagina non trovata', null, [
        'error_code' => 404,
        'requested_url' => $_SERVER['REQUEST_URI'] ?? '/',
        'referrer' => $_SERVER['HTTP_REFERER'] ?? null,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);
    $pageKey = '404';
    http_response_code(404);
}

if ($pageKey === 'admin') {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $scriptDir = $scriptDir === '/' ? '' : rtrim($scriptDir, '/');
    $adminUrl = ($scriptDir === '' ? '' : $scriptDir) . '/admin/';
    if (!str_starts_with($adminUrl, '/')) {
        $adminUrl = '/' . $adminUrl;
    }
    header('Location: ' . $adminUrl, true, 302);
    exit;
}

$currentPage = $pages[$pageKey];
$pageTitle = $currentPage['title'] ?? $site['default_title'];
$pageDescription = $currentPage['description'] ?? $site['default_description'];
$pageSections = $currentPage['sections'] ?? [];
$productDetail = null;

// Track page view analytics
$user = ap_auth_current_user();
ap_track_event('page_view', [
    'page' => $pageKey,
    'title' => $pageTitle,
    'url' => $_SERVER['REQUEST_URI'] ?? '/',
    'referrer' => $_SERVER['HTTP_REFERER'] ?? null
], $user ? (int) $user['id'] : null);

if ($pageKey === 'download') {
    $orderId = (int) ($_GET['order_id'] ?? 0);
    $productId = (int) ($_GET['product_id'] ?? 0);
    $user = ap_auth_current_user();
    if (!$user || $orderId <= 0 || $productId <= 0) {
        http_response_code(403);
        exit('Accesso negato.');
    }
    $order = ap_fetch_order($orderId);
    if (!$order || (int) $order['user_id'] !== (int) $user['id']) {
        http_response_code(403);
        exit('Ordine non trovato.');
    }
    $item = ap_fetch_order_item($orderId, $productId);
    if (!$item || empty($item['digital_file_path'])) {
        http_response_code(404);
        exit('File non disponibile.');
    }
    $filePath = __DIR__ . '/../' . $item['digital_file_path'];
    if (!file_exists($filePath)) {
        http_response_code(404);
        exit('File non trovato.');
    }
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
}

if ($pageKey === 'product') {
    $slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
    if ($slug !== '') {
        $productDetail = ap_find_product_by_slug($slug);
    }
    if (!$productDetail && isset($_GET['id'])) {
        $productId = (int) $_GET['id'];
        if ($productId > 0) {
            $productDetail = ap_find_product($productId);
        }
    }
    if ($productDetail) {
        $pageTitle = $productDetail['name'] . ' | ' . ($site['name'] ?? 'Agenzia Plinio');
        $pageDescription = mb_substr(strip_tags((string) $productDetail['description']), 0, 155);
    }
}

$meta = [
    'title' => $pageTitle,
    'description' => $pageDescription,
    'og_image' => $site['og_image'],
];

if ($productDetail && !empty($productDetail['image_url'])) {
    $meta['og_image'] = $productDetail['image_url'];
}

$business = $site['business'];
$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'LocalBusiness',
    'name' => $business['name'],
    'image' => $site['og_image'],
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => $business['address']['street'],
        'addressLocality' => $business['address']['locality'],
        'postalCode' => $business['address']['postal_code'],
        'addressCountry' => $business['address']['country']
    ],
    'telephone' => $business['telephone'],
    'email' => $business['email'],
    'openingHours' => $business['opening_hours'],
    'url' => sprintf('https://%s%s', $_SERVER['HTTP_HOST'] ?? 'localhost', $_SERVER['REQUEST_URI'] ?? '/')
];
