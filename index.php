<?php
session_start();

require __DIR__ . '/includes/env.php';
require __DIR__ . '/includes/database.php';
require __DIR__ . '/includes/faqs.php';
require __DIR__ . '/includes/ecommerce/helpers.php';
require __DIR__ . '/includes/ecommerce/notifications.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/cart.php';
require __DIR__ . '/includes/ecommerce/actions.php';

ap_process_abandoned_carts();

require __DIR__ . '/includes/form-handler.php';
$formResponse = handleContactForm();

require __DIR__ . '/includes/router.php';

$formToken = $_SESSION['ap_form_token'] ?? bin2hex(random_bytes(16));
$formFeedback = $_SESSION['ap_form_feedback'] ?? null;
unset($_SESSION['ap_form_feedback']);
$flashMessages = $_SESSION['ap_flash'] ?? [];
unset($_SESSION['ap_flash']);
$currentUser = ap_auth_current_user();
$cartCount = ap_cart_count();
$cartItems = ap_cart_items();
$cartSubtotal = ap_cart_total_cents();
$cartDiscount = ap_cart_discount_cents($cartSubtotal);
$cartTotal = max(0, $cartSubtotal - $cartDiscount);
$newsTickerItems = ap_active_announcements();

ob_start();
foreach ($pageSections as $section) {
    $sectionPath = __DIR__ . '/sections/' . $section . '.php';
    if (file_exists($sectionPath)) {
        include $sectionPath;
    }
}
$pageContent = ob_get_clean();

include __DIR__ . '/layout.php';
