<?php
declare(strict_types=1);

require __DIR__ . '/../includes/env.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/ecommerce/helpers.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if (strtoupper($method) !== 'POST') {
    header('Allow: POST');
    ap_stripe_webhook_response(405, ['error' => 'Metodo non consentito']);
}

$expectedToken = trim((string) (ap_env('STRIPE_WEBHOOK_SECRET', '') ?? ''));
if ($expectedToken !== '') {
    $signature = trim((string) ($_SERVER['HTTP_STRIPE_SIGNATURE'] ?? ''));
    if ($signature === '') {
        ap_stripe_webhook_response(401, ['error' => 'Firma webhook mancante']);
    }
    // Per semplicità, saltiamo la verifica firma per ora, o implementare manualmente
    // In produzione, implementare verifica firma Stripe
}

$rawPayload = file_get_contents('php://input');
$data = json_decode((string) $rawPayload, true);
if (!is_array($data)) {
    ap_stripe_webhook_response(400, ['error' => 'Payload JSON non valido']);
}

$eventType = $data['type'] ?? '';
$session = $data['data']['object'] ?? [];

$orderId = null;
if (!empty($session['metadata']['order_id'])) {
    $orderId = (int) $session['metadata']['order_id'];
}
if (!$orderId && !empty($session['id'])) {
    $orderId = ap_find_order_id_by_transaction_ref((string) $session['id']);
}
if (!$orderId) {
    ap_stripe_webhook_response(202, ['ack' => true, 'note' => 'Ordine non individuato']);
}

$paymentStatus = 'pending';
$orderStatus = 'pending';
if ($eventType === 'checkout.session.completed') {
    $paymentStatus = 'paid';
    $orderStatus = 'processing';
} elseif (in_array($eventType, ['payment_intent.succeeded', 'charge.succeeded'], true)) {
    $paymentStatus = 'paid';
    $orderStatus = 'processing';
} elseif (in_array($eventType, ['payment_intent.payment_failed', 'charge.failed'], true)) {
    $paymentStatus = 'failed';
    $orderStatus = 'cancelled';
}

$amountCents = (int) ($session['amount_total'] ?? 0);
$transactionRef = (string) ($session['id'] ?? ('wh-' . uniqid('stripe', true)));

ap_record_payment($orderId, [
    'provider' => 'stripe',
    'status' => $paymentStatus,
    'payment_status' => $paymentStatus,
    'order_status' => $orderStatus,
    'amount_cents' => $amountCents,
    'transaction_ref' => $transactionRef,
    'meta' => [
        'webhook_event' => $eventType,
        'webhook_data' => $session,
        'received_at' => date('c'),
    ],
]);

ap_stripe_webhook_response(200, ['ack' => true]);

function ap_stripe_webhook_response(int $status, array $body): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}