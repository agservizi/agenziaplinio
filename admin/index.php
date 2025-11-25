<?php
session_start();

require __DIR__ . '/../includes/env.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/faqs.php';
require __DIR__ . '/../includes/ecommerce/notifications.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/cart.php';
require __DIR__ . '/../includes/ecommerce/actions.php';

ap_process_abandoned_carts();

$config = require __DIR__ . '/../includes/config.php';
$site = $config['site'];
$meta = [
    'title' => 'Console Admin | ' . ($site['name'] ?? 'Agenzia Plinio'),
    'description' => 'Gestione operativa ecommerce Agenzia Plinio'
];

$flashMessages = $_SESSION['ap_flash'] ?? [];
unset($_SESSION['ap_flash']);
$currentUser = ap_auth_current_user();

if (!ap_auth_is_admin()) {
    header('Location: /?page=account');
    exit;
}

$pageKey = 'admin';
$adminBasePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php')), '/');
if ($adminBasePath === '' || $adminBasePath === '.') {
    $adminBasePath = '/admin';
}
if (str_ends_with($adminBasePath, '/') === false) {
    $adminBasePath .= '/';
}

ob_start();
require __DIR__ . '/../sections/admin.php';
$pageContent = ob_get_clean();

require __DIR__ . '/layout.php';
