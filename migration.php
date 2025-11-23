<?php
require __DIR__ . '/includes/env.php';
require __DIR__ . '/includes/database.php';

// This will trigger the schema bootstrap, including any new ALTER TABLE statements
$pdo = ap_db();
echo "Migration completed successfully.\n";
?>