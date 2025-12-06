<?php
$showcaseSource = ap_fetch_products(['sort' => 'newest']);
$showcaseProducts = array_slice(array_values(array_filter($showcaseSource, static function ($product) {
    return (int) ($product['is_active'] ?? 1) === 1;
})), 0, 6);
$showcaseMetrics = ap_shop_metrics($showcaseSource);
$redirectTarget = $_SERVER['REQUEST_URI'] ?? '?page=home';
$filterKeys = [];
$categoryMap = ap_product_category_map();
foreach ($showcaseProducts as $product) {
    $key = ap_product_category_key($product);
    if (!in_array($key, $filterKeys, true)) {
        $filterKeys[] = $key;
    }
}
?>
<section class="ap-section ap-section--market ap-section--widescreen" id="vetrina" data-theme="night">
    <div class="ap-market__hero" data-parallax-container>
        <div class="ap-market__hero-bg" aria-hidden="true"></div>
        <div class="ap-market__hero-content container">
            <div data-reveal="fade-up">
                <span class="ap-eyebrow">Vetrina attiva</span>
                <h1 data-reveal-text>Servizi pronti all'acquisto con fulfillment garantito</h1>
                <p>Ogni card racconta SLA, stock e value proposition. Aggiorniamo la selezione più volte al giorno per mantenere il feed sempre rilevante.</p>
            </div>
            <div class="ap-market__metrics" data-reveal>
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
            <div class="ap-market__filters" data-reveal>
                <button class="ap-chip is-active" type="button" data-filter="all">Tutti</button>
                <?php foreach ($filterKeys as $key): ?>
                    <?php $filterLabel = $categoryMap[$key]['label'] ?? ucwords(str_replace('-', ' ', $key)); ?>
                    <button class="ap-chip" type="button" data-filter="<?php echo htmlspecialchars($key, ENT_QUOTES); ?>">
                        <?php echo htmlspecialchars($filterLabel, ENT_QUOTES); ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <div class="ap-market__hero-actions" data-reveal>
                <a class="ap-btn ap-btn--primary" href="?page=shop">Apri shop completo</a>
                <a class="ap-btn ap-btn--ghost" href="?page=cart">Vai al carrello</a>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if (!empty($showcaseProducts)): ?>
            <div class="ap-market__grid" data-reveal>
                <?php foreach ($showcaseProducts as $product): ?>
                    <?php
                    $productSlug = $product['slug'] ?? '';
                    $productUrl = $productSlug !== ''
                        ? '?page=product&slug=' . urlencode($productSlug)
                        : '?page=product&id=' . (int) $product['id'];
                    $badges = ap_product_badges($product);
                    $highlights = ap_product_highlights($product);
                    $stock = (int) $product['stock'];
                    $stockLabel = $stock === 0
                        ? 'Sold out'
                        : ($stock < 5 ? 'Ultimi ' . $stock . ' pezzi' : 'Disponibile subito');
                    $categoryKey = ap_product_category_key($product);
                    $image = $product['image_url'] ?: 'assets/img/og-image.jpg';
                    ?>
                    <article class="ap-market-card" data-category="<?php echo htmlspecialchars($categoryKey, ENT_QUOTES); ?>">
                        <div class="ap-market-card__media" style="background-image: url('<?php echo htmlspecialchars($image, ENT_QUOTES); ?>');"></div>
                        <div class="ap-market-card__overlay"></div>
                        <div class="ap-market-card__body">
                            <div class="ap-market-card__chips">
                                <span class="ap-chip"><?php echo htmlspecialchars(ap_product_category_label($product), ENT_QUOTES); ?></span>
                                <?php foreach ($badges as $badge): ?>
                                    <span class="ap-chip ap-chip--<?php echo htmlspecialchars($badge['variant'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($badge['label'], ENT_QUOTES); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <h3><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></h3>
                            <p><?php echo htmlspecialchars($product['description'], ENT_QUOTES); ?></p>
                            <?php if ($highlights): ?>
                                <ul class="ap-market-card__highlights">
                                    <?php foreach (array_slice($highlights, 0, 3) as $highlight): ?>
                                        <li><?php echo htmlspecialchars($highlight, ENT_QUOTES); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            <div class="ap-market-card__footer">
                                <div>
                                    <span class="price"><?php echo ap_price_format((int) $product['price_cents']); ?></span>
                                    <small><?php echo htmlspecialchars($stockLabel, ENT_QUOTES); ?></small>
                                </div>
                                <div class="ap-market-card__cta">
                                    <a class="ap-btn ap-btn--ghost" href="<?php echo htmlspecialchars($productUrl, ENT_QUOTES); ?>">Scheda</a>
                                    <form method="post">
                                        <input type="hidden" name="ap_action" value="add_to_cart">
                                        <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($redirectTarget, ENT_QUOTES); ?>">
                                        <button type="submit" class="ap-btn ap-btn--primary" <?php echo $stock === 0 ? 'disabled' : ''; ?>>Aggiungi</button>
                                    </form>
                                </div>
                            </div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('#vetrina .ap-market__filters [data-filter]');
    const cards = document.querySelectorAll('#vetrina .ap-market-card');
    if (!filterButtons.length || !cards.length) {
        return;
    }
    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const target = button.getAttribute('data-filter');
            filterButtons.forEach(function (btn) { btn.classList.remove('is-active'); });
            button.classList.add('is-active');
            cards.forEach(function (card) {
                const category = card.getAttribute('data-category');
                const shouldShow = target === 'all' || category === target;
                card.classList.toggle('is-hidden', !shouldShow);
            });
        });
    });
});
</script>
