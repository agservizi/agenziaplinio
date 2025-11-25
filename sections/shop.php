<?php
$currentUrl = $_SERVER['REQUEST_URI'] ?? '?page=shop';
$allProducts = ap_fetch_products();
$categoryOptions = [];
foreach ($allProducts as $item) {
    $categoryOptions[ap_product_category_key($item)] = ap_product_category_label($item);
}
ksort($categoryOptions);

$searchTerm = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$minPriceInput = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float) $_GET['min_price'] : null;
$maxPriceInput = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float) $_GET['max_price'] : null;
$availabilityParam = isset($_GET['availability']) ? (string) $_GET['availability'] : '';
$sortParam = isset($_GET['sort']) ? (string) $_GET['sort'] : 'newest';
$categoryParam = isset($_GET['category']) ? (string) $_GET['category'] : '';

$filters = [];
if ($searchTerm !== '') {
    $filters['search'] = $searchTerm;
}
if ($minPriceInput !== null && $minPriceInput >= 0) {
    $filters['price_min'] = (int) round($minPriceInput * 100);
}
if ($maxPriceInput !== null && $maxPriceInput >= 0) {
    $filters['price_max'] = (int) round($maxPriceInput * 100);
}
if ($availabilityParam === 'in_stock') {
    $filters['in_stock'] = true;
}
$sortOptions = ['newest', 'price_asc', 'price_desc', 'alpha', 'stock'];
$filters['sort'] = in_array($sortParam, $sortOptions, true) ? $sortParam : 'newest';

$products = ap_fetch_products($filters);
if ($categoryParam !== '' && isset($categoryOptions[$categoryParam])) {
    $products = array_values(array_filter($products, static fn ($product) => ap_product_category_key($product) === $categoryParam));
}

