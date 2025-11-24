<?php
require_once __DIR__ . '/includes/env.php';
ap_load_env(__DIR__ . '/.env');
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/auth.php';

$productId = 408;
$fields = ap_fetch_product_custom_fields($productId);
echo "Campi personalizzati per prodotto $productId:\n";
if (empty($fields)) {
    echo "Nessun campo trovato.\n";
} else {
    foreach ($fields as $field) {
        echo "- ID: {$field['id']}, Nome: {$field['field_name']}, Tipo: {$field['field_type']}, Etichetta: {$field['field_label']}\n";
    }
}

// Aggiorna il tipo
if (!empty($fields)) {
    $pdo = ap_db();
    $stmt = $pdo->prepare('UPDATE product_custom_fields SET field_type = :type WHERE id = :id');
    $stmt->execute([':type' => 'provincia', ':id' => $fields[0]['id']]);
    echo "Aggiornato tipo a 'provincia' per campo {$fields[0]['id']}\n";
}
?>