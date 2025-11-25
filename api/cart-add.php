<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/cart.php';
require_once __DIR__ . '/../includes/ecommerce/helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$productId = (int) ($input['product_id'] ?? 0);
$quantity = (int) ($input['quantity'] ?? 1);

if ($productId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product ID']);
    exit;
}

$product = ap_find_product($productId);
if (!$product) {
    http_response_code(404);
    echo json_encode(['error' => 'Product not found']);
    exit;
}

ap_cart_add($productId, $quantity);

echo json_encode([
    'success' => true,
    'message' => 'Product added to cart',
    'cart_count' => ap_cart_count(),
    'cart_total_cents' => ap_cart_total_cents()
]);
?>