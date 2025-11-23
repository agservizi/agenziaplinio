<?php
declare(strict_types=1);

require_once __DIR__ . '/ecommerce/helpers.php';

function ap_cart_items(): array
{
    $cart = $_SESSION['ap_cart'] ?? [];
    if (!$cart) {
        return [];
    }
    $items = [];
    foreach ($cart as $productId => $qty) {
        $product = ap_find_product((int) $productId);
        if (!$product) {
            continue;
        }
        $items[] = [
            'product_id' => (int) $product['id'],
            'name' => $product['name'],
            'quantity' => (int) $qty,
            'price_cents' => (int) $product['price_cents'],
            'subtotal_cents' => (int) $product['price_cents'] * (int) $qty,
            'image_url' => $product['image_url'] ?: 'assets/img/og-image.jpg',
        ];
    }
    return $items;
}

function ap_cart_count(): int
{
    $cart = $_SESSION['ap_cart'] ?? [];
    return array_sum($cart);
}

function ap_cart_total_cents(): int
{
    return array_sum(array_column(ap_cart_items(), 'subtotal_cents'));
}

function ap_cart_add(int $productId, int $quantity = 1): void
{
    if ($quantity < 1) {
        $quantity = 1;
    }
    $_SESSION['ap_cart'][$productId] = ($_SESSION['ap_cart'][$productId] ?? 0) + $quantity;
    ap_cart_track_abandonment();
}

function ap_cart_update(int $productId, int $quantity): void
{
    if ($quantity <= 0) {
        unset($_SESSION['ap_cart'][$productId]);
        ap_cart_track_abandonment();
        return;
    }
    $_SESSION['ap_cart'][$productId] = $quantity;
    ap_cart_track_abandonment();
}

function ap_cart_clear(): void
{
    unset($_SESSION['ap_cart']);
    ap_cart_clear_coupon();
    ap_abandoned_cart_clear_session();
}

function ap_cart_coupon(): ?array
{
    $snapshot = $_SESSION['ap_cart_coupon'] ?? null;
    if (!$snapshot) {
        return null;
    }
    $coupon = null;
    if (!empty($snapshot['coupon_id'])) {
        $coupon = ap_find_coupon_by_id((int) $snapshot['coupon_id']);
    }
    if (!$coupon && !empty($snapshot['coupon_code'])) {
        $coupon = ap_find_coupon_by_code((string) $snapshot['coupon_code']);
    }
    if (!$coupon) {
        ap_cart_clear_coupon();
    }
    return $coupon ?: null;
}

function ap_cart_set_coupon(array $coupon): void
{
    $_SESSION['ap_cart_coupon'] = [
        'coupon_id' => (int) ($coupon['id'] ?? 0),
        'coupon_code' => (string) ($coupon['code'] ?? ''),
    ];
    ap_cart_track_abandonment();
}

function ap_cart_clear_coupon(): void
{
    unset($_SESSION['ap_cart_coupon']);
    ap_cart_track_abandonment();
}

function ap_cart_discount_cents(?int $subtotalOverride = null): int
{
    $coupon = ap_cart_coupon();
    if (!$coupon) {
        return 0;
    }
    $subtotal = $subtotalOverride ?? ap_cart_total_cents();
    $error = ap_coupon_validate($coupon, $subtotal);
    if ($error !== null) {
        ap_cart_clear_coupon();
        return 0;
    }
    return ap_coupon_discount($coupon, $subtotal);
}

function ap_cart_ensure_coupon_valid(): void
{
    $coupon = ap_cart_coupon();
    if (!$coupon) {
        return;
    }
    $subtotal = ap_cart_total_cents();
    $error = ap_coupon_validate($coupon, $subtotal);
    if ($error !== null) {
        ap_cart_clear_coupon();
        ap_flash('Coupon rimosso: ' . $error, 'info');
    }
}

function ap_cart_track_abandonment(?array $itemsOverride = null): void
{
    $items = $itemsOverride ?? ap_cart_items();
    if (empty($items)) {
        ap_abandoned_cart_clear_session();
        return;
    }
    $user = ap_auth_current_user();
    $subtotal = array_sum(array_column($items, 'subtotal_cents'));
    $coupon = ap_cart_coupon();
    $discount = $coupon ? ap_cart_discount_cents($subtotal) : 0;
    $payloadItems = array_map(static function ($item) {
        return [
            'product_id' => (int) $item['product_id'],
            'name' => $item['name'],
            'quantity' => (int) $item['quantity'],
            'price_cents' => (int) $item['price_cents'],
            'subtotal_cents' => (int) $item['subtotal_cents'],
        ];
    }, $items);
    ap_abandoned_cart_save_session([
        'user_id' => $user['id'] ?? null,
        'email' => $user['email'] ?? null,
        'items' => $payloadItems,
        'total_cents' => $subtotal,
        'coupon_code' => $coupon['code'] ?? null,
        'discount_cents' => $discount,
    ]);
}
