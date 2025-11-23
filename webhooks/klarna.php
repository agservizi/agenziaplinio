<?php
declare(strict_types=1);

require __DIR__ . '/../includes/env.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/ecommerce/helpers.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if (strtoupper($method) !== 'POST') {
    header('Allow: POST');
    ap_klarna_webhook_response(405, ['error' => 'Metodo non consentito']);
}

$expectedToken = trim((string) (ap_env('KLARNA_WEBHOOK_TOKEN', '') ?? ''));
if ($expectedToken !== '') {
    $providedToken = trim((string) ($_SERVER['HTTP_X_KLARNA_WEBHOOK_TOKEN'] ?? ''));
    if ($providedToken === '' || !hash_equals($expectedToken, $providedToken)) {
        ap_klarna_webhook_response(401, ['error' => 'Token webhook non valido']);
    }
}

$rawPayload = file_get_contents('php://input');
$data = json_decode((string) $rawPayload, true);
if (!is_array($data)) {
    ap_klarna_webhook_response(400, ['error' => 'Payload JSON non valido']);
}

$orderId = null;
if (!empty($data['merchant_reference1'])) {
    $orderId = (int) $data['merchant_reference1'];
} elseif (!empty($data['merchant_reference'])) {
    $orderId = (int) $data['merchant_reference'];
}
if (!$orderId && !empty($data['session_id'])) {
    $orderId = ap_find_order_id_by_transaction_ref((string) $data['session_id']);
}
if (!$orderId && !empty($data['order_id'])) {
    $orderId = ap_find_order_id_by_transaction_ref((string) $data['order_id']);
}
if (!$orderId) {
    ap_klarna_webhook_response(202, ['ack' => true, 'note' => 'Ordine non individuato']);
}

$statusLabel = strtolower((string) ($data['status'] ?? $data['event_type'] ?? ''));
$successStates = ['completed', 'paid', 'captured', 'authorized', 'success'];
$paymentStatus = in_array($statusLabel, $successStates, true) ? 'paid' : 'pending';
$orderStatus = $paymentStatus === 'paid' ? 'processing' : 'pending';

$amountCents = (int) ($data['order_amount'] ?? $data['amount'] ?? 0);
$transactionRef = (string) ($data['order_id'] ?? $data['session_id'] ?? ('wh-' . uniqid('klarna', true)));

ap_record_payment($orderId, [
    'provider' => 'klarna',
    'status' => $paymentStatus,
    'payment_status' => $paymentStatus,
    'order_status' => $orderStatus,
    'amount_cents' => $amountCents,
    'transaction_ref' => $transactionRef,
    'meta' => [
        'webhook' => $data,
        'received_at' => date('c'),
    ],
]);

ap_klarna_webhook_response(200, ['ack' => true]);

function ap_klarna_webhook_response(int $status, array $body): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
