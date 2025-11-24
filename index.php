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

// Global error handler for audit logging
function ap_error_handler($errno, $errstr, $errfile, $errline) {
    // Only log errors, not warnings/notices unless in production
    if (!(error_reporting() & $errno)) {
        return false;
    }

    $errorTypes = [
        E_ERROR => 'php_error',
        E_WARNING => 'php_warning',
        E_PARSE => 'php_error',
        E_NOTICE => 'php_notice',
        E_CORE_ERROR => 'php_error',
        E_CORE_WARNING => 'php_warning',
        E_COMPILE_ERROR => 'php_error',
        E_COMPILE_WARNING => 'php_warning',
        E_USER_ERROR => 'php_error',
        E_USER_WARNING => 'php_warning',
        E_USER_NOTICE => 'php_notice',
        E_STRICT => 'php_notice',
        E_RECOVERABLE_ERROR => 'php_error',
        E_DEPRECATED => 'php_warning',
        E_USER_DEPRECATED => 'php_warning',
    ];

    $eventType = $errorTypes[$errno] ?? 'php_error';
    $severity = match($errno) {
        E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR => 'error',
        E_WARNING, E_CORE_WARNING, E_COMPILE_WARNING, E_USER_WARNING, E_DEPRECATED, E_USER_DEPRECATED => 'warning',
        default => 'info'
    };

    try {
        ap_log_audit_event($eventType, "Errore PHP: $errstr in $errfile line $errline", null, [
            'error_type' => $errno,
            'file' => $errfile,
            'line' => $errline,
            'message' => $errstr,
            'severity' => $severity,
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
        ]);
    } catch (Throwable $e) {
        // Prevent infinite loop if logging fails
        error_log("Failed to log PHP error: " . $e->getMessage());
    }

    // Don't prevent default error handling
    return false;
}

function ap_exception_handler($exception) {
    try {
        ap_log_audit_event('php_exception', 'Eccezione PHP non gestita: ' . $exception->getMessage(), null, [
            'exception_class' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'severity' => 'critical'
        ]);
    } catch (Throwable $e) {
        error_log("Failed to log exception: " . $e->getMessage());
    }

    // Show generic error page
    http_response_code(500);
    echo "<h1>Errore interno del server</h1><p>Si è verificato un errore imprevisto. Riprova più tardi.</p>";
    exit;
}

// Set error and exception handlers
set_error_handler('ap_error_handler');
set_exception_handler('ap_exception_handler');

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
