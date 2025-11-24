<?php
$items = ap_cart_items();
$total = array_sum(array_column($items, 'subtotal_cents'));
$coupon = ap_cart_coupon();
$discount = 0;
if ($coupon) {
    $couponValidation = ap_coupon_validate($coupon, $total);
    if ($couponValidation !== null) {
        ap_cart_clear_coupon();
        $coupon = null;
    } else {
        $discount = ap_coupon_discount($coupon, $total);
    }
}
$shippingOptions = ap_shipping_methods();
$defaultShippingKey = 'standard';
$defaultShipping = $shippingOptions[$defaultShippingKey] ?? reset($shippingOptions) ?: ['label' => 'Spedizione', 'price_cents' => 0];
$netSubtotal = max(0, $total - $discount);
$estimatedTotal = $netSubtotal + (int) $defaultShipping['price_cents'];
$currentUrl = '?page=cart';
$user = ap_auth_current_user();
$addresses = $user ? ap_fetch_addresses((int) $user['id']) : [];
$defaultAddressId = $addresses[0]['id'] ?? 0;
$cartContainerClass = empty($items) ? 'container' : 'container-xxl';

// Check if all products are digital
$isAllDigital = !empty($items) && array_reduce($items, function($carry, $item) {
    $product = ap_find_product((int) $item['product_id']);
    return $carry && $product && strtolower((string) $product['fulfillment_type']) === 'digital';
}, true);

// Adjust shipping for digital products
if ($isAllDigital) {
    $defaultShipping['price_cents'] = 0;
    $estimatedTotal = $netSubtotal;
}

