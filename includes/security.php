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
    header("Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=()");

    // Cross-Origin policies
    header('Cross-Origin-Embedder-Policy: require-corp');
    header('Cross-Origin-Opener-Policy: same-origin');
    header('Cross-Origin-Resource-Policy: same-origin');

    // HSTS (HTTP Strict Transport Security) - only if HTTPS
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }

    // CSP (Content Security Policy) - enhanced with granular controls
    $csp = "default-src 'self'; " .
           "script-src 'self' 'unsafe-inline' https://unpkg.com https://js.klarna.com https://www.google.com https://www.gstatic.com; " .
           "script-src-elem 'self' https://unpkg.com https://js.klarna.com https://www.google.com https://www.gstatic.com; " .
           "script-src-attr 'self' 'unsafe-inline'; " .
           "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://unpkg.com; " .
           "style-src-elem 'self' https://fonts.googleapis.com https://unpkg.com; " .
           "style-src-attr 'self' 'unsafe-inline'; " .
           "font-src 'self' https://fonts.gstatic.com; " .
           "img-src 'self' data: https: blob:; " .
           "connect-src 'self' https://*; " .
           "frame-src 'self' https://www.google.com https://www.youtube.com; " .
           "object-src 'none'; " .
           "base-uri 'self'; " .
           "form-action 'self'; " .
           "frame-ancestors 'self'; " .
           "upgrade-insecure-requests;";
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
    private static $ip_attempts = [];

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

    public static function check_ip($max_attempts = 10, $time_window = 600) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $now = time();

        if (!isset(self::$ip_attempts[$ip])) {
            self::$ip_attempts[$ip] = [];
        }

        // Remove old attempts outside the time window
        self::$ip_attempts[$ip] = array_filter(self::$ip_attempts[$ip], function($timestamp) use ($now, $time_window) {
            return ($now - $timestamp) < $time_window;
        });

        if (count(self::$ip_attempts[$ip]) >= $max_attempts) {
            // Log rate limit violation
            ap_log_security_event('rate_limit_exceeded', 'IP-based rate limit exceeded', null, [
                'ip' => $ip,
                'attempts' => count(self::$ip_attempts[$ip]),
                'max_attempts' => $max_attempts,
                'time_window' => $time_window
            ]);
            return false;
        }

        self::$ip_attempts[$ip][] = $now;
        return true;
    }

    public static function reset($key) {
        $key = md5($key);
        self::$attempts[$key] = [];
    }

    public static function reset_ip($ip = null) {
        if ($ip === null) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
        self::$ip_attempts[$ip] = [];
    }

    // Clean up old entries periodically to prevent memory leaks
    public static function cleanup() {
        $now = time();
        $max_age = 3600; // 1 hour

        foreach (self::$attempts as $key => $attempts) {
            self::$attempts[$key] = array_filter($attempts, function($timestamp) use ($now, $max_age) {
                return ($now - $timestamp) < $max_age;
            });
            if (empty(self::$attempts[$key])) {
                unset(self::$attempts[$key]);
            }
        }

        foreach (self::$ip_attempts as $ip => $attempts) {
            self::$ip_attempts[$ip] = array_filter($attempts, function($timestamp) use ($now, $max_age) {
                return ($now - $timestamp) < $max_age;
            });
            if (empty(self::$ip_attempts[$ip])) {
                unset(self::$ip_attempts[$ip]);
            }
        }
    }
}

// Input sanitization and validation
function ap_sanitize_input($input, $type = 'string') {
    if (is_array($input)) {
        return array_map(function($item) use ($type) {
            return ap_sanitize_input($item, $type);
        }, $input);
    }

    $input = trim($input);

    switch ($type) {
        case 'email':
            $sanitized = filter_var($input, FILTER_SANITIZE_EMAIL);
            return filter_var($sanitized, FILTER_VALIDATE_EMAIL) ? $sanitized : false;
        case 'url':
            $sanitized = filter_var($input, FILTER_SANITIZE_URL);
            return filter_var($sanitized, FILTER_VALIDATE_URL) ? $sanitized : false;
        case 'int':
            return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
        case 'float':
            return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        case 'html':
            return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        case 'sql':
            // Remove potential SQL injection characters
            return preg_replace('/[\'"\\\\;]/', '', $input);
        default:
            return filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
    }
}

