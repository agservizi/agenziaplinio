<?php
// Debug script for staging server
echo "=== DEBUG STAGING SERVER ===\n\n";

// PHP Version
echo "PHP Version: " . PHP_VERSION . "\n";

// Server variables
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'NOT SET') . "\n";

// Check if files exist
$files_to_check = [
    '/admin/index.php',
    '/includes/config.php',
    '/includes/database.php',
    '/includes/auth.php'
];

echo "\n=== FILE CHECKS ===\n";
foreach ($files_to_check as $file) {
    $full_path = $_SERVER['DOCUMENT_ROOT'] . $file;
    echo "$file: " . (file_exists($full_path) ? "EXISTS" : "MISSING") . "\n";
}

// Test database connection
echo "\n=== DATABASE TEST ===\n";
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/database.php';

    $pdo = ap_db();
    $stmt = $pdo->query("SELECT 1");
    $result = $stmt->fetch();

    echo "Database connection: SUCCESS\n";
    echo "Test query result: " . ($result ? "OK" : "FAILED") . "\n";

} catch (Exception $e) {
    echo "Database connection: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
}

// Test auth functions
echo "\n=== AUTH TEST ===\n";
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
    echo "Auth functions: LOADED\n";
    echo "ap_auth_is_admin function: " . (function_exists('ap_auth_is_admin') ? "EXISTS" : "MISSING") . "\n";
} catch (Exception $e) {
    echo "Auth functions: FAILED TO LOAD\n";
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== END DEBUG ===\n";
?>