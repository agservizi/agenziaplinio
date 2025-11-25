<?php
declare(strict_types=1);

// Test delle funzioni di sicurezza
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/security.php';

echo "=== Test Sicurezza Agenzia Plinio ===\n\n";

// Test generazione token CSRF
echo "1. Test generazione token CSRF:\n";
$token = ap_generate_csrf_token();
echo "Token generato: " . substr($token, 0, 20) . "...\n";
echo "Lunghezza token: " . strlen($token) . "\n\n";

// Test validazione input
echo "2. Test sanitizzazione input:\n";
$dirtyInput = "<script>alert('xss')</script>Hello <b>World</b>";
$sanitized = ap_sanitize_input($dirtyInput, 'html');
echo "Input originale: $dirtyInput\n";
echo "Input sanitizzato: $sanitized\n\n";

// Test hashing password
echo "3. Test hashing password:\n";
$password = "TestPassword123!";
$hash = ap_hash_password($password);
echo "Password hash generato (primi 50 caratteri): " . substr($hash, 0, 50) . "...\n";
echo "Verifica password: " . (ap_verify_password($password, $hash) ? "SUCCESSO" : "FALLITO") . "\n\n";

// Test rate limiting con classe RateLimiter
echo "4. Test rate limiting:\n";
$limiter = new RateLimiter();

// Simula alcuni tentativi di login
for ($i = 1; $i <= 7; $i++) {
    $allowed = $limiter->check('login_127.0.0.1', 5, 60); // 5 tentativi per minuto
    echo "Tentativo login $i: " . ($allowed ? "Consentito" : "Bloccato") . "\n";
    if (!$allowed) break;
}
echo "\n";

// Test protezione XSS
echo "5. Test protezione XSS:\n";
$xssInput = "<script>alert('xss')</script><img src=x onerror=alert('xss')>";
$protected = ap_xss_protect($xssInput);
echo "Input XSS: $xssInput\n";
echo "Input protetto: $protected\n\n";

// Test validazione token CSRF
echo "6. Test validazione token CSRF:\n";
$testToken = ap_generate_csrf_token();
$valid = ap_validate_csrf_token($testToken);
echo "Token valido: " . ($valid ? "SI" : "NO") . "\n";
$invalid = ap_validate_csrf_token("fake_token");
echo "Token falso valido: " . ($invalid ? "SI" : "NO") . "\n\n";

// Test generazione campo CSRF
echo "7. Test generazione campo CSRF:\n";
$csrfField = ap_csrf_token_field();
echo "Campo CSRF generato: $csrfField\n\n";

echo "=== Test completato con successo! ===\n";