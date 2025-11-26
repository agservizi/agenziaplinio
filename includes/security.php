<?php
/**
 * Security functions for Agenzia Plinio
 * Implements comprehensive security measures
 */

// Security headers
function ap_security_headers() {
    // Prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');

    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');

    // Referrer Policy
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Permissions Policy
    header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

    // HSTS (HTTP Strict Transport Security) - only if HTTPS
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }

    // CSP (Content Security Policy) - enhanced
    $csp = "default-src 'self'; " .
           "script-src 'self' 'unsafe-inline' https://unpkg.com https://js.klarna.com https://www.google.com https://www.gstatic.com; " .
           "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://unpkg.com; " .
           "font-src 'self' https://fonts.gstatic.com; " .
           "img-src 'self' data: https:; " .
           "connect-src 'self' https://*; " .
           "frame-src 'self' https://www.google.com https://www.youtube.com; " .
           "object-src 'none'; " .
           "base-uri 'self'; " .
           "form-action 'self';";
    header("Content-Security-Policy: $csp");
}

// CSRF Protection
function ap_generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function ap_validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function ap_csrf_token_field() {
    $token = ap_generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES) . '">';
}

// Rate limiting
class RateLimiter {
    private static $attempts = [];

    public static function check($key, $max_attempts = 5, $time_window = 300) {
        $now = time();
        $key = md5($key);

        if (!isset(self::$attempts[$key])) {
            self::$attempts[$key] = [];
        }

        // Remove old attempts outside the time window
        self::$attempts[$key] = array_filter(self::$attempts[$key], function($timestamp) use ($now, $time_window) {
            return ($now - $timestamp) < $time_window;
        });

        if (count(self::$attempts[$key]) >= $max_attempts) {
            return false; // Rate limit exceeded
        }

        self::$attempts[$key][] = $now;
        return true;
    }

    public static function reset($key) {
        $key = md5($key);
        self::$attempts[$key] = [];
    }
}

// Input sanitization
function ap_sanitize_input($input, $type = 'string') {
    if (is_array($input)) {
        return array_map(function($item) use ($type) {
            return ap_sanitize_input($item, $type);
        }, $input);
    }

    $input = trim($input);

    switch ($type) {
        case 'email':
            return filter_var($input, FILTER_SANITIZE_EMAIL);
        case 'url':
            return filter_var($input, FILTER_SANITIZE_URL);
        case 'int':
            return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
        case 'float':
            return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        case 'html':
            return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        default:
            return filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
    }
}

// Secure session management
function ap_secure_session_start() {
    if (session_status() === PHP_SESSION_NONE) {
        // Set secure session parameters
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 1 : 0);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_samesite', 'Strict');

        session_start();

        // Regenerate session ID periodically
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 1800) { // 30 minutes
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
}

// Password security
function ap_hash_password($password) {
    return password_hash($password, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 3
    ]);
}

function ap_verify_password($password, $hash) {
    return password_verify($password, $hash);
}

// File upload security
function ap_secure_file_upload($file, $allowed_types = [], $max_size = 5242880) { // 5MB default
    $errors = [];

    // Check if file was uploaded
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Errore nel caricamento del file';
        return ['success' => false, 'errors' => $errors];
    }

    // Check file size
    if ($file['size'] > $max_size) {
        $errors[] = 'File troppo grande';
        return ['success' => false, 'errors' => $errors];
    }

    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        $errors[] = 'Tipo di file non consentito';
        return ['success' => false, 'errors' => $errors];
    }

    // Generate secure filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $secure_name = bin2hex(random_bytes(16)) . '.' . $extension;

    return [
        'success' => true,
        'filename' => $secure_name,
        'original_name' => $file['name'],
        'mime_type' => $mime_type,
        'size' => $file['size']
    ];
}

// SQL injection protection (ensure prepared statements)
function ap_secure_query($query, $params = []) {
    global $pdo;
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt;
}

// Error handling for production
function ap_secure_error_handler($errno, $errstr, $errfile, $errline) {
    // Log error securely
    $error_message = sprintf(
        "[%s] Error %d: %s in %s on line %d",
        date('Y-m-d H:i:s'),
        $errno,
        $errstr,
        $errfile,
        $errline
    );

    error_log($error_message, 3, __DIR__ . '/../storage/logs/security.log');

    // Don't show errors in production
    if (getenv('APP_ENV') === 'production') {
        return true; // Suppress error display
    }

    return false; // Show error in development
}

// Initialize security measures
function ap_init_security() {
    // Set error handler
    set_error_handler('ap_secure_error_handler');

    // Set exception handler
    set_exception_handler(function($e) {
        error_log('Uncaught Exception: ' . $e->getMessage(), 3, __DIR__ . '/../storage/logs/security.log');
        if (getenv('APP_ENV') === 'production') {
            http_response_code(500);
            echo 'Si è verificato un errore interno. Riprova più tardi.';
            exit;
        }
        throw $e;
    });

    // Security headers
    ap_security_headers();

    // Secure session
    ap_secure_session_start();

    // Disable error display in production
    if (getenv('APP_ENV') === 'production') {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
    } else {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
    }

    error_reporting(E_ALL);
}

// Audit logging
function ap_log_security_event($event, $message, $user_id = null, $data = []) {
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'event' => $event,
        'message' => $message,
        'user_id' => $user_id,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'data' => json_encode($data)
    ];

    $log_line = json_encode($log_entry) . PHP_EOL;

    $log_file = __DIR__ . '/../storage/logs/security_audit.log';
    $log_dir = dirname($log_file);

    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    file_put_contents($log_file, $log_line, FILE_APPEND | LOCK_EX);
}

// XSS protection
function ap_xss_protect($data) {
    if (is_array($data)) {
        return array_map('ap_xss_protect', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// Initialize security on every request
ap_init_security();