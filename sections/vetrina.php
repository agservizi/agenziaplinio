<?php
$showcaseSource = ap_fetch_products(['sort' => 'newest']);
$showcaseProducts = array_slice(array_values(array_filter($showcaseSource, static function ($product) {
    return (int) ($product['is_active'] ?? 1) === 1;
})), 0, 4);
$showcaseMetrics = ap_shop_metrics($showcaseSource);
$redirectTarget = $_SERVER['REQUEST_URI'] ?? '?page=home';
?>
<section class="ap-section ap-section--shop" id="vetrina">
    <div class="container">
        <div class="ap-section__heading" data-reveal>
            <span class="ap-eyebrow">Vetrina attiva</span>
            <div class="ap-section__heading-row">
                <div>
                    <h2>Servizi pronti all'acquisto</h2>
                    <p>Ricariche multicanale, identità digitali, logistica smart e pacchetti business disponibili subito.</p>
                </div>
                <div class="ap-section__actions">
                    <a class="ap-btn ap-btn--primary" href="?page=shop">Apri shop completo</a>
                    <a class="ap-btn ap-btn--ghost" href="?page=cart">Vai al carrello</a>
                </div>
            </div>
        </div>

        <?php if (!empty($showcaseProducts)): ?>
            <div class="ap-grid ap-grid--shop">
                <?php foreach ($showcaseProducts as $product): ?>
                    <?php
                    $productSlug = $product['slug'] ?? '';
                    $productUrl = $productSlug !== ''
                        ? '?page=product&slug=' . urlencode($productSlug)
                        : '?page=product&id=' . (int) $product['id'];
                    $badges = ap_product_badges($product);
                    $highlights = ap_product_highlights($product);
                    $stock = (int) $product['stock'];
                    $stockState = $stock === 0 ? 'is-out' : ($stock < 5 ? 'is-low' : 'is-ok');
                    $stockLabel = $stock === 0
                        ? 'Sold out'
                        : ($stock < 5 ? 'Ultimi ' . $stock . ' pezzi' : 'Disponibile subito');
                    ?>
                    <article class="ap-product <?php echo $stockState; ?>" data-reveal>
                        <header class="ap-product__head">
                            <span class="ap-chip"><?php echo htmlspecialchars(ap_product_category_label($product), ENT_QUOTES); ?></span>
                            <?php if ($badges): ?>
                                <div class="ap-product__badges">
                                    <?php foreach ($badges as $badge): ?>
                                        <span class="ap-badge ap-badge--<?php echo htmlspecialchars($badge['variant'], ENT_QUOTES); ?>">
                                            <?php echo htmlspecialchars($badge['label'], ENT_QUOTES); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </header>
                        <div class="ap-product__body">
                            <h3><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></h3>
                            <p><?php echo htmlspecialchars($product['description'], ENT_QUOTES); ?></p>
                            <?php if ($highlights): ?>
                                <ul>
                                    <?php foreach (array_slice($highlights, 0, 3) as $highlight): ?>
                                        <li><?php echo htmlspecialchars($highlight, ENT_QUOTES); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                        <div class="ap-product__meta">
                            <div>
                                <span class="ap-product__price"><?php echo ap_price_format((int) $product['price_cents']); ?></span>
                                <?php if (!empty($product['sku'])): ?>
                                    <span class="ap-product__sku">SKU <?php echo htmlspecialchars($product['sku'], ENT_QUOTES); ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="ap-product__stock"><?php echo htmlspecialchars($stockLabel, ENT_QUOTES); ?></span>
                        </div>
                        <div class="ap-product__actions">
                            <a class="ap-link" href="<?php echo htmlspecialchars($productUrl, ENT_QUOTES); ?>">Dettagli</a>
                            <form method="post">
                                <input type="hidden" name="ap_action" value="add_to_cart">
                                <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($redirectTarget, ENT_QUOTES); ?>">
                                <button type="submit" class="ap-btn ap-btn--secondary" <?php echo $stock === 0 ? 'disabled' : ''; ?>>Aggiungi</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="ap-empty" data-reveal>
                <p>Nessun servizio attivo in vetrina al momento.</p>
                <a class="ap-btn ap-btn--ghost" href="?page=shop">Guarda tutto il catalogo</a>
            </div>
        <?php endif; ?>
    </div>
</section>
