<?php
require_once 'includes/database.php';
require_once 'includes/env.php';
require_once 'includes/ecommerce/helpers.php';

echo 'Test della sezione Sicurezza...' . PHP_EOL;

try {
    $stats = ap_get_security_logs(100, 0);
    echo '✅ Funzione ap_get_security_logs funziona!' . PHP_EOL;
    echo 'Record trovati: ' . count($stats['logs']) . PHP_EOL;
    echo 'Totale: ' . $stats['total'] . PHP_EOL;
    
    if (count($stats['logs']) > 0) {
        echo 'Primo evento: ' . $stats['logs'][0]['event_type'] . ' - ' . $stats['logs'][0]['description'] . PHP_EOL;
    }
    
    echo '✅ Sezione Sicurezza pronta!' . PHP_EOL;
} catch (Exception $e) {
    echo '❌ Errore: ' . $e->getMessage() . PHP_EOL;
}
