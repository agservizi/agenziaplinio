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
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['ap_auth_user_id'] = (int) $user['id'];
        $_SESSION['ap_auth_user'] = $user;
        return true;
    }
    return false;
}

function ap_auth_register(string $name, string $email, string $password): bool
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        return false;
    }
    $insert = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :password, :role)');
    return $insert->execute([
        ':name' => $name,
        ':email' => $email,
        ':password' => password_hash($password, PASSWORD_BCRYPT),
        ':role' => 'customer',
    ]);
}

function ap_auth_logout(): void
{
    unset($_SESSION['ap_auth_user_id'], $_SESSION['ap_auth_user']);
}

function ap_auth_is_admin(): bool
{
    $user = ap_auth_current_user();
    return $user ? $user['role'] === 'admin' : false;
}