$hasDigitalProducts = false;
$digitalCustomFields = [];
foreach ($items as $item) {
    $product = ap_find_product((int) $item['product_id']);
    if ($product && strtolower((string) $product['fulfillment_type']) === 'digital') {
        $hasDigitalProducts = true;
        $customFields = ap_fetch_product_custom_fields((int) $item['product_id']);
        if (!empty($customFields)) {
            $digitalCustomFields[(int) $item['product_id']] = $customFields;
        }
    }
}
$italianProvinces = [
    'Agrigento', 'Alessandria', 'Ancona', 'Aosta', 'Arezzo', 'Ascoli Piceno', 'Asti', 'Avellino', 'Bari', 'Barletta-Andria-Trani', 'Belluno', 'Benevento', 'Bergamo', 'Biella', 'Bologna', 'Bolzano', 'Brescia', 'Brindisi', 'Cagliari', 'Caltanissetta', 'Campobasso', 'Carbonia-Iglesias', 'Caserta', 'Catania', 'Catanzaro', 'Chieti', 'Como', 'Cosenza', 'Cremona', 'Crotone', 'Cuneo', 'Enna', 'Fermo', 'Ferrara', 'Firenze', 'Foggia', 'Forlì-Cesena', 'Frosinone', 'Genova', 'Gorizia', 'Grosseto', 'Imperia', 'Isernia', 'La Spezia', 'L\'Aquila', 'Latina', 'Lecce', 'Lecco', 'Livorno', 'Lodi', 'Lucca', 'Macerata', 'Mantova', 'Massa-Carrara', 'Matera', 'Medio Campidano', 'Messina', 'Milano', 'Modena', 'Monza e della Brianza', 'Napoli', 'Novara', 'Nuoro', 'Ogliastra', 'Olbia-Tempio', 'Oristano', 'Padova', 'Palermo', 'Parma', 'Pavia', 'Perugia', 'Pesaro e Urbino', 'Pescara', 'Piacenza', 'Pisa', 'Pistoia', 'Pordenone', 'Potenza', 'Prato', 'Ragusa', 'Ravenna', 'Reggio Calabria', 'Reggio Emilia', 'Rieti', 'Rimini', 'Roma', 'Rovigo', 'Salerno', 'Sassari', 'Savona', 'Siena', 'Siracusa', 'Sondrio', 'Taranto', 'Teramo', 'Terni', 'Torino', 'Trapani', 'Trento', 'Treviso', 'Trieste', 'Udine', 'Varese', 'Venezia', 'Verbano-Cusio-Ossola', 'Vercelli', 'Verona', 'Vibo Valentia', 'Vicenza', 'Viterbo'
];
$comuniData = json_decode(file_get_contents(__DIR__ . '/../comuni.json'), true);
$italianComuni = array_column($comuniData, 'nome');
sort($italianComuni);
?>
<section class="cart-section section-padding">
    <div class="<?php echo $cartContainerClass; ?>">
        <div class="section-heading mb-4 text-center">
            <p class="eyebrow mb-2">Carrello</p>
            <h2 class="mb-1">Riepilogo prodotti</h2>
            <p class="text-muted">Aggiorna quantità o rimuovi i servizi che non ti servono più.</p>
        </div>
        <?php if (empty($items)): ?>
            <div class="empty-state p-5 text-center">
                <h3 class="mb-3">Il carrello è vuoto</h3>
                <p class="text-muted mb-4">Aggiungi i servizi che ti interessano e torna qui per completare l'ordine.</p>
                <a class="ap-btn ap-btn--primary" href="?page=shop">Vai allo shop</a>
            </div>
        <?php else: ?>
            <div class="row g-5">
                <div class="col-lg-7">
                    <form method="post" class="cart-items-form card shadow-sm">
                        <input type="hidden" name="ap_action" value="update_cart">
                        <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($currentUrl, ENT_QUOTES); ?>">
                        <div class="card-body">
                            <?php $lastItemIndex = array_key_last($items); ?>
                            <?php foreach ($items as $index => $item): ?>
                                <div class="cart-item row g-3 align-items-center py-3 <?php echo $index === $lastItemIndex ? '' : 'border-bottom'; ?>">
                                    <div class="col-sm-3">
                                        <div class="ratio ratio-4x3 rounded bg-light" style="background-image: url('<?php echo htmlspecialchars($item['image_url'], ENT_QUOTES); ?>'); background-size: cover;"></div>
                                    </div>
                                    <div class="col-sm-5">
                                        <p class="fw-semibold mb-1"><?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?></p>
                                        <small class="text-muted d-block">Prezzo unitario: <?php echo ap_price_format((int) $item['price_cents']); ?></small>
                                        <small class="text-muted">Subtotal: <?php echo ap_price_format((int) $item['subtotal_cents']); ?></small>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label small" for="qty-<?php echo (int) $item['product_id']; ?>">Quantità</label>
                                        <input class="form-control" type="number" id="qty-<?php echo (int) $item['product_id']; ?>" name="items[<?php echo (int) $item['product_id']; ?>]" min="0" value="<?php echo (int) $item['quantity']; ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="d-flex justify-content-between align-items-center pt-3">
                                <p class="mb-0 text-muted">Imposta 0 per rimuovere un servizio.</p>
                                <button class="ap-btn ap-btn--ghost" type="submit">Aggiorna carrello</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="border rounded-3 p-3 mb-4 bg-light-subtle">
                                <?php if ($coupon && $discount > 0): ?>
                                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                                        <div>
                                            <p class="fw-semibold mb-0">Coupon <?php echo htmlspecialchars($coupon['code'], ENT_QUOTES); ?></p>
                                            <?php if (!empty($coupon['description'])): ?>
                                                <small class="text-muted d-block"><?php echo htmlspecialchars($coupon['description'], ENT_QUOTES); ?></small>
                                            <?php endif; ?>
                                            <small class="text-success d-block">Sconto applicato: <?php echo ap_price_format($discount); ?></small>
                                        </div>
                                        <form method="post" class="ms-auto">
                                            <input type="hidden" name="ap_action" value="remove_coupon">
                                            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($currentUrl, ENT_QUOTES); ?>">
                                            <button class="btn btn-sm btn-outline-secondary" type="submit">Rimuovi</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <form method="post" class="row g-2 align-items-end">
                                        <input type="hidden" name="ap_action" value="apply_coupon">
                                        <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($currentUrl, ENT_QUOTES); ?>">
                                        <div class="col-12 col-md-8">
                                            <label class="form-label" for="coupon_code">Buono sconto</label>
                                            <input class="form-control" type="text" id="coupon_code" name="coupon_code" placeholder="CODICEPROMO">
                                        </div>
                                        <div class="col-12 col-md-4 d-grid">
                                            <label class="form-label visually-hidden" for="coupon_code_btn">Applica</label>
                                            <button class="ap-btn ap-btn--ghost w-100" type="submit" id="coupon_code_btn">Applica</button>
                                        </div>
                                        <div class="col-12">
                                            <small class="text-muted">I coupon sono soggetti a disponibilità e condizioni interne.</small>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <h3 class="h5 mb-4">Checkout rapido</h3>
                            <form method="post" class="checkout-form" enctype="multipart/form-data">
                                <input type="hidden" name="ap_action" value="checkout">
                                <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($currentUrl, ENT_QUOTES); ?>">
                                <?php if ($hasDigitalProducts && !empty($digitalCustomFields)): ?>
                                    <div class="card shadow-sm mb-4">
                                        <div class="card-body">
                                            <h5 class="card-title">Informazioni aggiuntive per prodotti digitali</h5>
                                            <p class="text-muted small">Compila i campi richiesti per i servizi digitali selezionati.</p>
                                            <?php foreach ($digitalCustomFields as $productId => $fields): ?>
                                                <?php $product = ap_find_product($productId); ?>
                                                <div class="mb-4">
                                                    <h6><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></h6>
                                                    <?php foreach ($fields as $field): ?>
                                                        <div class="mb-3">
                                                            <label class="form-label" for="custom_<?php echo (int) $productId; ?>_<?php echo (int) $field['id']; ?>">
                                                                <?php echo htmlspecialchars($field['field_label'], ENT_QUOTES); ?>
                                                                <?php if ((int) $field['is_required'] === 1): ?><span class="text-danger">*</span><?php endif; ?>
                                                            </label>
                                                            <?php if ($field['field_type'] === 'text'): ?>
                                                                <input class="form-control" type="text" id="custom_<?php echo (int) $productId; ?>_<?php echo (int) $field['id']; ?>" name="custom_fields[<?php echo (int) $productId; ?>][<?php echo (int) $field['id']; ?>]" <?php echo (int) $field['is_required'] === 1 ? 'required' : ''; ?>>
                                                            <?php elseif ($field['field_type'] === 'textarea'): ?>
                                                                <textarea class="form-control" id="custom_<?php echo (int) $productId; ?>_<?php echo (int) $field['id']; ?>" name="custom_fields[<?php echo (int) $productId; ?>][<?php echo (int) $field['id']; ?>]" rows="3" <?php echo (int) $field['is_required'] === 1 ? 'required' : ''; ?>></textarea>
                                                            <?php elseif ($field['field_type'] === 'select'): ?>
                                                                <?php
                                                                $options = explode("\n", $field['field_options']);
                                                                $allOptions = $options;
                                                                if ($field['field_name'] === 'province') {
                                                                    // Aggiungi città capoluogo per facilitare la ricerca
                                                                    $cities = [
                                                                        'Agrigento', 'Alessandria', 'Ancona', 'Aosta', 'Arezzo', 'Ascoli Piceno', 'Asti', 'Avellino', 'Bari', 'Barletta', 'Belluno', 'Benevento', 'Bergamo', 'Biella', 'Bologna', 'Bolzano', 'Brescia', 'Brindisi', 'Cagliari', 'Caltanissetta', 'Campobasso', 'Carbonia', 'Caserta', 'Catania', 'Catanzaro', 'Chieti', 'Como', 'Cosenza', 'Cremona', 'Crotone', 'Cuneo', 'Enna', 'Fermo', 'Ferrara', 'Firenze', 'Foggia', 'Forlì', 'Frosinone', 'Genova', 'Gorizia', 'Grosseto', 'Imperia', 'Isernia', 'La Spezia', 'L\'Aquila', 'Latina', 'Lecce', 'Lecco', 'Livorno', 'Lodi', 'Lucca', 'Macerata', 'Mantova', 'Massa', 'Matera', 'Villacidro', 'Messina', 'Milano', 'Modena', 'Monza', 'Napoli', 'Novara', 'Nuoro', 'Lanusei', 'Olbia', 'Oristano', 'Padova', 'Palermo', 'Parma', 'Pavia', 'Perugia', 'Pesaro', 'Pescara', 'Piacenza', 'Pisa', 'Pistoia', 'Pordenone', 'Potenza', 'Prato', 'Ragusa', 'Ravenna', 'Reggio Calabria', 'Reggio Emilia', 'Rieti', 'Rimini', 'Roma', 'Rovigo', 'Salerno', 'Sassari', 'Savona', 'Siena', 'Siracusa', 'Sondrio', 'Taranto', 'Teramo', 'Terni', 'Torino', 'Trapani', 'Trento', 'Treviso', 'Trieste', 'Udine', 'Varese', 'Venezia', 'Verbania', 'Vercelli', 'Verona', 'Vibo Valentia', 'Vicenza', 'Viterbo'
                                                                    ];
                                                                    $allOptions = array_unique(array_merge($options, $cities));
                                                                    sort($allOptions);
                                                                }
                                                                ?>
                                                                <input class="form-control" type="text" list="datalist_<?php echo (int) $productId; ?>_<?php echo (int) $field['id']; ?>" id="custom_<?php echo (int) $productId; ?>_<?php echo (int) $field['id']; ?>" name="custom_fields[<?php echo (int) $productId; ?>][<?php echo (int) $field['id']; ?>]" <?php echo (int) $field['is_required'] === 1 ? 'required' : ''; ?>>
                                                                <datalist id="datalist_<?php echo (int) $productId; ?>_<?php echo (int) $field['id']; ?>">
                                                                    <?php foreach ($allOptions as $option): ?>
                                                                        <option value="<?php echo htmlspecialchars(trim($option), ENT_QUOTES); ?>">
                                                                    <?php endforeach; ?>
                                                                </datalist>
                                                            <?php elseif ($field['field_type'] === 'checkbox'): ?>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="custom_<?php echo (int) $productId; ?>_<?php echo (int) $field['id']; ?>" name="custom_fields[<?php echo (int) $productId; ?>][<?php echo (int) $field['id']; ?>]" value="1">
                                                                    <label class="form-check-label" for="custom_<?php echo (int) $productId; ?>_<?php echo (int) $field['id']; ?>"><?php echo htmlspecialchars($field['field_label'], ENT_QUOTES); ?></label>
                                                                </div>
                                                            <?php elseif ($field['field_type'] === 'provincia'): ?>
                                                                <select class="form-select" id="custom_<?php echo (int) $productId; ?>_<?php echo (int) $field['id']; ?>" name="custom_fields[<?php echo (int) $productId; ?>][<?php echo (int) $field['id']; ?>]" <?php echo (int) $field['is_required'] === 1 ? 'required' : ''; ?>>
                                                                    <option value="">Seleziona provincia</option>
                                                                    <?php foreach ($italianProvinces as $prov): ?>
                                                                        <option value="<?php echo htmlspecialchars($prov, ENT_QUOTES); ?>"><?php echo htmlspecialchars($prov, ENT_QUOTES); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            <?php elseif ($field['field_type'] === 'comune'): ?>
                                                                <input class="form-control" type="text" list="datalist_comune_<?php echo (int) $productId; ?>_<?php echo (int) $field['id']; ?>" id="custom_<?php echo (int) $productId; ?>_<?php echo (int) $field['id']; ?>" name="custom_fields[<?php echo (int) $productId; ?>][<?php echo (int) $field['id']; ?>]" placeholder="Inserisci comune" <?php echo (int) $field['is_required'] === 1 ? 'required' : ''; ?>>
                                                                <datalist id="datalist_comune_<?php echo (int) $productId; ?>_<?php echo (int) $field['id']; ?>">
                                                                    <?php foreach ($italianComuni as $comune): ?>
                                                                        <option value="<?php echo htmlspecialchars($comune, ENT_QUOTES); ?>">
                                                                    <?php endforeach; ?>
                                                                </datalist>
                                                            <?php elseif ($field['field_type'] === 'cap' || $field['field_type'] === 'citta'): ?>
                                                                <input class="form-control" type="text" id="custom_<?php echo (int) $productId; ?>_<?php echo (int) $field['id']; ?>" name="custom_fields[<?php echo (int) $productId; ?>][<?php echo (int) $field['id']; ?>]" placeholder="Inserisci <?php echo $field['field_type']; ?>" <?php echo (int) $field['is_required'] === 1 ? 'required' : ''; ?>>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!$isAllDigital): ?>
                                    <?php if ($user && !empty($addresses)): ?>
                                        <div class="mb-3">
                                            <label class="form-label" for="shipping_address_id">Indirizzi salvati</label>
                                            <select class="form-select" id="shipping_address_id" name="shipping_address_id">
                                                <option value="0">Usa un nuovo indirizzo</option>
                                                <?php foreach ($addresses as $address): ?>
                                                    <option value="<?php echo (int) $address['id']; ?>" <?php echo (int) $address['id'] === (int) $defaultAddressId ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($address['label'] ?: $address['recipient_name'], ENT_QUOTES); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php endif; ?>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="shipping_name">Destinatario</label>
                                            <input class="form-control" type="text" id="shipping_name" name="shipping_name" value="<?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="shipping_phone">Telefono</label>
                                            <input class="form-control" type="tel" id="shipping_phone" name="shipping_phone" placeholder="+39">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label" for="shipping_address">Indirizzo</label>
                                            <input class="form-control" type="text" id="shipping_address" name="shipping_address" placeholder="Via e civico">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="shipping_city">Città</label>
                                            <input class="form-control" type="text" id="shipping_city" name="shipping_city" placeholder="Milano">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label" for="shipping_postal_code">CAP</label>
                                            <input class="form-control" type="text" id="shipping_postal_code" name="shipping_postal_code" placeholder="20100">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label" for="shipping_province">Provincia</label>
                                            <input class="form-control" type="text" id="shipping_province" name="shipping_province" placeholder="MI" maxlength="2">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="shipping_country">Paese</label>
                                            <input class="form-control" type="text" id="shipping_country" name="shipping_country" value="Italia">
                                        </div>
                                    </div>
                                    <div class="mb-3 mt-4">
                                        <span class="form-label d-block mb-2">Spedizione</span>
                                        <?php foreach ($shippingOptions as $key => $option): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="shipping_method" id="ship-<?php echo $key; ?>" value="<?php echo $key; ?>" data-price="<?php echo (int) $option['price_cents']; ?>" data-label="<?php echo htmlspecialchars($option['label'], ENT_QUOTES); ?>" <?php echo $key === $defaultShippingKey ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="ship-<?php echo $key; ?>">
                                                    <?php echo htmlspecialchars($option['label'], ENT_QUOTES); ?> — <?php echo ap_price_format((int) $option['price_cents']); ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="border rounded-3 p-3 mb-4 bg-light-subtle">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Prodotti</span>
                                        <strong><?php echo ap_price_format($total); ?></strong>
                                    </div>
                                    <?php if ($discount > 0): ?>
                                        <div class="d-flex justify-content-between mb-2 text-success">
                                            <span>Coupon <?php echo htmlspecialchars($coupon['code'], ENT_QUOTES); ?></span>
                                            <strong>-<?php echo ap_price_format($discount); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!$isAllDigital): ?>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span id="shipping-summary-label"><?php echo htmlspecialchars($defaultShipping['label'], ENT_QUOTES); ?></span>
                                            <strong id="shipping-summary-price" data-value="<?php echo (int) $defaultShipping['price_cents']; ?>"><?php echo ap_price_format((int) $defaultShipping['price_cents']); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <span>Totale <?php echo $isAllDigital ? 'digitale' : 'stimato'; ?></span>
                                        <strong id="checkout-total" data-subtotal="<?php echo (int) $netSubtotal; ?>" data-shipping="<?php echo (int) $defaultShipping['price_cents']; ?>"><?php echo ap_price_format($estimatedTotal); ?></strong>
                                    </div>
                                </div>
                                <?php if (function_exists('ap_klarna_messaging_enabled') && ap_klarna_messaging_enabled()): ?>
                                    <div class="klarna-onsite-wrapper small text-muted mb-4">
                                        <?php echo ap_render_klarna_messaging((int) $estimatedTotal, ap_klarna_messaging_placement('cart')); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="mb-3">
                                    <label class="form-label" for="shipping_notes">Note per la spedizione</label>
                                    <textarea class="form-control" id="shipping_notes" name="shipping_notes" rows="2" placeholder="Citofono, orari preferiti, ecc."></textarea>
                                </div>
                                <?php if ($user): ?>
                                    <div class="border rounded-3 p-3 mb-3">
                                        <p class="fw-semibold mb-2">Salva questo indirizzo</p>
                                        <div class="mb-2">
                                            <label class="form-label" for="address_label">Etichetta</label>
                                            <input class="form-control" type="text" id="address_label" name="address_label" placeholder="Es. Sede operativa">
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="save_shipping_address" name="save_shipping_address">
                                            <label class="form-check-label" for="save_shipping_address">Aggiungi alla rubrica personale</label>
                                        </div>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" id="mark_default_address" name="mark_default_address">
                                            <label class="form-check-label" for="mark_default_address">Imposta come predefinito</label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="mb-3">
                                    <span class="form-label d-block mb-2">Pagamento</span>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="pay-card" value="card" checked>
                                        <label class="form-check-label" for="pay-card">Carta (Stripe simulato) — conferma immediata</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="pay-paypal" value="paypal">
                                        <label class="form-check-label" for="pay-paypal">PayPal — conferma immediata</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="pay-klarna" value="klarna">
                                        <label class="form-check-label" for="pay-klarna">Klarna — paga ora o in 3 rate</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="pay-wire" value="wire">
                                        <label class="form-check-label" for="pay-wire">Bonifico bancario — spedizione dopo accredito</label>
                                    </div>
                                    <small class="text-muted d-block mt-2">Scegliendo Klarna verrai reindirizzato su una pagina protetta per completare il pagamento.</small>
                                </div>
                                <?php if (!$user): ?>
                                    <p class="small text-muted">Per completare il checkout è necessario accedere o registrarsi. Dopo l'invio verrai guidato al login.</p>
                                <?php endif; ?>
                                <button class="ap-btn ap-btn--primary w-100" type="submit">Conferma ordine</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php if ($user && !empty($addresses)): ?>
