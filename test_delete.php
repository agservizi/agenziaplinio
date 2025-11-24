<?php
require_once 'includes/database.php';
require_once 'includes/env.php';
require_once 'includes/ecommerce/helpers.php';

// Simula un utente admin loggato (ID 1)
$_SESSION['ap_auth_user'] = [
    'id' => 1,
    'name' => 'Carmine Cavaliere',
    'email' => 'admin@agenziaplinio.it',
    'role' => 'admin'
];

// Simula POST data
$_POST['user_id'] = '3';

// Includi il file actions
require_once 'includes/ecommerce/actions.php';

// Chiama direttamente la funzione
echo 'Chiamo ap_action_admin_delete_user...' . PHP_EOL;
try {
    ap_action_admin_delete_user();
    echo 'Funzione chiamata con successo' . PHP_EOL;
    
    // Controlla se l'utente è stato eliminato
    $user = ap_find_user(3);
    if ($user) {
        echo 'ERRORE: Utente ancora esistente!' . PHP_EOL;
    } else {
        echo 'SUCCESSO: Utente eliminato!' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'ERRORE: ' . $e->getMessage() . PHP_EOL;
}
?>
