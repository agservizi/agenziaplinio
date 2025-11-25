<?php
$business = $site['business'] ?? [];
$contactEmail = $business['email'] ?? 'info@agenziaplinio.it';
$contactPhone = $business['telephone'] ?? '+39 081 058 45 42';
$contactAddress = $business['address']['street'] ?? 'Via Plinio Il Vecchio, 72';
$contactCity = $business['address']['locality'] ?? 'Castellammare di Stabia (NA)';
$contactPostal = $business['address']['postal_code'] ?? '80053';
$defaultHours = "Lun-Ven · 08:30 — 19:15 (chiuso 13:15 — 16:00)\nSab · 09:15 — 13:00\nDomenica: Chiuso";
$storedHours = trim($business['opening_hours'] ?? '');
$legacyHours = 'Mo-Fr 08:30-19:00, Sa 09:00-13:00';
$contactHours = $defaultHours; // default to the new schedule
if ($storedHours !== '' && $storedHours !== $legacyHours) {
    $contactHours = $storedHours; // allow future overrides while blocking the legacy string
}
$sidebarRedirect = $_SERVER['REQUEST_URI'] ?? '?page=shop';
if (strpos($sidebarRedirect, '?') !== false) {
    $sidebarRedirect .= '&cart_open=1';
} else {
    $sidebarRedirect .= '?cart_open=1';
}
?>
<div id="cart-sidebar" class="cart-sidebar" aria-hidden="true">
    <div class="cart-sidebar__overlay" data-cart-sidebar-close></div>
    <aside class="cart-sidebar__panel" role="dialog" aria-modal="true" aria-labelledby="cart-sidebar-title" tabindex="-1">
        <header class="cart-sidebar__header">
            <div>
                <p class="cart-sidebar__eyebrow">Il tuo ordine</p>
                <h2 id="cart-sidebar-title">Riepilogo rapido</h2>
            </div>
            <button type="button" class="cart-sidebar__close" data-cart-sidebar-close aria-label="Chiudi pannello">&times;</button>
        </header>
        <section class="cart-sidebar__section">
            <?php if (!empty($cartItems)): ?>
                <ul class="cart-sidebar__items list-unstyled mb-0">
                    <?php foreach ($cartItems as $item): ?>
                        <li class="cart-sidebar__item">
                            <div class="cart-sidebar__item-info">
                                <strong><?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?></strong>
                                <form method="post" class="cart-sidebar__quantity-form">
                                    <input type="hidden" name="ap_action" value="update_cart">
                                    <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($sidebarRedirect, ENT_QUOTES); ?>">
                                    <label class="visually-hidden" for="qty-<?php echo (int) $item['product_id']; ?>">Quantità per <?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?></label>
                                    <input type="number" id="qty-<?php echo (int) $item['product_id']; ?>" name="items[<?php echo (int) $item['product_id']; ?>]" value="<?php echo (int) $item['quantity']; ?>" min="0" max="<?php echo (int) $item['stock']; ?>" class="cart-sidebar__quantity-input">
                                    <span>&middot; <?php echo ap_price_format((int) $item['price_cents']); ?></span>
                                </form>
                            </div>
                            <div class="cart-sidebar__item-actions">
                                <span class="cart-sidebar__item-price"><?php echo ap_price_format((int) $item['subtotal_cents']); ?></span>
                                <form method="post" class="cart-sidebar__remove-form">
                                    <input type="hidden" name="ap_action" value="update_cart">
                                    <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($sidebarRedirect, ENT_QUOTES); ?>">
                                    <input type="hidden" name="items[<?php echo (int) $item['product_id']; ?>]" value="0">
                                    <button type="submit" class="cart-sidebar__remove-button" aria-label="Rimuovi <?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?>">
                                        Rimuovi
                                    </button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="cart-sidebar__empty">Nessun articolo nel carrello al momento.</p>
            <?php endif; ?>
            <div class="cart-sidebar__totals">
                <div class="cart-sidebar__total-row">
                    <span>Subtotale</span>
                    <strong><?php echo ap_price_format((int) $cartSubtotal); ?></strong>
                </div>
                <?php if ((int) $cartDiscount > 0): ?>
                    <div class="cart-sidebar__total-row is-discount">
                        <span>Sconto</span>
                        <strong>-<?php echo ap_price_format((int) $cartDiscount); ?></strong>
                    </div>
                <?php endif; ?>
                <div class="cart-sidebar__total-row is-total">
                    <span>Totale</span>
                    <strong><?php echo ap_price_format((int) $cartTotal); ?></strong>
                </div>
            </div>
            <div class="cart-sidebar__actions">
                <form method="post" class="cart-sidebar__update-form">
                    <input type="hidden" name="ap_action" value="update_cart">
                    <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($sidebarRedirect, ENT_QUOTES); ?>">
                    <button type="submit" class="cart-sidebar__update-button">Aggiorna carrello</button>
                </form>
                <div class="cart-sidebar__secondary-actions">
                    <form method="post" class="cart-sidebar__clear-form">
                        <input type="hidden" name="ap_action" value="clear_cart">
                        <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($sidebarRedirect, ENT_QUOTES); ?>">
                        <button type="submit" class="cart-sidebar__clear-button" onclick="return confirm('Sei sicuro di voler svuotare il carrello?')">Svuota carrello</button>
                    </form>
                    <a class="cart-sidebar__cta" href="?page=cart">
                        Vai al carrello
                    </a>
                </div>
            </div>
        </section>
        <section class="cart-sidebar__section">
            <p class="cart-sidebar__eyebrow">Serve aiuto?</p>
            <ul class="cart-sidebar__info list-unstyled mb-0">
                <li><span>Email:</span> <a href="mailto:<?php echo htmlspecialchars($contactEmail, ENT_QUOTES); ?>"><?php echo htmlspecialchars($contactEmail, ENT_QUOTES); ?></a></li>
                <li><span>Telefono:</span> <a href="tel:<?php echo htmlspecialchars($contactPhone, ENT_QUOTES); ?>"><?php echo htmlspecialchars($contactPhone, ENT_QUOTES); ?></a></li>
                <li><span>Indirizzo:</span> <?php echo htmlspecialchars($contactAddress . ', ' . $contactPostal . ' ' . $contactCity, ENT_QUOTES); ?></li>
                <li>
                    <span>Orari:</span>
                    <div class="cart-sidebar__hours"><?php echo nl2br(htmlspecialchars($contactHours, ENT_QUOTES)); ?></div>
                </li>
            </ul>
        </section>
    </aside>
</div>