<script>
    (function () {
        const select = document.getElementById('shipping_address_id');
        if (!select) {
            return;
        }
        const dataset = <?php echo json_encode(array_map(function ($address) {
            return [
                'id' => (int) $address['id'],
                'recipient_name' => $address['recipient_name'],
                'street' => $address['street'],
                'city' => $address['city'],
                'postal_code' => $address['postal_code'],
                'province' => $address['province'],
                'country' => $address['country'],
                'phone' => $address['phone'],
            ];
        }, $addresses), JSON_UNESCAPED_UNICODE); ?>;
        const fillFields = (payload) => {
            const nameField = document.getElementById('shipping_name');
            if (payload.recipient_name && nameField) {
                nameField.value = payload.recipient_name;
            }
            document.getElementById('shipping_address').value = payload.street || '';
            document.getElementById('shipping_city').value = payload.city || '';
            document.getElementById('shipping_postal_code').value = payload.postal_code || '';
            document.getElementById('shipping_province').value = payload.province || '';
            document.getElementById('shipping_country').value = payload.country || 'Italia';
            document.getElementById('shipping_phone').value = payload.phone || '';
        };
        const reset = () => {
            document.getElementById('shipping_address').value = '';
            document.getElementById('shipping_city').value = '';
            document.getElementById('shipping_postal_code').value = '';
            document.getElementById('shipping_province').value = '';
            document.getElementById('shipping_country').value = 'Italia';
            document.getElementById('shipping_phone').value = '';
        };
        select.addEventListener('change', function () {
            const id = parseInt(this.value, 10);
            if (!id) {
                reset();
                return;
            }
            const matched = dataset.find((entry) => entry.id === id);
            if (matched) {
                fillFields(matched);
            }
        });
        if (parseInt(select.value, 10)) {
            const initial = dataset.find((entry) => entry.id === parseInt(select.value, 10));
            if (initial) {
                fillFields(initial);
            }
        }
    })();
