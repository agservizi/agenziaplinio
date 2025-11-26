<?php
declare(strict_types=1);

if (function_exists('opcache_reset')) {
    opcache_reset();
}

require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../cart.php';
require_once __DIR__ . '/../faqs.php';
require_once __DIR__ . '/notifications.php';
require_once __DIR__ . '/../database.php';

function ap_action_save_order_custom_data(): void
{
    $user = ap_auth_current_user();
    if (!$user) {
        ap_flash('Accedi per salvare i dati.', 'error');
        return;
    }
    
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $productId = (int) ($_POST['product_id'] ?? 0);
    
    if ($orderId > 0 && $productId > 0) {
        // Save to database for account completion
        $data = [];
        $customFieldsData = $_POST;
        unset($customFieldsData['ap_action'], $customFieldsData['order_id'], $customFieldsData['product_id'], $customFieldsData['redirect_to']);
        
        // Validate required fields
        $product = ap_find_product($productId);
        if (!$product) {
            ap_flash('Prodotto non trovato.', 'error');
            return;
        }
        
        $fields = ap_fetch_product_custom_fields($productId);
        foreach ($fields as $field) {
            $fieldName = $field['field_name'];
            $value = trim($customFieldsData[$fieldName] ?? '');
            
            if ((int) $field['is_required'] === 1 && $value === '') {
                ap_flash('Il campo "' . htmlspecialchars($field['field_label'], ENT_QUOTES) . '" è obbligatorio.', 'error');
                return;
            }
            
            $data[$fieldName] = $value;
        }
        
        try {
            ap_save_order_custom_data($orderId, $productId, $data);
            ap_flash('Dati personalizzati salvati con successo.', 'success');
        } catch (Exception $e) {
            ap_flash('Errore nel salvataggio dei dati.', 'error');
        }
    } else {
        // Save to session for checkout
        $customFieldsData = $_POST['custom_fields'] ?? [];
        if (!is_array($customFieldsData)) {
            ap_flash('Dati non validi.', 'error');
            return;
        }
        // Salva in session per uso successivo nel checkout
        $_SESSION['ap_custom_fields'] = $customFieldsData;
        ap_flash('Dati personalizzati salvati.', 'success');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['ap_action'] ?? null;
    if ($action && trim($action) !== '') {
        try {
            ap_handle_action($action);
        } catch (Throwable $exception) {
            ap_flash('Si è verificato un errore inatteso. Riprova.', 'error');
        }
        ap_redirect_after_post();
    }
}

function ap_handle_action(string $action): void
{
    switch ($action) {
        case 'login':
            ap_action_login();
            break;
        case 'register':
            ap_action_register();
            break;
        case 'logout':
            ap_auth_logout();
            ap_flash('Logout eseguito con successo.', 'success');
            break;
        case 'add_to_cart':
            ap_action_add_to_cart();
            break;
        case 'update_cart':
            ap_action_update_cart();
            break;
        case 'clear_cart':
            ap_action_clear_cart();
            break;
        case 'apply_coupon':
            ap_action_apply_coupon();
            break;
        case 'remove_coupon':
            ap_action_remove_coupon();
            break;
        case 'checkout':
            ap_action_checkout();
            break;
        case 'admin_delete_product':
            ap_action_admin_delete_product();
            break;
        case 'admin_save_product':
            ap_action_admin_save_product();
            break;
        case 'admin_save_coupon':
            ap_action_admin_save_coupon();
            break;
        case 'admin_update_order':
            ap_action_admin_update_order();
            break;
        case 'admin_save_announcement':
            ap_action_admin_save_announcement();
            break;
        case 'admin_delete_announcement':
            ap_action_admin_delete_announcement();
            break;
        case 'admin_save_faq':
            ap_action_admin_save_faq();
            break;
        case 'admin_delete_user':
            ap_action_admin_delete_user();
            break;
        case 'admin_save_user':
            ap_action_admin_save_user();
            break;
        case 'admin_save_settings':
            ap_action_admin_save_settings();
            break;
        case 'save_address':
            ap_action_save_address();
            break;
        case 'delete_address':
            ap_action_delete_address();
            break;
        case 'set_default_address':
            ap_action_set_default_address();
            break;
        case 'repeat_order':
            ap_action_repeat_order();
            break;
        case 'change_password':
            ap_action_change_password();
            break;
        case 'update_profile':
            ap_action_update_profile();
            break;
        case 'save_order_custom_data':
            ap_action_save_order_custom_data();
            break;
        case 'toggle_wishlist':
            ap_action_toggle_wishlist();
            break;
        case 'remove_from_wishlist':
            ap_action_remove_from_wishlist();
            break;
        case 'add_product_review':
            ap_action_add_product_review();
            break;
        case 'mark_notification_read':
            ap_action_mark_notification_read();
            break;
        case 'mark_all_notifications_read':
            ap_action_mark_all_notifications_read();
            break;
        case 'track_event':
            ap_action_track_event();
            break;
        default:
            ap_flash('Azione non riconosciuta: ' . $action, 'error');
    }
}

function ap_action_update_profile(): void
{
    $user = ap_auth_current_user();
    if (!$user) {
        ap_flash('Accedi per modificare il profilo.', 'error');
        return;
    }
    $name = trim($_POST['name'] ?? '');
    $rawEmail = trim($_POST['email'] ?? '');
    $email = filter_var($rawEmail, FILTER_VALIDATE_EMAIL) ?: '';
    if ($name === '' || $email === '') {
        ap_flash('Nome ed email sono obbligatori.', 'error');
        return;
    }
    if ($name === ($user['name'] ?? '') && $email === ($user['email'] ?? '')) {
        ap_flash('Nessuna modifica rilevata.', 'info');
        return;
    }
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1');
    $stmt->execute([
        ':email' => $email,
        ':id' => (int) $user['id'],
    ]);
    if ($stmt->fetch()) {
        ap_flash('Email già utilizzata da un altro account.', 'error');
        return;
    }
    $update = $pdo->prepare('UPDATE users SET name = :name, email = :email WHERE id = :id');
    $update->execute([
        ':name' => $name,
        ':email' => $email,
        ':id' => (int) $user['id'],
    ]);
    $_SESSION['ap_auth_user']['name'] = $name;
    $_SESSION['ap_auth_user']['email'] = $email;
    ap_flash('Profilo aggiornato con successo.', 'success');
}

function ap_action_login(): void
{
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?: '';
    $password = trim($_POST['password'] ?? '');
    if (!$email || $password === '') {
        ap_flash('Inserisci email e password validi.', 'error');
        return;
    }
    if (ap_auth_attempt($email, $password)) {
        ap_flash('Bentornato!', 'success');
        return;
    }
    ap_flash('Credenziali non corrette.', 'error');
}

function ap_action_register(): void
{
    $name = trim($_POST['name'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?: '';
    $password = trim($_POST['password'] ?? '');
    if ($name === '' || !$email || strlen($password) < 8) {
        ap_flash('Compila tutti i campi: password minima 8 caratteri.', 'error');
        return;
    }
    if (ap_auth_register($name, $email, $password)) {
        ap_notify_user_registered($name, $email);
        ap_flash('Registrazione completata, effettua l\'accesso.', 'success');
        return;
    }
    ap_flash('Email già registrata.', 'error');
}

function ap_action_add_to_cart(): void
{
    $productId = (int) ($_POST['product_id'] ?? 0);
    $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
    $product = ap_find_product($productId);
    if (!$product || (int) $product['is_active'] !== 1) {
        ap_flash('Prodotto non disponibile.', 'error');
        return;
    }
    if ($quantity > (int) $product['stock']) {
        ap_flash('Quantità richiesta non disponibile a stock.', 'error');
        return;
    }
    $current = $_SESSION['ap_cart'][$productId] ?? 0;
    if ($current + $quantity > (int) $product['stock']) {
        ap_flash('Hai già raggiunto la quantità massima disponibile.', 'error');
        return;
    }
    ap_cart_add($productId, $quantity);

    // Save custom fields for digital products
    $customFieldsData = $_POST['custom_fields'] ?? [];
    if (!empty($customFieldsData[$productId])) {
        $_SESSION['ap_cart_custom_fields'][$productId] = $customFieldsData[$productId];
    }

    ap_flash('Prodotto aggiunto al carrello.', 'success');

    // Track analytics event
    $user = ap_auth_current_user();
    ap_track_event('add_to_cart', [
        'product_id' => $productId,
        'quantity' => $quantity,
        'product_name' => $product['name'],
        'product_price' => (int) $product['price_cents']
    ], $user ? (int) $user['id'] : null);
}

function ap_action_update_cart(): void
{
    $items = $_POST['items'] ?? [];
    if (!is_array($items)) {
        return;
    }
    foreach ($items as $productId => $quantity) {
        $productId = (int) $productId;
        $qty = (int) $quantity;
        $product = ap_find_product($productId);
        if ($product && $qty > (int) $product['stock']) {
            $qty = (int) $product['stock'];
        }
        ap_cart_update($productId, $qty);
    }
    ap_cart_ensure_coupon_valid();
    ap_flash('Carrello aggiornato.', 'success');
}

function ap_action_clear_cart(): void
{
    ap_cart_clear();
    ap_flash('Carrello svuotato.', 'success');
}

function ap_action_apply_coupon(): void
{
    $code = strtoupper(trim((string) ($_POST['coupon_code'] ?? '')));
    if ($code === '') {
        ap_flash('Inserisci un codice coupon.', 'error');
        return;
    }
    $items = ap_cart_items();
    if (!$items) {
        ap_flash('Aggiungi almeno un prodotto al carrello prima di applicare un coupon.', 'error');
        return;
    }
    $coupon = ap_find_coupon_by_code($code);
    if (!$coupon) {
        ap_flash('Coupon non trovato o non più valido.', 'error');
        return;
    }
    $subtotal = array_sum(array_column($items, 'subtotal_cents'));
    $validation = ap_coupon_validate($coupon, $subtotal);
    if ($validation !== null) {
        ap_flash($validation, 'error');
        return;
    }
    ap_cart_set_coupon($coupon);
    $discount = ap_coupon_discount($coupon, $subtotal);
    ap_flash('Coupon applicato: -' . ap_price_format($discount), 'success');
}

function ap_action_remove_coupon(): void
{
    if (!empty($_SESSION['ap_cart_coupon'])) {
        ap_cart_clear_coupon();
        ap_flash('Coupon rimosso.', 'info');
        return;
    }
    ap_flash('Non ci sono coupon attivi da rimuovere.', 'info');
}

function ap_action_checkout(): void
{
    $user = ap_auth_current_user();
    if (!$user) {
        ap_flash('Accedi per completare l\'ordine.', 'error');
        return;
    }
    $items = ap_cart_items();
    if (!$items) {
        ap_flash('Il carrello è vuoto.', 'error');
        return;
    }

    // Check if all products are digital
    $isAllDigital = array_reduce($items, function($carry, $item) {
        $product = ap_find_product((int) $item['product_id']);
        return $carry && $product && strtolower((string) $product['fulfillment_type']) === 'digital';
    }, true);
    $addressId = isset($_POST['shipping_address_id']) ? (int) $_POST['shipping_address_id'] : 0;
    $selectedAddress = null;
    if ($addressId > 0) {
        $selectedAddress = ap_find_address((int) $user['id'], $addressId);
    }
    $name = trim($_POST['shipping_name'] ?? $user['name']);
    $street = trim($_POST['shipping_address'] ?? '');
    $city = trim($_POST['shipping_city'] ?? '');
    $postal = trim($_POST['shipping_postal_code'] ?? '');
    $province = trim($_POST['shipping_province'] ?? '');
    $country = trim($_POST['shipping_country'] ?? 'Italia');
    $phone = trim($_POST['shipping_phone'] ?? '');
    $shippingMethod = $_POST['shipping_method'] ?? 'standard';
    $shippingOptions = ap_shipping_methods();
    if (!array_key_exists($shippingMethod, $shippingOptions)) {
        $shippingMethod = 'standard';
    }
    $shippingCost = $isAllDigital ? 0 : (int) $shippingOptions[$shippingMethod]['price_cents'];
    $shippingNotes = trim($_POST['shipping_notes'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? 'card';
    $poNumber = trim($_POST['po_number'] ?? '');
    $costCenter = trim($_POST['cost_center'] ?? '');
    $deliveryWindow = $_POST['delivery_window'] ?? 'standard';
    $slaPlan = $_POST['sla_plan'] ?? 'core';
    $allowedWindows = ['standard', 'early', 'late'];
    $allowedSla = ['core', 'priority', 'mission'];
    if (!in_array($deliveryWindow, $allowedWindows, true)) {
        $deliveryWindow = 'standard';
    }
    if (!in_array($slaPlan, $allowedSla, true)) {
        $slaPlan = 'core';
    }
    $addonsSelected = $_POST['addons'] ?? [];
    $opsSelected = $_POST['ops_channels'] ?? [];
    if (!is_array($addonsSelected)) {
        $addonsSelected = [];
    }
    if (!is_array($opsSelected)) {
        $opsSelected = [];
    }
    $allowedAddons = ['carbon-neutral', 'white-glove', 'audit-ready'];
    $allowedOps = ['slack', 'teams', 'whatsapp'];
    $addons = array_values(array_unique(array_intersect($addonsSelected, $allowedAddons)));
    $opsChannels = array_values(array_unique(array_intersect($opsSelected, $allowedOps)));
    $contractRef = trim($_POST['contract_ref'] ?? '');
    $procurementFiles = ap_handle_procurement_uploads($_FILES['procurement_files'] ?? null);
    $address = '';
    if ($isAllDigital) {
        $address = 'Consegna digitale';
    } elseif ($selectedAddress) {
        $name = $selectedAddress['recipient_name'];
        $address = ap_format_address($selectedAddress);
        $phone = $selectedAddress['phone'] ?? '';
        $street = $selectedAddress['street'];
        $city = $selectedAddress['city'];
        $postal = $selectedAddress['postal_code'];
        $province = $selectedAddress['province'] ?? '';
        $country = $selectedAddress['country'] ?? 'Italia';
    } else {
        if ($name === '' || $street === '' || $city === '' || $postal === '') {
            ap_flash('Inserisci tutti i dettagli di spedizione richiesti.', 'error');
            return;
        }
        $addressParts = [$street, $postal . ' ' . $city];
        if ($province !== '') {
            $addressParts[] = strtoupper($province);
        }
        $addressParts[] = $country;
        $address = implode("\n", array_filter(array_map('trim', $addressParts)));
    }
    foreach ($items as $item) {
        $product = ap_find_product($item['product_id']);
        if (!$product || $item['quantity'] > (int) $product['stock']) {
            ap_flash('Aggiorna il carrello: disponibilità variata.', 'error');
            return;
        }
    }

    // Validate custom fields for digital products
    $customFieldsData = $_POST['custom_fields'] ?? [];
    foreach ($items as $item) {
        $product = ap_find_product($item['product_id']);
        if ($product['fulfillment_type'] === 'digital') {
            $fields = ap_fetch_product_custom_fields($item['product_id']);
            foreach ($fields as $field) {
                $fieldId = $field['id'];
                $value = trim($customFieldsData[$item['product_id']][$fieldId] ?? '');
                if ((int) $field['is_required'] === 1 && $value === '') {
                    ap_flash('Il campo "' . htmlspecialchars($field['field_label'], ENT_QUOTES) . '" è obbligatorio per ' . htmlspecialchars($product['name']), 'error');
                    return;
                }
            }
        }
    }
    $itemsTotal = ap_cart_total_cents();
    $coupon = ap_cart_coupon();
    $discount = 0;
    if ($coupon) {
        $couponValidation = ap_coupon_validate($coupon, $itemsTotal);
        if ($couponValidation !== null) {
            ap_cart_clear_coupon();
            ap_flash('Coupon rimosso: ' . $couponValidation, 'info');
        } else {
            $discount = ap_coupon_discount($coupon, $itemsTotal);
        }
    }
    $netSubtotal = max(0, $itemsTotal - $discount);
    $total = $netSubtotal + $shippingCost;
    $payload = [
        'total_cents' => $total,
        'shipping_name' => $name,
        'shipping_address' => $address,
        'shipping_method' => $shippingMethod,
        'shipping_cost_cents' => $shippingCost,
        'shipping_notes' => $shippingNotes,
        'shipping_address_id' => $selectedAddress['id'] ?? null,
        'contact_phone' => $phone ?: null,
        'po_number' => $poNumber ?: null,
        'cost_center' => $costCenter ?: null,
        'delivery_window' => $deliveryWindow,
        'sla_plan' => $slaPlan,
        'addons' => $addons,
        'ops_channels' => $opsChannels,
        'contract_ref' => $contractRef ?: null,
        'procurement_files' => $procurementFiles,
        'coupon_id' => $discount > 0 ? ($coupon['id'] ?? null) : null,
        'coupon_code' => $discount > 0 ? ($coupon['code'] ?? null) : null,
        'discount_cents' => $discount,
    ];
    $saveAddress = !$selectedAddress && !empty($_POST['save_shipping_address']);
    if ($saveAddress) {
        $newAddressId = ap_save_address((int) $user['id'], [
            'label' => trim($_POST['address_label'] ?? 'Indirizzo principale') ?: 'Indirizzo salvato',
            'recipient_name' => $name,
            'street' => $street,
            'city' => $city,
            'province' => $province ?: null,
            'postal_code' => $postal,
            'country' => $country,
            'phone' => $phone ?: null,
            'is_default' => isset($_POST['mark_default_address']) ? 1 : 0,
        ]);
        if ($newAddressId) {
            $payload['shipping_address_id'] = $newAddressId;
        }
    }
    $orderId = ap_create_order((int) $user['id'], $items, [
        'total_cents' => $payload['total_cents'],
        'shipping_name' => $payload['shipping_name'],
        'shipping_address' => $payload['shipping_address'],
        'shipping_method' => $payload['shipping_method'],
        'shipping_cost_cents' => $payload['shipping_cost_cents'],
        'shipping_notes' => $payload['shipping_notes'],
        'shipping_address_id' => $payload['shipping_address_id'],
        'contact_phone' => $payload['contact_phone'],
    ]);

    // Save custom fields for digital products
    foreach ($items as $item) {
        $product = ap_find_product($item['product_id']);
        if ($product['fulfillment_type'] === 'digital') {
            $fields = ap_fetch_product_custom_fields($item['product_id']);
            $data = [];
            foreach ($fields as $field) {
                $fieldId = $field['id'];
                $value = trim($customFieldsData[$item['product_id']][$fieldId] ?? '');
                $data[$field['field_name']] = $value;
            }
            if (!empty($data)) {
                ap_save_order_custom_data($orderId, $item['product_id'], $data);
            }
        }
    }
    $klarnaRedirect = null;
    $stripeRedirect = null;
    if ($paymentMethod === 'klarna') {
        try {
            $session = ap_klarna_create_session($orderId, $items, array_merge($payload, [
                'shipping_street' => $street,
                'shipping_city' => $city,
                'shipping_postal_code' => $postal,
                'shipping_province' => $province,
                'shipping_country' => $country,
                'customer_email' => $user['email'] ?? null,
            ]));
            ap_record_payment($orderId, [
                'provider' => 'klarna',
                'status' => 'pending',
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'amount_cents' => $total,
                'transaction_ref' => $session['session_id'] ?? null,
                'meta' => [
                    'klarna_session_id' => $session['session_id'] ?? null,
                    'klarna_session_url' => $session['session_url'] ?? null,
                    'klarna_response' => $session,
                ],
            ]);
            $klarnaRedirect = $session['session_url'] ?? null;
        } catch (Throwable $klarnaException) {
            ap_cancel_order_and_restore_stock($orderId);
            ap_flash('Pagamento Klarna non disponibile: ' . $klarnaException->getMessage(), 'error');
            return;
        }
    } elseif ($paymentMethod === 'card') {
        try {
            $session = ap_stripe_create_session($orderId, $items, array_merge($payload, [
                'shipping_street' => $street,
                'shipping_city' => $city,
                'shipping_postal_code' => $postal,
                'shipping_province' => $province,
                'shipping_country' => $country,
                'customer_email' => $user['email'] ?? null,
            ]));
            ap_record_payment($orderId, [
                'provider' => 'stripe',
                'status' => 'pending',
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'amount_cents' => $total,
                'transaction_ref' => $session['id'] ?? null,
                'meta' => [
                    'stripe_session_id' => $session['id'] ?? null,
                    'stripe_session_url' => $session['url'] ?? null,
                    'stripe_response' => $session,
                ],
            ]);
            $stripeRedirect = $session['url'] ?? null;
        } catch (Throwable $stripeException) {
            ap_cancel_order_and_restore_stock($orderId);
            ap_flash('Pagamento Stripe non disponibile: ' . $stripeException->getMessage(), 'error');
            return;
        }
    } else {
        ap_simulate_payment($orderId, $paymentMethod, $total);
    }
    ap_notify_order_received($orderId);
    ap_cart_clear();

    // Track order completion analytics
    $totalItems = array_sum(array_column($items, 'quantity'));
    ap_track_event('order_completed', [
        'order_id' => $orderId,
        'total_amount' => $total,
        'payment_method' => $paymentMethod,
        'items_count' => count($items),
        'total_quantity' => $totalItems
    ], (int) $user['id']);

    if ($paymentMethod === 'wire') {
        ap_flash("Ordine #{$orderId} creato. Completa il bonifico per avviare la spedizione.", 'info');
    } elseif ($paymentMethod === 'klarna') {
        ap_flash("Ordine #{$orderId} creato. Completa il pagamento su Klarna.", 'info');
        if ($klarnaRedirect) {
            ap_override_redirect($klarnaRedirect);
        }
    } elseif ($paymentMethod === 'card') {
        ap_flash("Ordine #{$orderId} creato. Completa il pagamento su Stripe.", 'info');
        if ($stripeRedirect) {
            ap_override_redirect($stripeRedirect);
        }
    } else {
        ap_flash("Ordine #{$orderId} confermato. Ti aggiorneremo via email.", 'success');
    }
    // Log audit event for order completion
    ap_log_audit_event('user_action', 'Ordine completato con successo', (int) $user['id'], [
        'action' => 'order_completed',
        'order_id' => $orderId,
        'total_amount' => $total,
        'payment_method' => $paymentMethod,
        'items_count' => count($items)
    ]);
}

function ap_action_admin_save_product(): void
{
    if (!ap_auth_is_admin()) {
        ap_flash('Non autorizzato.', 'error');
        return;
    }
    $user = ap_auth_current_user();
    $id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : null;
    $name = trim($_POST['name'] ?? '');
    $price = (float) str_replace(',', '.', (string) ($_POST['price'] ?? '0'));
    $sku = trim($_POST['sku'] ?? '');
    $stock = max(0, (int) ($_POST['stock'] ?? 0));
    $description = trim($_POST['description'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: ap_slugify($name);
    $image = trim($_POST['image_url'] ?? 'assets/img/og-image.jpg');
    $uploaded = ap_handle_product_upload($_FILES['image_file'] ?? null);
    if ($uploaded) {
        $image = $uploaded;
    }
    $active = isset($_POST['is_active']) ? 1 : 0;
    $categoryKey = trim((string) ($_POST['category_key'] ?? ''));
    $fulfillmentType = $_POST['fulfillment_type'] ?? 'digital';
    if (!in_array($fulfillmentType, ['digital', 'physical'], true)) {
        $fulfillmentType = 'digital';
    }
    $categoryOptions = ap_product_category_options();
    if ($categoryKey !== '' && !array_key_exists($categoryKey, $categoryOptions)) {
        ap_flash('Categoria selezionata non valida.', 'error');
        return;
    }
    if ($categoryKey === '') {
        $categoryKey = null;
    }

    if ($name === '' || $price <= 0) {
        ap_flash('Nome e prezzo sono obbligatori.', 'error');
        return;
    }
    $data = [
        'name' => $name,
        'slug' => $slug,
        'description' => $description,
        'price_cents' => (int) round($price * 100),
        'sku' => $sku,
        'category_key' => $categoryKey,
        'fulfillment_type' => $fulfillmentType,
        'stock' => $stock,
        'image_url' => $image,
        'is_active' => $active,
    ];
    $result = ap_save_product($data, $id);
    if ($result) {
        ap_flash('Catalogo aggiornato.', 'success');
        // Log audit event
        ap_log_audit_event('admin_action', $id ? 'Prodotto aggiornato: ' . $name : 'Prodotto creato: ' . $name, $user['id'] ?? null, [
            'action' => $id ? 'product_updated' : 'product_created',
            'product_id' => $result,
            'product_name' => $name,
            'changes' => $data
        ]);
    } else {
        ap_flash('Prodotto non trovato o nessuna modifica rilevata.', 'error');
    }

    // Handle custom fields
    $customFieldsSaved = false;
    if ($id) {
        // Save new or update existing fields
        $customFields = $_POST['custom_fields'] ?? [];
        // Delete existing fields not in the current POST (only numeric IDs)
        $postedFieldIds = array_filter(array_keys($customFields), 'is_numeric');
        $postedFieldIds = array_map('intval', $postedFieldIds);
        if (!empty($postedFieldIds)) {
            $placeholders = str_repeat('?,', count($postedFieldIds) - 1) . '?';
            $pdo = ap_db();
            $stmt = $pdo->prepare("DELETE FROM product_custom_fields WHERE product_id = ? AND id NOT IN ($placeholders)");
            $stmt->execute(array_merge([$id], $postedFieldIds));
        } else {
            // If no existing fields posted, delete all
            $pdo = ap_db();
            $stmt = $pdo->prepare('DELETE FROM product_custom_fields WHERE product_id = ?');
            $stmt->execute([$id]);
        }

        foreach ($customFields as $fieldId => $fieldData) {
            if (empty($fieldData['name']) || empty($fieldData['label'])) continue;
            $data = [
                'id' => is_numeric($fieldId) ? (int)$fieldId : null,
                'name' => trim($fieldData['name']),
                'label' => trim($fieldData['label']),
                'type' => $fieldData['type'] ?? 'text',
                'options' => trim($fieldData['options'] ?? ''),
                'required' => isset($fieldData['required']),
                'sort' => 0,
            ];
            ap_save_product_custom_field($id, $data);
        }
        file_put_contents('/tmp/debug.log', "Saved custom fields for product $id: " . json_encode($customFields) . "\n", FILE_APPEND);
        $customFieldsSaved = true;
    }

    // Update flash message if custom fields were saved
    if (!$result && $customFieldsSaved) {
        ap_flash('Catalogo aggiornato.', 'success');
    }
}

function ap_action_admin_save_announcement(): void
{
    if (!ap_auth_is_admin()) {
        ap_flash('Non autorizzato.', 'error');
        return;
    }
    $id = isset($_POST['announcement_id']) ? (int) $_POST['announcement_id'] : null;
    $message = trim((string) ($_POST['message'] ?? ''));
    $isActive = isset($_POST['is_active']);
    if ($message === '') {
        ap_flash('Scrivi il testo della news da mostrare nella topbar.', 'error');
        return;
    }
    ap_save_announcement([
        'message' => mb_substr($message, 0, 280),
        'is_active' => $isActive ? 1 : 0,
    ], $id ?: null);
    ap_flash($id ? 'News aggiornata.' : 'News pubblicata.', 'success');
}

function ap_action_admin_delete_announcement(): void
{
    if (!ap_auth_is_admin()) {
        ap_flash('Non autorizzato.', 'error');
        return;
    }
    $id = isset($_POST['announcement_id']) ? (int) $_POST['announcement_id'] : 0;
    if ($id <= 0) {
        ap_flash('News non trovata.', 'error');
        return;
    }
    ap_delete_announcement($id);
    ap_flash('News rimossa.', 'info');
}

function ap_action_admin_save_faq(): void
{
    if (!ap_auth_is_admin()) {
        ap_flash('Non autorizzato.', 'error');
        return;
    }
    $id = isset($_POST['faq_id']) ? (int) $_POST['faq_id'] : null;
    $payload = [
        'question' => trim((string) ($_POST['question'] ?? '')),
        'answer' => trim((string) ($_POST['answer'] ?? '')),
        'category' => trim((string) ($_POST['category'] ?? '')),
        'keywords' => trim((string) ($_POST['keywords'] ?? '')),
        'sort_order' => isset($_POST['sort_order']) ? (int) $_POST['sort_order'] : 0,
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];
    try {
        ap_save_faq($payload, $id ?: null);
    } catch (InvalidArgumentException $exception) {
        ap_flash('Compila domanda e risposta per salvare la FAQ.', 'error');
        return;
    }
    ap_flash($id ? 'FAQ aggiornata.' : 'FAQ aggiunta al bot.', 'success');
}

function ap_action_admin_delete_faq(): void
{
    if (!ap_auth_is_admin()) {
        ap_flash('Non autorizzato.', 'error');
        return;
    }
    $id = isset($_POST['faq_id']) ? (int) $_POST['faq_id'] : 0;
    if ($id <= 0) {
        ap_flash('FAQ non trovata.', 'error');
        return;
    }
    ap_delete_faq($id);
    ap_flash('FAQ rimossa dal bot.', 'info');
}

function ap_action_admin_save_coupon(): void
{
    if (!ap_auth_is_admin()) {
        ap_flash('Operazione non consentita.', 'error');
        return;
    }
    $couponId = isset($_POST['coupon_id']) ? (int) $_POST['coupon_id'] : null;
    if ($couponId !== null && $couponId <= 0) {
        $couponId = null;
    }
    $code = strtoupper(trim((string) ($_POST['code'] ?? '')));
    $description = trim((string) ($_POST['description'] ?? ''));
    $type = ($_POST['type'] ?? 'fixed') === 'percent' ? 'percent' : 'fixed';
    $amountInput = str_replace(',', '.', (string) ($_POST['amount'] ?? '0'));
    $amountCents = (int) round(max(0, (float) $amountInput) * 100);
    $percent = (int) max(0, min(100, (int) ($_POST['percent'] ?? 0)));
    $minTotalInput = str_replace(',', '.', (string) ($_POST['min_total'] ?? '0'));
    $minTotalCents = (int) round(max(0, (float) $minTotalInput) * 100);
    $maxRedemptionsRaw = trim((string) ($_POST['max_redemptions'] ?? ''));
    $maxRedemptions = $maxRedemptionsRaw === '' ? null : max(0, (int) $maxRedemptionsRaw);
    if ($maxRedemptions === 0) {
        $maxRedemptions = null;
    }
    $startsInput = trim((string) ($_POST['starts_at'] ?? ''));
    $endsInput = trim((string) ($_POST['ends_at'] ?? ''));
    $startsTimestamp = $startsInput !== '' ? strtotime($startsInput) : false;
    $endsTimestamp = $endsInput !== '' ? strtotime($endsInput) : false;
    $startsAt = $startsTimestamp ? date('Y-m-d H:i:s', $startsTimestamp) : null;
    $endsAt = $endsTimestamp ? date('Y-m-d H:i:s', $endsTimestamp) : null;
    if ($code === '') {
        ap_flash('Inserisci un codice coupon.', 'error');
        return;
    }
    if ($type === 'fixed' && $amountCents <= 0) {
        ap_flash('Definisci un importo per il coupon a valore fisso.', 'error');
        return;
    }
    if ($type === 'percent' && $percent <= 0) {
        ap_flash('Inserisci una percentuale compresa tra 1 e 100.', 'error');
        return;
    }
    if ($startsTimestamp && $endsTimestamp && $startsTimestamp > $endsTimestamp) {
        ap_flash('La data di fine deve essere successiva alla data di inizio.', 'error');
        return;
    }
    $existing = ap_find_coupon_by_code($code);
    if ($existing && (!$couponId || (int) $existing['id'] !== $couponId)) {
        ap_flash('Codice coupon già in uso.', 'error');
        return;
    }
    $data = [
        'code' => $code,
        'description' => $description !== '' ? $description : null,
        'type' => $type,
        'amount_cents' => $amountCents,
        'percent' => $percent,
        'min_total_cents' => $minTotalCents,
        'max_redemptions' => $maxRedemptions,
        'starts_at' => $startsAt,
        'ends_at' => $endsAt,
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];
    ap_save_coupon($data, $couponId);
    ap_flash('Coupon salvato correttamente.', 'success');
}

function ap_action_admin_update_order(): void
{
    if (!ap_auth_is_admin()) {
        ap_flash('Operazione non consentita.', 'error');
        return;
    }
    $user = ap_auth_current_user();
    $orderId = (int) ($_POST['order_id'] ?? 0);
    if ($orderId <= 0) {
        ap_flash('Ordine non valido.', 'error');
        return;
    }
    $status = $_POST['status'] ?? 'pending';
    $allowed = ['pending', 'processing', 'completed', 'cancelled'];
    if (!in_array($status, $allowed, true)) {
        ap_flash('Stato non valido.', 'error');
        return;
    }
    $shippingStatus = $_POST['shipping_status'] ?? null;
    $shippingMethod = $_POST['shipping_method'] ?? null;
    $tracking = trim($_POST['tracking_code'] ?? '');
    if ($shippingMethod === '') {
        $shippingMethod = null;
    }
    if ($shippingStatus === '') {
        $shippingStatus = null;
    }
    if ($shippingStatus && !in_array($shippingStatus, ['preparing', 'in_transit', 'delivered', 'issue'], true)) {
        ap_flash('Stato spedizione non valido.', 'error');
        return;
    }
    $pdo = ap_db();
    $stmt = $pdo->prepare('UPDATE orders SET status = :status WHERE id = :id');
    $stmt->execute([':status' => $status, ':id' => $orderId]);
    if ($shippingStatus || $tracking || $shippingMethod) {
        ap_update_shipping($orderId, [
            'shipping_status' => $shippingStatus,
            'tracking_code' => $tracking,
            'shipping_method' => $shippingMethod,
        ]);
    }
    ap_notify_order_status($orderId, $status);
    ap_flash('Dati ordine aggiornati.', 'success');
    // Log audit event
    ap_log_audit_event('admin_action', 'Ordine aggiornato: stato cambiato a ' . $status, $user['id'] ?? null, [
        'action' => 'order_updated',
        'order_id' => $orderId,
        'new_status' => $status,
        'shipping_status' => $shippingStatus,
        'tracking_code' => $tracking
    ]);
}

function ap_action_save_address(): void
{
    $user = ap_auth_current_user();
    if (!$user) {
        ap_flash('Accedi per gestire gli indirizzi.', 'error');
        return;
    }
    $addressId = isset($_POST['address_id']) ? (int) $_POST['address_id'] : null;
    $data = [
        'label' => trim($_POST['label'] ?? ''),
        'recipient_name' => trim($_POST['recipient_name'] ?? ''),
        'street' => trim($_POST['street'] ?? ''),
        'city' => trim($_POST['city'] ?? ''),
        'province' => trim($_POST['province'] ?? ''),
        'postal_code' => trim($_POST['postal_code'] ?? ''),
        'country' => trim($_POST['country'] ?? 'Italia'),
        'phone' => trim($_POST['phone'] ?? ''),
        'is_default' => isset($_POST['is_default']) ? 1 : 0,
    ];
    if ($data['label'] === '' || $data['recipient_name'] === '' || $data['street'] === '' || $data['city'] === '' || $data['postal_code'] === '') {
        ap_flash('Compila tutti i campi obbligatori dell\'indirizzo.', 'error');
        return;
    }
    $savedId = ap_save_address((int) $user['id'], $data, $addressId);
    if ($savedId) {
        ap_flash('Indirizzo salvato correttamente.', 'success');
    } else {
        ap_flash('Impossibile salvare l\'indirizzo.', 'error');
    }
}

function ap_action_delete_address(): void
{
    $user = ap_auth_current_user();
    if (!$user) {
        ap_flash('Accedi per gestire gli indirizzi.', 'error');
        return;
    }
    $addressId = isset($_POST['address_id']) ? (int) $_POST['address_id'] : 0;
    if ($addressId > 0) {
        ap_delete_address((int) $user['id'], $addressId);
        ap_flash('Indirizzo rimosso.', 'success');
    }
}

function ap_action_set_default_address(): void
{
    $user = ap_auth_current_user();
    if (!$user) {
        ap_flash('Accedi per gestire gli indirizzi.', 'error');
        return;
    }
    $addressId = isset($_POST['address_id']) ? (int) $_POST['address_id'] : 0;
    if ($addressId > 0 && ap_find_address((int) $user['id'], $addressId)) {
        ap_set_default_address((int) $user['id'], $addressId);
        ap_flash('Indirizzo impostato come predefinito.', 'success');
    }
}

function ap_action_repeat_order(): void
{
    $user = ap_auth_current_user();
    if (!$user) {
        ap_flash('Effettua il login per riordinare.', 'error');
        return;
    }
    $orderId = (int) ($_POST['order_id'] ?? 0);
    if ($orderId <= 0) {
        ap_flash('Ordine non valido.', 'error');
        return;
    }
    $order = ap_fetch_order($orderId);
    if (!$order || (int) $order['user_id'] !== (int) $user['id']) {
        ap_flash('Ordine non disponibile.', 'error');
        return;
    }
    $items = ap_fetch_order_items($orderId);
    if (!$items) {
        ap_flash('Nessun articolo da riordinare.', 'error');
        return;
    }
    $added = false;
    foreach ($items as $item) {
        $product = ap_find_product((int) $item['product_id']);
        if (!$product || (int) $product['is_active'] !== 1) {
            continue;
        }
        $maxQty = min((int) $product['stock'], (int) $item['quantity']);
        if ($maxQty <= 0) {
            continue;
        }
        $productId = (int) $product['id'];
        $currentQty = $_SESSION['ap_cart'][$productId] ?? 0;
        $available = max(0, $maxQty - (int) $currentQty);
        if ($available > 0) {
            ap_cart_add($productId, $available);
            $added = true;
        }
    }
    ap_cart_ensure_coupon_valid();
    if ($added) {
        ap_flash('Articoli riaggiunti al carrello. Puoi finalizzare il nuovo ordine.', 'success');
    } else {
        ap_flash('I prodotti non sono più disponibili a stock.', 'error');
    }
}

function ap_action_change_password(): void
{
    $user = ap_auth_current_user();
    if (!$user) {
        ap_flash('Accedi per aggiornare la password.', 'error');
        return;
    }
    $current = trim($_POST['current_password'] ?? '');
    $new = trim($_POST['new_password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');
    if ($current === '' || $new === '' || $confirm === '') {
        ap_flash('Compila tutti i campi per continuare.', 'error');
        return;
    }
    if (!password_verify($current, $user['password_hash'] ?? '')) {
        ap_flash('La password attuale non è corretta.', 'error');
        return;
    }
    if (strlen($new) < 8) {
        ap_flash('La nuova password deve avere almeno 8 caratteri.', 'error');
        return;
    }
    if ($new !== $confirm) {
        ap_flash('Conferma password non corrispondente.', 'error');
        return;
    }
    if (password_verify($new, $user['password_hash'] ?? '')) {
        ap_flash('Imposta una password diversa da quella attuale.', 'info');
        return;
    }
    $hash = password_hash($new, PASSWORD_BCRYPT);
    $pdo = ap_db();
    $stmt = $pdo->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
    $stmt->execute([
        ':hash' => $hash,
        ':id' => (int) $user['id'],
    ]);
    $_SESSION['ap_auth_user']['password_hash'] = $hash;
    ap_flash('Password aggiornata correttamente.', 'success');
}

function ap_action_admin_delete_product(): void
{
    if (!ap_auth_is_admin()) {
        ap_flash('Non autorizzato.', 'error');
        return;
    }
    $id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
    if ($id <= 0) {
        ap_flash('Prodotto non trovato. ID ricevuto: ' . $id, 'error');
        return;
    }
    $result = ap_delete_product($id);
    if ($result) {
        ap_flash('Prodotto eliminato forzatamente dal catalogo. Gli ordini esistenti potrebbero essere influenzati.', 'warning');
    } else {
        ap_flash('Errore durante l\'eliminazione forzata del prodotto.', 'error');
    }
}

function ap_simulate_payment(int $orderId, string $method, int $amount): void
{
    $method = strtolower($method);
    $instant = in_array($method, ['card', 'paypal'], true);
    $status = $instant ? 'paid' : 'pending';
    $orderStatus = $instant ? 'processing' : 'pending';
    $transactionRef = strtoupper($method ?: 'MANUAL') . '-' . strtoupper(substr(ap_random_token(12), 0, 8));
    ap_record_payment($orderId, [
        'provider' => $method ?: 'manual',
        'status' => $status,
        'payment_status' => $status,
        'order_status' => $orderStatus,
        'amount_cents' => $amount,
        'transaction_ref' => $transactionRef,
        'meta' => ['simulated' => true],
    ]);
}

function ap_redirect_after_post(): void
{
    if (!empty($GLOBALS['ap_redirect_override'])) {
        header('Location: ' . $GLOBALS['ap_redirect_override']);
        exit;
    }
    $target = $_POST['redirect_to'] ?? ($_SERVER['HTTP_REFERER'] ?? '?page=shop');
    $target = ap_filter_redirect($target);
    header('Location: ' . $target);
    exit;
}

function ap_override_redirect(string $absoluteUrl): void
{
    if ($absoluteUrl !== '') {
        $GLOBALS['ap_redirect_override'] = $absoluteUrl;
    }
}

function ap_filter_redirect(string $value): string
{
    $value = trim($value);
    if ($value === '' || preg_match('/^https?:/i', $value)) {
        return '?page=shop';
    }
    if ($value[0] !== '/' && $value[0] !== '?') {
        return '?page=shop';
    }
    return $value;
}

function ap_action_admin_upload_digital_file(): void
{
    if (!ap_auth_is_admin()) {
        ap_flash('Non autorizzato.', 'error');
        return;
    }
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $productId = (int) ($_POST['product_id'] ?? 0);
    if ($orderId <= 0 || $productId <= 0) {
        ap_flash('Richiesta non valida.', 'error');
        return;
    }
    $order = ap_fetch_order($orderId);
    if (!$order) {
        ap_flash('Ordine non trovato.', 'error');
        return;
    }
    $product = ap_find_product($productId);
    if (!$product || $product['fulfillment_type'] !== 'digital') {
        ap_flash('Prodotto non valido.', 'error');
        return;
    }
    $file = $_FILES['digital_file'] ?? null;
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        ap_flash('Errore nel caricamento del file.', 'error');
        return;
    }
    $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
    if (!in_array($file['type'], $allowedTypes, true)) {
        ap_flash('Tipo di file non supportato. Usa PDF, DOC, DOCX, JPG, PNG.', 'error');
        return;
    }
    $maxSize = 10 * 1024 * 1024; // 10MB
    if ($file['size'] > $maxSize) {
        ap_flash('File troppo grande. Massimo 10MB.', 'error');
        return;
    }
    $uploadDir = __DIR__ . '/../../uploads/digital/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $fileName = uniqid('digital_', true) . '_' . basename($file['name']);
    $filePath = $uploadDir . $fileName;
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        ap_flash('Errore nel salvataggio del file.', 'error');
        return;
    }
    $pdo = ap_db();
    $stmt = $pdo->prepare('UPDATE order_items SET digital_file_path = :path WHERE order_id = :order_id AND product_id = :product_id');
    $stmt->execute([
        ':path' => 'uploads/digital/' . $fileName,
        ':order_id' => $orderId,
        ':product_id' => $productId,
    ]);
    ap_flash('File allegato con successo.', 'success');
}

function ap_action_admin_save_user(): void
{
    if (!ap_auth_is_admin()) {
        ap_flash('Non autorizzato.', 'error');
        return;
    }
    $user = ap_auth_current_user();
    $id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : null;
    $name = trim($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL) ?: '';
    $role = trim($_POST['role'] ?? 'user');
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $password = trim($_POST['password'] ?? '');
    if ($name === '' || $email === '') {
        ap_flash('Nome ed email sono obbligatori.', 'error');
        return;
    }
    if (!in_array($role, ['user', 'admin'], true)) {
        $role = 'user';
    }
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1');
    $stmt->execute([
        ':email' => $email,
        ':id' => $id ?: 0,
    ]);
    if ($stmt->fetch()) {
        ap_flash('Email già utilizzata da un altro account.', 'error');
        return;
    }
    $data = [
        'name' => $name,
        'email' => $email,
        'role' => $role,
        'is_active' => $isActive,
    ];
    if ($password !== '' && strlen($password) >= 8) {
        $data['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
    } elseif (!$id) {
        ap_flash('Per nuovi utenti, inserisci una password di almeno 8 caratteri.', 'error');
        return;
    }
    $result = ap_save_user($data, $id);
    if ($result) {
        ap_flash($id ? 'Utente aggiornato.' : 'Utente creato.', 'success');
        // Log audit event
        ap_log_audit_event('admin_action', $id ? 'Utente aggiornato: ' . $name : 'Utente creato: ' . $name, $user['id'] ?? null, [
            'action' => $id ? 'user_updated' : 'user_created',
            'user_id' => $result,
            'user_name' => $name,
            'user_email' => $email,
            'changes' => $data
        ]);
    } else {
        ap_flash('Errore nel salvataggio dell\'utente.', 'error');
    }
}

function ap_action_admin_delete_user(): void
{
    if (!ap_auth_is_admin()) {
        ap_flash('Non autorizzato.', 'error');
        return;
    }
    $user = ap_auth_current_user();
    $id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
    if ($id <= 0) {
        ap_flash('Utente non trovato.', 'error');
        return;
    }
    // Prevent deleting self
    if ($id === (int) $user['id']) {
        ap_flash('Non puoi eliminare il tuo stesso account.', 'error');
        return;
    }
    $userToDelete = ap_find_user($id);
    if (!$userToDelete) {
        ap_flash('Utente non trovato.', 'error');
        return;
    }
    $result = ap_delete_user($id);
    if ($result) {
        ap_flash('Utente eliminato definitivamente.', 'warning');
        // Log audit event
        ap_log_audit_event('admin_action', 'Utente eliminato: ' . $userToDelete['name'] . ' (' . $userToDelete['email'] . ')', $user['id'] ?? null, [
            'action' => 'user_deleted',
            'deleted_user_id' => $id,
            'deleted_user_name' => $userToDelete['name'],
            'deleted_user_email' => $userToDelete['email']
        ]);
    } else {
        ap_flash('Errore durante l\'eliminazione dell\'utente.', 'error');
    }
}

function ap_action_admin_save_settings(): void
{
    if (!ap_auth_is_admin()) {
        ap_flash('Non autorizzato.', 'error');
        return;
    }
    $user = ap_auth_current_user();
    $settings = $_POST['settings'] ?? [];
    if (!is_array($settings)) {
        ap_flash('Dati non validi.', 'error');
        return;
    }
    foreach ($settings as $key => $value) {
        $type = $_POST['setting_types'][$key] ?? 'string';
        $description = $_POST['setting_descriptions'][$key] ?? null;
        ap_set_setting($key, $value, $type, $description);
    }
    ap_flash('Impostazioni salvate.', 'success');
    // Log audit event
    ap_log_audit_event('admin_action', 'Impostazioni sito aggiornate', $user['id'] ?? null, [
        'action' => 'settings_updated',
        'updated_settings' => array_keys($settings)
    ]);
}

// Force cache reload - added 2025-01-24

function ap_action_admin_bulk_update_products(): void
{
    if (!ap_auth_is_admin()) {
        ap_flash('Non autorizzato.', 'error');
        return;
    }
    $user = ap_auth_current_user();
    $productIds = $_POST['product_ids'] ?? [];
    $status = $_POST['status'] ?? '';
    
    if (!is_array($productIds) || empty($productIds)) {
        ap_flash('Nessun prodotto selezionato.', 'error');
        return;
    }
    
    if (!in_array($status, ['active', 'inactive'], true)) {
        ap_flash('Stato non valido.', 'error');
        return;
    }
    
    $pdo = ap_db();
    $isActive = $status === 'active' ? 1 : 0;
    
    // Update products in bulk
    $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
    $stmt = $pdo->prepare("UPDATE products SET is_active = ?, updated_at = NOW() WHERE id IN ($placeholders)");
    $params = array_merge([$isActive], $productIds);
    
    try {
        $stmt->execute($params);
        $affectedRows = $stmt->rowCount();
        
        $action = $status === 'active' ? 'attivati' : 'disattivati';
        ap_flash("$affectedRows prodotti $action.", 'success');
        
        // Log audit event
        ap_log_audit_event('admin_action', "Prodotti bulk $action: $affectedRows prodotti", $user['id'] ?? null, [
            'action' => 'bulk_product_status_update',
            'status' => $status,
            'product_ids' => $productIds,
            'affected_count' => $affectedRows
        ]);
        
        // Clear product cache
        ap_cache_forget_prefix('products_');
        
    } catch (Exception $e) {
        ap_flash('Errore durante l\'aggiornamento dei prodotti.', 'error');
    }
}

function ap_action_admin_bulk_delete_products(): void
{
    if (!ap_auth_is_admin()) {
        ap_flash('Non autorizzato.', 'error');
        return;
    }
    $user = ap_auth_current_user();
    $productIds = $_POST['product_ids'] ?? [];
    
    if (!is_array($productIds) || empty($productIds)) {
        ap_flash('Nessun prodotto selezionato.', 'error');
        return;
    }
    
    $pdo = ap_db();
    
    try {
        // Get product names for logging before deletion
        $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
        $stmt = $pdo->prepare("SELECT id, name FROM products WHERE id IN ($placeholders)");
        $stmt->execute($productIds);
        $productsToDelete = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Delete product custom fields first
        $stmt = $pdo->prepare("DELETE FROM product_custom_fields WHERE product_id IN ($placeholders)");
        $stmt->execute($productIds);
        
        // Delete products
        $stmt = $pdo->prepare("DELETE FROM products WHERE id IN ($placeholders)");
        $stmt->execute($productIds);
        $affectedRows = $stmt->rowCount();
        
        ap_flash("$affectedRows prodotti eliminati definitivamente.", 'warning');
        
        // Log audit event
        $productNames = array_column($productsToDelete, 'name');
        ap_log_audit_event('admin_action', "Prodotti bulk eliminati: " . implode(', ', $productNames), $user['id'] ?? null, [
            'action' => 'bulk_product_deletion',
            'product_ids' => $productIds,
            'product_names' => $productNames,
            'affected_count' => $affectedRows
        ]);
        
        // Clear product cache
        ap_cache_forget_prefix('products_');
        
    } catch (Exception $e) {
        ap_flash('Errore durante l\'eliminazione dei prodotti.', 'error');
    }
}

function ap_action_admin_duplicate_product(): void
{
    if (!ap_auth_is_admin()) {
        ap_flash('Non autorizzato.', 'error');
        return;
    }
    $user = ap_auth_current_user();
    $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
    
    if ($productId <= 0) {
        ap_flash('Prodotto non trovato.', 'error');
        return;
    }
    
    $originalProduct = ap_find_product($productId);
    if (!$originalProduct) {
        ap_flash('Prodotto non trovato.', 'error');
        return;
    }
    
    $pdo = ap_db();
    
    try {
        // Duplicate the product
        $stmt = $pdo->prepare('
            INSERT INTO products (
                name, slug, description, price_cents, sku, stock, image_url, 
                category_key, fulfillment_type, is_active, created_at, updated_at
            ) 
            SELECT 
                CONCAT(name, " (copia)"), 
                CONCAT(slug, "-copia-", UNIX_TIMESTAMP()), 
                description, price_cents, sku, stock, image_url,
                category_key, fulfillment_type, 0, NOW(), NOW()
            FROM products 
            WHERE id = ?
        ');
        $stmt->execute([$productId]);
        $newProductId = $pdo->lastInsertId();
        
        // Duplicate custom fields
        $stmt = $pdo->prepare('
            INSERT INTO product_custom_fields (
                product_id, field_name, field_label, field_type, field_options, is_required, sort_order
            )
            SELECT ?, field_name, field_label, field_type, field_options, is_required, sort_order
            FROM product_custom_fields
            WHERE product_id = ?
        ');
        $stmt->execute([$newProductId, $productId]);
        
        ap_flash('Prodotto duplicato. Ora puoi modificarlo.', 'success');
        
        // Log audit event
        ap_log_audit_event('admin_action', 'Prodotto duplicato: ' . $originalProduct['name'] . ' → ID ' . $newProductId, $user['id'] ?? null, [
            'action' => 'product_duplicated',
            'original_product_id' => $productId,
            'original_product_name' => $originalProduct['name'],
            'new_product_id' => $newProductId
        ]);
        
        // Clear product cache
        ap_cache_forget_prefix('products_');
        
        // Redirect to edit the new product
        $redirectTo = $_POST['redirect_to'] ?? '?page=admin&section=catalogo';
        header('Location: ' . $redirectTo . '&section=edit_product&id=' . $newProductId);
        exit;
        
    } catch (Exception $e) {
        ap_flash('Errore durante la duplicazione del prodotto.', 'error');
    }
}

function ap_action_toggle_wishlist(): void
{
    $user = ap_auth_current_user();
    if (!$user) {
        ap_flash('Accedi per gestire la wishlist.', 'error');
        return;
    }

    $productId = (int) ($_POST['product_id'] ?? 0);
    if ($productId <= 0) {
        ap_flash('Prodotto non valido.', 'error');
        return;
    }

    if (ap_is_in_wishlist((int) $user['id'], $productId)) {
        // Remove from wishlist
        if (ap_remove_from_wishlist((int) $user['id'], $productId)) {
            ap_flash('Prodotto rimosso dalla wishlist.', 'success');
            ap_track_event('wishlist_remove', ['product_id' => $productId], (int) $user['id']);
        } else {
            ap_flash('Errore nella rimozione dalla wishlist.', 'error');
        }
    } else {
        // Add to wishlist
        if (ap_add_to_wishlist((int) $user['id'], $productId)) {
            ap_flash('Prodotto aggiunto alla wishlist.', 'success');
            ap_track_event('wishlist_add', ['product_id' => $productId], (int) $user['id']);
        } else {
            ap_flash('Errore nell\'aggiunta alla wishlist.', 'error');
        }
    }
}

function ap_action_remove_from_wishlist(): void
{
    $user = ap_auth_current_user();
    if (!$user) {
        ap_flash('Accedi per gestire la wishlist.', 'error');
        return;
    }

    $productId = (int) ($_POST['product_id'] ?? 0);
    if ($productId <= 0) {
        ap_flash('Prodotto non valido.', 'error');
        return;
    }

    if (ap_remove_from_wishlist((int) $user['id'], $productId)) {
        ap_flash('Prodotto rimosso dalla wishlist.', 'success');
        // Track analytics event
        ap_track_event('wishlist_remove', ['product_id' => $productId], (int) $user['id']);
    } else {
        ap_flash('Prodotto non trovato nella wishlist.', 'error');
    }
}

// === PRODUCT REVIEWS ACTIONS ===

function ap_action_add_product_review(): void
{
    $user = ap_auth_current_user();
    if (!$user) {
        ap_flash('Accedi per lasciare una recensione.', 'error');
        return;
    }

    $productId = (int) ($_POST['product_id'] ?? 0);
    $rating = (int) ($_POST['rating'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $review = trim($_POST['review'] ?? '');

    if ($productId <= 0) {
        ap_flash('Prodotto non valido.', 'error');
        return;
    }

    if ($rating < 1 || $rating > 5) {
        ap_flash('Valutazione non valida.', 'error');
        return;
    }

    if ($title === '' || $review === '') {
        ap_flash('Titolo e recensione sono obbligatori.', 'error');
        return;
    }

    if (!ap_can_user_review_product((int) $user['id'], $productId)) {
        ap_flash('Puoi recensire solo prodotti che hai acquistato.', 'error');
        return;
    }

    if (ap_add_product_review((int) $user['id'], $productId, $rating, $title, $review)) {
        ap_flash('Recensione pubblicata con successo.', 'success');
        // Track analytics event
        ap_track_event('review_added', [
            'product_id' => $productId,
            'rating' => $rating
        ], (int) $user['id']);
    } else {
        ap_flash('Errore nella pubblicazione della recensione.', 'error');
    }
}

// === NOTIFICATIONS ACTIONS ===

function ap_action_mark_notification_read(): void
{
    $user = ap_auth_current_user();
    if (!$user) {
        ap_flash('Accedi per gestire le notifiche.', 'error');
        return;
    }

    $notificationId = (int) ($_POST['notification_id'] ?? 0);
    if ($notificationId <= 0) {
        ap_flash('Notifica non valida.', 'error');
        return;
    }

    if (ap_mark_notification_read($notificationId, (int) $user['id'])) {
        // No flash message needed for this action
    } else {
        ap_flash('Notifica non trovata.', 'error');
    }
}

function ap_action_mark_all_notifications_read(): void
{
    $user = ap_auth_current_user();
    if (!$user) {
        ap_flash('Accedi per gestire le notifiche.', 'error');
        return;
    }

    if (ap_mark_all_notifications_read((int) $user['id'])) {
        ap_flash('Tutte le notifiche sono state contrassegnate come lette.', 'success');
    } else {
        ap_flash('Errore nell\'aggiornamento delle notifiche.', 'error');
    }
}

// === ANALYTICS ACTIONS ===

function ap_action_track_event(): void
{
    $user = ap_auth_current_user();
    $eventType = trim($_POST['event_type'] ?? '');
    $eventData = $_POST['event_data'] ?? [];

    if ($eventType === '') {
        return; // Silent fail for invalid requests
    }

    if (!is_array($eventData)) {
        $eventData = [];
    }

    ap_track_event($eventType, $eventData, $user ? (int) $user['id'] : null);
}
