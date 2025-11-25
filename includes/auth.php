<?php
declare(strict_types=1);

require_once __DIR__ . '/ecommerce/helpers.php';

function ap_auth_current_user(): ?array
{
    if (!empty($_SESSION['ap_auth_user'])) {
        return $_SESSION['ap_auth_user'];
    }
    if (empty($_SESSION['ap_auth_user_id'])) {
        return null;
    }
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $_SESSION['ap_auth_user_id']]);
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION['ap_auth_user'] = $user;
        return $user;
    }
    unset($_SESSION['ap_auth_user_id']);
    return null;
}

function ap_auth_attempt(string $email, string $password): bool
{
    $client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    // Rate limiting - max 5 attempts per 15 minutes per IP
    if (!RateLimiter::check('login_' . $client_ip, 5, 900)) {
        ap_log_security_event('login_rate_limited', 'Login rate limit exceeded', null, [
            'email' => $email,
            'ip' => $client_ip
        ]);
        return false;
    }

    // Sanitize inputs
    $email = ap_sanitize_input($email, 'email');
    $password = ap_sanitize_input($password, 'html');

    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && ap_verify_password($password, $user['password_hash'])) {
        // Regenerate session ID for security
        session_regenerate_id(true);

        $_SESSION['ap_auth_user_id'] = (int) $user['id'];
        $_SESSION['ap_auth_user'] = $user;

        // Reset rate limiter on successful login
        RateLimiter::reset('login_' . $client_ip);

        // Log successful login
        ap_log_security_event('login_success', 'Accesso riuscito', (int) $user['id'], [
            'login_method' => 'password',
            'user_email' => $email,
            'ip' => $client_ip
        ]);
        return true;
    }

    // Log failed login attempt
    ap_log_security_event('login_failed', 'Tentativo di accesso fallito - credenziali errate', null, [
        'email' => $email,
        'reason' => 'invalid_credentials',
        'ip' => $client_ip
    ]);
    return false;
}

function ap_auth_register(string $name, string $email, string $password): bool
{
    // Sanitize inputs
    $name = ap_sanitize_input($name, 'html');
    $email = ap_sanitize_input($email, 'email');

    // Validate password strength
    if (strlen($password) < 8) {
        ap_log_security_event('registration_failed', 'Password troppo debole', null, [
            'email' => $email,
            'reason' => 'weak_password'
        ]);
        return false;
    }

    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        ap_log_security_event('registration_failed', 'Email già registrata', null, [
            'email' => $email,
            'reason' => 'email_exists'
        ]);
        return false;
    }

    $password_hash = ap_hash_password($password);

    $insert = $pdo->prepare('INSERT INTO users (name, email, password_hash, role, created_at) VALUES (:name, :email, :password, :role, NOW())');
    $result = $insert->execute([
        ':name' => $name,
        ':email' => $email,
        ':password' => $password_hash,
        ':role' => 'customer',
    ]);

    if ($result) {
        $userId = (int) $pdo->lastInsertId();
        // Log user registration
        ap_log_security_event('user_registered', 'Nuovo account utente creato', $userId, [
            'registration_method' => 'form',
            'user_email' => $email,
            'user_name' => $name
        ]);
    } else {
        ap_log_security_event('registration_failed', 'Errore durante la registrazione', null, [
            'email' => $email,
            'reason' => 'database_error'
        ]);
    }

    return $result;
}

function ap_auth_logout(): void
{
    $user = ap_auth_current_user();
    if ($user) {
        // Log logout
        ap_log_audit_event('logout', 'Disconnessione', (int) $user['id'], [
            'session_duration' => 'unknown' // Could be enhanced with session start time
        ]);
    }
    unset($_SESSION['ap_auth_user_id'], $_SESSION['ap_auth_user']);
}

function ap_auth_is_admin(): bool
{
    $user = ap_auth_current_user();
    return $user ? $user['role'] === 'admin' : false;
}