$metrics = ap_shop_metrics($products);
$activeFilters = array_filter([
    'search' => $searchTerm !== '' ? $searchTerm : null,
    'category' => $categoryParam !== '' ? ($categoryOptions[$categoryParam] ?? null) : null,
    'availability' => $availabilityParam === 'in_stock' ? 'Solo disponibili' : null,
    'price_min' => $minPriceInput !== null ? $minPriceInput : null,
    'price_max' => $maxPriceInput !== null ? $maxPriceInput : null,
]);
?>
<section id="shop" class="shop-section section-padding">
    <div class="container-xxl">
        <div class="section-heading text-center mb-5" data-reveal>
            <p class="eyebrow mb-2">Ecommerce</p>
            <h2 class="mb-3">Servizi pronti all'acquisto</h2>
            <p class="lead text-muted">Completa l'ordine in autonomia: attiviamo SIM, PEC, spedizioni e servizi digitali in poche ore.</p>
        </div>
        <div class="row gy-4">
            <div class="col-lg-3">
                <div class="ap-card p-4 h-100 position-sticky" style="top: 20px;" data-reveal>
                    <h5 class="mb-3">Filtri</h5>
                    <form class="row g-3 align-items-end" method="get">
                        <input type="hidden" name="page" value="shop">
                        <div class="col-12">
                            <label class="form-label" for="shop-search">Ricerca proattiva</label>
                            <input type="text" class="form-control" id="shop-search" name="q" placeholder="Sim, PEC, corriere..." value="<?php echo htmlspecialchars($searchTerm, ENT_QUOTES); ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="shop-min-price">Min €</label>
                            <input type="number" min="0" step="1" class="form-control" id="shop-min-price" name="min_price" value="<?php echo $minPriceInput !== null ? htmlspecialchars((string) $minPriceInput, ENT_QUOTES) : ''; ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="shop-max-price">Max €</label>
                            <input type="number" min="0" step="1" class="form-control" id="shop-max-price" name="max_price" value="<?php echo $maxPriceInput !== null ? htmlspecialchars((string) $maxPriceInput, ENT_QUOTES) : ''; ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="shop-availability">Disponibilità</label>
                            <select id="shop-availability" name="availability" class="form-select">
                                <option value="">Tutti</option>
                                <option value="in_stock" <?php echo $availabilityParam === 'in_stock' ? 'selected' : ''; ?>>Solo disponibili</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="shop-category">Categoria dinamica</label>
                            <select id="shop-category" name="category" class="form-select">
                                <option value="">Tutte</option>
                                <?php foreach ($categoryOptions as $key => $label): ?>
                                    <option value="<?php echo htmlspecialchars($key, ENT_QUOTES); ?>" <?php echo $categoryParam === $key ? 'selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="shop-sort">Ordinamento</label>
                            <select id="shop-sort" name="sort" class="form-select">
                                <option value="newest" <?php echo $sortParam === 'newest' ? 'selected' : ''; ?>>Più recenti</option>
                                <option value="price_asc" <?php echo $sortParam === 'price_asc' ? 'selected' : ''; ?>>Prezzo crescente</option>
                                <option value="price_desc" <?php echo $sortParam === 'price_desc' ? 'selected' : ''; ?>>Prezzo decrescente</option>
                                <option value="alpha" <?php echo $sortParam === 'alpha' ? 'selected' : ''; ?>>Alfabetico</option>
                                <option value="stock" <?php echo $sortParam === 'stock' ? 'selected' : ''; ?>>Stock</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <button class="ap-btn ap-btn--primary w-100" type="submit">Filtra</button>
                        </div>
                        <div class="col-6">
                            <a href="?page=shop" class="btn btn-link w-100">Reset</a>
                        </div>
                    </form>
                    <?php if ($activeFilters): ?>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <?php foreach ($activeFilters as $key => $value): ?>
                                <span class="badge rounded-pill bg-dark-subtle text-dark small"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $key)) . ': ' . $value, ENT_QUOTES); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="row g-3 mt-3" data-reveal>
                    <div class="col-sm-6 col-lg-12">
                        <div class="ap-stat">
                            <p class="text-muted mb-1">Catalogo</p>
                            <h4 class="mb-0"><?php echo (int) $metrics['total']; ?></h4>
                            <small>Servizi attualmente filtrati</small>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-12">
                        <div class="ap-stat">
                            <p class="text-muted mb-1">Disponibili</p>
                            <h4 class="mb-0"><?php echo (int) $metrics['available']; ?></h4>
                            <small><?php echo (int) $metrics['low_stock']; ?> in esaurimento</small>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-12">
                        <div class="ap-stat">
                            <p class="text-muted mb-1">Ticket medio</p>
                            <h4 class="mb-0"><?php echo ap_price_format((int) $metrics['average_price']); ?></h4>
                            <small>Calcolato sul set filtrato</small>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-12">
                        <div class="ap-stat">
                            <p class="text-muted mb-1">Sold-out</p>
                            <h4 class="mb-0"><?php echo (int) $metrics['sold_out']; ?></h4>
                            <small>Monitoraggio stock live</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <?php if (empty($products)): ?>
                    <div class="empty-state text-center p-5 h-100 d-flex flex-column justify-content-center" data-reveal>
                        <p class="mb-1">Nessun prodotto disponibile al momento.</p>
                        <small class="text-muted">Torna più tardi o contatta il nostro team per richieste personalizzate.</small>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($products as $product): ?>
                            <?php
                            $productSlug = $product['slug'] ?? '';
                            $productUrl = $productSlug !== ''
                                ? '?page=product&slug=' . urlencode($productSlug)
                                : '?page=product&id=' . (int) $product['id'];
                            $badges = ap_product_badges($product);
                            $highlights = ap_product_highlights($product);
                            $categoryLabel = ap_product_category_label($product);
                            ?>
                            <div class="col-md-6" data-reveal>
                                <div class="shop-card h-100">
                                    <div class="shop-card__image" style="background-image: url('<?php echo htmlspecialchars($product['image_url'] ?: 'assets/img/og-image.jpg', ENT_QUOTES); ?>');"></div>
                                    <div class="shop-card__body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-dark text-white"><?php echo htmlspecialchars($categoryLabel, ENT_QUOTES); ?></span>
                                            <?php if ($badges): ?>
                                                <div class="d-flex gap-1 flex-wrap">
                                                    <?php foreach ($badges as $badge): ?>
                                                        <span class="badge bg-<?php echo htmlspecialchars($badge['variant'], ENT_QUOTES); ?>-subtle text-<?php echo htmlspecialchars($badge['variant'], ENT_QUOTES); ?> fw-normal"><?php echo htmlspecialchars($badge['label'], ENT_QUOTES); ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <h5 class="shop-card__title"><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></h5>
                                        <p class="shop-card__description"><?php echo htmlspecialchars($product['description'], ENT_QUOTES); ?></p>
                                        <?php if ($highlights): ?>
                                            <ul class="list-unstyled small text-muted mb-3">
                                                <?php foreach (array_slice($highlights, 0, 3) as $highlight): ?>
                                                    <li>• <?php echo htmlspecialchars($highlight, ENT_QUOTES); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                        <div class="shop-card__meta">
                                            <span class="price-tag"><?php echo ap_price_format((int) $product['price_cents']); ?></span>
                                            <?php if (!empty($product['sku'])): ?>
                                                <span class="sku">SKU <?php echo htmlspecialchars($product['sku'], ENT_QUOTES); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (function_exists('ap_klarna_messaging_enabled') && ap_klarna_messaging_enabled()): ?>
                                            <div class="klarna-onsite-wrapper mt-2 small text-muted">
                                                <?php echo ap_render_klarna_messaging((int) $product['price_cents'], ap_klarna_messaging_placement('product')); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="mt-3">
                                            <a class="fw-semibold text-decoration-none" href="<?php echo htmlspecialchars($productUrl, ENT_QUOTES); ?>">Scopri il prodotto →</a>
                                        </div>
                                        <form method="post" class="shop-card__form">
                                            <input type="hidden" name="ap_action" value="add_to_cart">
                                            <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($currentUrl, ENT_QUOTES); ?>">
                                            <div class="d-flex align-items-center gap-2 mb-3">
                                                <label class="visually-hidden" for="qty-<?php echo (int) $product['id']; ?>">Quantità</label>
                                                <input type="number" id="qty-<?php echo (int) $product['id']; ?>" name="quantity" min="1" max="<?php echo (int) $product['stock']; ?>" value="1" class="form-control form-control-sm" <?php echo (int) $product['stock'] === 0 ? 'disabled' : ''; ?>>
                                                <span class="text-muted small">Disponibili: <?php echo (int) $product['stock']; ?></span>
                                            </div>
                                            <button class="ap-btn ap-btn--primary w-100" type="submit" <?php echo (int) $product['stock'] === 0 ? 'disabled' : ''; ?>>Aggiungi al carrello</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
