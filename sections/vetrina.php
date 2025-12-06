<?php
$showcaseSource = ap_fetch_products(['sort' => 'newest']);
$showcaseProducts = array_slice(array_values(array_filter($showcaseSource, static function ($product) {
    return (int) ($product['is_active'] ?? 1) === 1;
})), 0, 4);
$showcaseMetrics = ap_shop_metrics($showcaseSource);
$redirectTarget = $_SERVER['REQUEST_URI'] ?? '?page=home';
?>
<section class="ap-section ap-section--market" id="vetrina">
    <div class="container">
        <div class="ap-market__heading" data-reveal>
            <div>
                <span class="ap-eyebrow">Vetrina attiva</span>
                <h2>Servizi pronti all'acquisto con fulfillment garantito</h2>
                <p>Ogni card racconta SLA, stock e value proposition. Aggiorniamo la selezione più volte al giorno per mantenere il feed sempre rilevante.</p>
            </div>
            <div class="ap-section__actions">
                <a class="ap-btn ap-btn--primary" href="?page=shop">Apri shop completo</a>
                <a class="ap-btn ap-btn--ghost" href="?page=cart">Vai al carrello</a>
            </div>
        </div>

        <div class="ap-market__stats" data-reveal>
            <article>
                <strong><?php echo $showcaseMetrics['available']; ?></strong>
                <small>servizi disponibili</small>
            </article>
            <article>
                <strong><?php echo $showcaseMetrics['low_stock']; ?></strong>
                <small>in low stock</small>
            </article>
            <article>
                <strong><?php echo $showcaseMetrics['sold_out']; ?></strong>
                <small>sold out monitorati</small>
            </article>
            <article>
                <strong><?php echo ap_price_format($showcaseMetrics['average_price']); ?></strong>
                <small>ticket medio</small>
            </article>
        </div>

        <?php if (!empty($showcaseProducts)): ?>
            <?php
            $heroProduct = $showcaseProducts[0];
            $heroSlug = $heroProduct['slug'] ?? '';
            $heroUrl = $heroSlug !== ''
                ? '?page=product&slug=' . urlencode($heroSlug)
                : '?page=product&id=' . (int) $heroProduct['id'];
            $heroBadges = ap_product_badges($heroProduct);
            $heroHighlights = ap_product_highlights($heroProduct);
            $heroStock = (int) $heroProduct['stock'];
            $heroStockLabel = $heroStock === 0
                ? 'Sold out'
                : ($heroStock < 5 ? 'Ultimi ' . $heroStock . ' pezzi' : 'Disponibile subito');
            ?>
            <div class="ap-market__hero" data-reveal>
                <article class="ap-market__hero-card">
                    <div class="ap-market__chip">Focus product</div>
                    <div class="ap-market__hero-head">
                        <div>
                            <p><?php echo htmlspecialchars(ap_product_category_label($heroProduct), ENT_QUOTES); ?></p>
                            <h3><?php echo htmlspecialchars($heroProduct['name'], ENT_QUOTES); ?></h3>
                        </div>
                        <span><?php echo ap_price_format((int) $heroProduct['price_cents']); ?></span>
                    </div>
                    <p><?php echo htmlspecialchars($heroProduct['description'], ENT_QUOTES); ?></p>
                    <?php if ($heroHighlights): ?>
                        <ul>
                            <?php foreach (array_slice($heroHighlights, 0, 3) as $highlight): ?>
                                <li><?php echo htmlspecialchars($highlight, ENT_QUOTES); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <div class="ap-market__hero-meta">
                        <span><?php echo htmlspecialchars($heroStockLabel, ENT_QUOTES); ?></span>
                        <div class="ap-market__hero-badges">
                            <?php foreach ($heroBadges as $badge): ?>
                                <span class="ap-badge ap-badge--<?php echo htmlspecialchars($badge['variant'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($badge['label'], ENT_QUOTES); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="ap-market__hero-actions">
                        <a class="ap-btn ap-btn--secondary" href="<?php echo htmlspecialchars($heroUrl, ENT_QUOTES); ?>">Dettagli</a>
                        <form method="post">
                            <input type="hidden" name="ap_action" value="add_to_cart">
                            <input type="hidden" name="product_id" value="<?php echo (int) $heroProduct['id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($redirectTarget, ENT_QUOTES); ?>">
                            <button type="submit" class="ap-btn ap-btn--primary" <?php echo $heroStock === 0 ? 'disabled' : ''; ?>>Metti nel carrello</button>
                        </form>
                    </div>
                </article>
                <div class="ap-market__hero-video">
                    <p>Workflow live demo</p>
                    <span>02:45</span>
                    <button type="button" aria-label="Riproduci video" data-modal-open="service-modal">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
            </div>

            <?php $otherProducts = array_slice($showcaseProducts, 1); ?>
            <?php if ($otherProducts): ?>
                <div class="ap-market__list" data-reveal>
                    <?php foreach ($otherProducts as $product): ?>
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
                        <article class="ap-market__row <?php echo $stockState; ?>">
                            <div class="ap-market__row-head">
                                <span class="ap-chip"><?php echo htmlspecialchars(ap_product_category_label($product), ENT_QUOTES); ?></span>
                                <h4><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></h4>
                            </div>
                            <p><?php echo htmlspecialchars($product['description'], ENT_QUOTES); ?></p>
                            <?php if ($highlights): ?>
                                <ul>
                                    <?php foreach (array_slice($highlights, 0, 2) as $highlight): ?>
                                        <li><?php echo htmlspecialchars($highlight, ENT_QUOTES); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            <div class="ap-market__row-meta">
                                <span class="price"><?php echo ap_price_format((int) $product['price_cents']); ?></span>
                                <?php if (!empty($product['sku'])): ?>
                                    <span class="sku">SKU <?php echo htmlspecialchars($product['sku'], ENT_QUOTES); ?></span>
                                <?php endif; ?>
                                <span class="stock"><?php echo htmlspecialchars($stockLabel, ENT_QUOTES); ?></span>
                            </div>
                            <div class="ap-market__row-actions">
                                <a class="ap-link" href="<?php echo htmlspecialchars($productUrl, ENT_QUOTES); ?>">Scheda</a>
                                <form method="post">
                                    <input type="hidden" name="ap_action" value="add_to_cart">
                                    <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($redirectTarget, ENT_QUOTES); ?>">
                                    <button type="submit" class="ap-btn ap-btn--ghost" <?php echo $stock === 0 ? 'disabled' : ''; ?>>Aggiungi</button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="ap-empty" data-reveal>
                <p>Nessun servizio attivo in vetrina al momento.</p>
                <a class="ap-btn ap-btn--ghost" href="?page=shop">Guarda tutto il catalogo</a>
            </div>
        <?php endif; ?>
    </div>
</section>
