<?php
$showcaseSource = ap_fetch_products(['sort' => 'newest']);
$showcaseProducts = array_slice(array_values(array_filter($showcaseSource, static function ($product) {
    return (int) ($product['is_active'] ?? 1) === 1;
})), 0, 4);
$showcaseMetrics = ap_shop_metrics($showcaseSource);
$redirectTarget = $_SERVER['REQUEST_URI'] ?? '?page=home';
?>
<section class="mini-shop-section section-padding" id="vetrina">
    <div class="container">
        <div class="mini-shop__intro" data-reveal>
            <div>
                <p class="eyebrow">Vetrina rapida</p>
                <h2>Prodotti e servizi pronti all'acquisto</h2>
                <p class="mini-shop__lead">Mostriamo qui i pacchetti più richiesti: attivazioni digitali, spedizioni e servizi business che puoi mettere subito nel carrello.</p>
            </div>
            <div class="mini-shop__actions">
                <a class="ap-btn ap-btn--primary" href="?page=shop">Apri lo shop completo</a>
                <a class="ap-btn ap-btn--ghost" href="?page=cart">Vai al carrello</a>
            </div>
        </div>
        <?php if (!empty($showcaseProducts)): ?>
            <div class="mini-shop__grid">
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
                    <article class="mini-shop-card <?php echo $stockState; ?>" data-reveal>
                        <div class="mini-shop-card__head">
                            <span class="badge bg-dark text-white"><?php echo htmlspecialchars(ap_product_category_label($product), ENT_QUOTES); ?></span>
                            <?php if ($badges): ?>
                                <div class="mini-shop-card__badges">
                                    <?php foreach ($badges as $badge): ?>
                                        <span class="badge bg-<?php echo htmlspecialchars($badge['variant'], ENT_QUOTES); ?>-subtle text-<?php echo htmlspecialchars($badge['variant'], ENT_QUOTES); ?>">
                                            <?php echo htmlspecialchars($badge['label'], ENT_QUOTES); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h3 class="mini-shop-card__title"><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></h3>
                        <p class="mini-shop-card__excerpt"><?php echo htmlspecialchars($product['description'], ENT_QUOTES); ?></p>
                        <?php if ($highlights): ?>
                            <ul class="mini-shop-card__highlights">
                                <?php foreach (array_slice($highlights, 0, 3) as $highlight): ?>
                                    <li><?php echo htmlspecialchars($highlight, ENT_QUOTES); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <div class="mini-shop-card__meta">
                            <div>
                                <span class="mini-shop-card__price"><?php echo ap_price_format((int) $product['price_cents']); ?></span>
                                <?php if (!empty($product['sku'])): ?>
                                    <span class="mini-shop-card__sku">SKU <?php echo htmlspecialchars($product['sku'], ENT_QUOTES); ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="mini-shop-card__stock"><?php echo htmlspecialchars($stockLabel, ENT_QUOTES); ?></span>
                        </div>
                        <div class="mini-shop-card__actions">
                            <a class="mini-shop-card__link" href="<?php echo htmlspecialchars($productUrl, ENT_QUOTES); ?>">Dettagli</a>
                            <form method="post" class="mini-shop-card__form">
                                <input type="hidden" name="ap_action" value="add_to_cart">
                                <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($redirectTarget, ENT_QUOTES); ?>">
                                <button type="submit" class="mini-shop-card__cart-btn" <?php echo $stock === 0 ? 'disabled' : ''; ?>>Aggiungi</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="mini-shop__empty" data-reveal>
                <p>Nessun servizio attivo in vetrina al momento.</p>
                <a class="ap-btn ap-btn--ghost" href="?page=shop">Guarda tutto il catalogo</a>
            </div>
        <?php endif; ?>
    </div>
</section>