</script>
<?php endif; ?>
<script>
    (function () {
        const shippingRadios = document.querySelectorAll('input[name="shipping_method"]');
        if (!shippingRadios.length) {
            return;
        }
        const shippingLabel = document.getElementById('shipping-summary-label');
        const shippingPrice = document.getElementById('shipping-summary-price');
        const totalElement = document.getElementById('checkout-total');
        if (!shippingLabel || !shippingPrice || !totalElement) {
            return;
        }
        const subtotal = parseInt(totalElement.dataset.subtotal || '0', 10);
        const formatter = new Intl.NumberFormat('it-IT', { style: 'currency', currency: 'EUR' });
        const updateSummary = (radio) => {
            const priceCents = parseInt(radio.dataset.price || '0', 10);
            const label = radio.dataset.label || '';
            shippingLabel.textContent = label;
            shippingPrice.dataset.value = String(priceCents);
            shippingPrice.textContent = formatter.format(priceCents / 100);
            totalElement.dataset.shipping = String(priceCents);
            totalElement.textContent = formatter.format((subtotal + priceCents) / 100);
        };
        shippingRadios.forEach((radio) => {
            radio.addEventListener('change', () => updateSummary(radio));
            if (radio.checked) {
                updateSummary(radio);
            }
        });
    })();
</script>