// Advanced input validation
function ap_validate_input($input, $rules = []) {
    $errors = [];

    foreach ($rules as $field => $rule) {
        $value = $input[$field] ?? null;

        // Required check
        if (isset($rule['required']) && $rule['required'] && empty($value)) {
            $errors[$field] = 'Questo campo è obbligatorio';
            continue;
        }

        // Skip further validation if empty and not required
        if (empty($value) && !isset($rule['required'])) {
            continue;
        }

        // Type validation
        if (isset($rule['type'])) {
            switch ($rule['type']) {
                case 'email':
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field] = 'Email non valida';
                    }
                    break;
                case 'url':
                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        $errors[$field] = 'URL non valido';
                    }
                    break;
                case 'numeric':
                    if (!is_numeric($value)) {
                        $errors[$field] = 'Deve essere un numero';
                    }
                    break;
                case 'integer':
                    if (!filter_var($value, FILTER_VALIDATE_INT)) {
                        $errors[$field] = 'Deve essere un numero intero';
                    }
                    break;
            }
        }

        // Length validation
        if (isset($rule['min_length']) && strlen($value) < $rule['min_length']) {
            $errors[$field] = "Deve essere lungo almeno {$rule['min_length']} caratteri";
        }

        if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
            $errors[$field] = "Deve essere lungo al massimo {$rule['max_length']} caratteri";
        }

        // Pattern validation
        if (isset($rule['pattern']) && !preg_match($rule['pattern'], $value)) {
            $errors[$field] = $rule['pattern_message'] ?? 'Formato non valido';
        }

        // Custom validation
        if (isset($rule['custom']) && is_callable($rule['custom'])) {
            $custom_result = $rule['custom']($value);
            if ($custom_result !== true) {
                $errors[$field] = is_string($custom_result) ? $custom_result : 'Validazione fallita';
            }
        }
    }

    return empty($errors) ? true : $errors;
}

// Secure session management
function ap_secure_session_start() {
    if (session_status() === PHP_SESSION_NONE) {
        // Set secure session parameters
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 1 : 0);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.gc_maxlifetime', 3600); // 1 hour
        ini_set('session.cookie_lifetime', 0); // Session cookie

        session_start();

        // Session fingerprinting for additional security
        $current_fingerprint = ap_generate_session_fingerprint();
        if (isset($_SESSION['fingerprint'])) {
            if ($_SESSION['fingerprint'] !== $current_fingerprint) {
                // Fingerprint changed - potential session hijacking
                ap_log_security_event('session_hijacking_attempt', 'Session fingerprint mismatch', null, [
                    'old_fingerprint' => $_SESSION['fingerprint'],
                    'new_fingerprint' => $current_fingerprint,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                ]);
                session_destroy();
                session_start();
            }
        } else {
            $_SESSION['fingerprint'] = $current_fingerprint;
        }

        // IP-based session validation (log warning if IP changes)
        $current_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (isset($_SESSION['ip']) && $_SESSION['ip'] !== $current_ip) {
            ap_log_security_event('ip_change', 'Client IP address changed during session', null, [
                'old_ip' => $_SESSION['ip'],
                'new_ip' => $current_ip,
                'session_id' => session_id()
            ]);
        }
        $_SESSION['ip'] = $current_ip;

        // Regenerate session ID periodically
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 1800) { // 30 minutes
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
}

// Generate session fingerprint for hijacking detection
function ap_generate_session_fingerprint() {
    $components = [
        $_SERVER['HTTP_USER_AGENT'] ?? '',
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
        $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '',
        $_SERVER['REMOTE_ADDR'] ?? '',
        // Add more components as needed
    ];

    return hash('sha256', implode('|', $components));
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

// Security monitoring and alerting
function ap_security_monitor_request() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $request_uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
    $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';

    // Check for suspicious patterns
    $suspicious_patterns = [
        '/\.\./',  // Directory traversal
        '/<script/i',  // XSS attempts
        '/union.*select/i',  // SQL injection
        '/eval\(/i',  // Code injection
        '/base64_decode/i',  // Encoded attacks
    ];

    foreach ($suspicious_patterns as $pattern) {
        if (preg_match($pattern, $request_uri . $user_agent)) {
            ap_log_security_event('suspicious_request', 'Suspicious request pattern detected', null, [
                'pattern' => $pattern,
                'ip' => $ip,
                'user_agent' => $user_agent,
                'request_uri' => $request_uri,
                'method' => $method
            ]);
            break;
        }
    }

    // Rate limiting check
    if (!RateLimiter::check_ip()) {
        ap_log_security_event('rate_limit_blocked', 'Request blocked due to rate limiting', null, [
            'ip' => $ip,
            'request_uri' => $request_uri
        ]);
        http_response_code(429);
        die('Too Many Requests');
    }
}

// Enhanced error handling
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

    // Mask sensitive information in error messages
    $error_message = preg_replace('/(password|token|key|secret).*/i', '$1: [REDACTED]', $error_message);

    error_log($error_message, 3, __DIR__ . '/../storage/logs/security.log');

    // Don't show errors in production
    if (getenv('APP_ENV') === 'production') {
        return true; // Suppress error display
    }

    return false; // Show error in development
}

// Initialize security measures
function ap_init_security() {
    // Security monitoring
    ap_security_monitor_request();

    // Set error handler
    set_error_handler('ap_secure_error_handler');

    // Set exception handler
    set_exception_handler(function($e) {
        $error_msg = $e->getMessage();
        // Mask sensitive information
        $error_msg = preg_replace('/(password|token|key|secret).*/i', '$1: [REDACTED]', $error_msg);

        error_log('Uncaught Exception: ' . $error_msg, 3, __DIR__ . '/../storage/logs/security.log');

        ap_log_security_event('uncaught_exception', 'Uncaught exception occurred', null, [
            'message' => $error_msg,
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

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

    // Periodic cleanup of rate limiter
    if (mt_rand(1, 100) === 1) { // 1% chance per request
        RateLimiter::cleanup();
    }
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