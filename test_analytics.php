<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/ecommerce/helpers.php';

echo "Testing analytics functions...\n\n";

try {
    echo "Testing ap_get_analytics_summary():\n";
    $summary = ap_get_analytics_summary();
    echo "Summary data:\n";
    print_r($summary);
    echo "\n";

    echo "Testing ap_get_conversion_funnel():\n";
    $funnel = ap_get_conversion_funnel();
    echo "Funnel data:\n";
    print_r($funnel);
    echo "\n";

    echo "All tests passed!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>