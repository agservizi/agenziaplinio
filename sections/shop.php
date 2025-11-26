<?php
/**
 * Shop Page Section
 * Displays ecommerce products with filters, sidebar, and product grid.
 */

// Current URL for redirects
$currentUrl = $_SERVER['REQUEST_URI'] ?? '?page=shop';

// Fetch all products for category options
$allProducts = ap_fetch_products();
$categoryOptions = [];
foreach ($allProducts as $item) {
    $categoryOptions[ap_product_category_key($item)] = ap_product_category_label($item);
}
ksort($categoryOptions);

// Parse GET parameters for filters
$searchTerm = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$minPriceInput = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float) $_GET['min_price'] : null;
$maxPriceInput = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float) $_GET['max_price'] : null;
$availabilityParam = isset($_GET['availability']) ? (string) $_GET['availability'] : '';
$sortParam = isset($_GET['sort']) ? (string) $_GET['sort'] : 'newest';
$categoryParam = isset($_GET['category']) ? (string) $_GET['category'] : '';
$pageParam = isset($_GET['p']) && (int) $_GET['p'] > 0 ? (int) $_GET['p'] : 1;
$perPage = 12; // Products per page

// Build filters array
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
$filters['limit'] = $perPage;
$filters['offset'] = ($pageParam - 1) * $perPage;

// Fetch filtered products
$products = ap_fetch_products($filters);
if ($categoryParam !== '' && isset($categoryOptions[$categoryParam])) {
    $products = array_values(array_filter($products, static fn ($product) => ap_product_category_key($product) === $categoryParam));
}

// Get total count for pagination
$totalFilters = $filters;
unset($totalFilters['limit'], $totalFilters['offset']);
$allFilteredProducts = ap_fetch_products($totalFilters);
if ($categoryParam !== '' && isset($categoryOptions[$categoryParam])) {
    $allFilteredProducts = array_values(array_filter($allFilteredProducts, static fn ($product) => ap_product_category_key($product) === $categoryParam));
}
$totalProducts = count($allFilteredProducts);
$totalPages = (int) ceil($totalProducts / $perPage);

// Shop metrics
$metrics = ap_shop_metrics($allFilteredProducts);

// Active filters for display
$activeFilters = array_filter([
    'search' => $searchTerm !== '' ? $searchTerm : null,
    'category' => $categoryParam !== '' ? ($categoryOptions[$categoryParam] ?? null) : null,
    'availability' => $availabilityParam === 'in_stock' ? 'Solo disponibili' : null,
    'price_min' => $minPriceInput !== null ? $minPriceInput : null,
    'price_max' => $maxPriceInput !== null ? $maxPriceInput : null,
]);

// Build base URL for pagination
$queryParams = $_GET;
unset($queryParams['p']); // Remove page param for base URL
$baseUrl = '?' . http_build_query(array_merge($queryParams, ['page' => 'shop']));
?>
<section id="shop" class="shop-section section-padding" aria-labelledby="shop-heading">
    <div class="container-xxl">
        <!-- Shop Header -->
        <header class="shop-header text-center mb-5" data-reveal>
            <p class="eyebrow mb-2">Ecommerce</p>
            <h1 id="shop-heading" class="mb-3">Servizi pronti all'acquisto</h1>
            <p class="lead text-muted">Completa l'ordine in autonomia: attiviamo SIM, PEC, spedizioni e servizi digitali in poche ore.</p>
        </header>

        <!-- Shop Layout -->
        <div class="shop-layout" style="display: flex; min-height: 100vh; gap: 2rem;">
            <!-- Sidebar Toggle for Mobile -->
            <button class="shop-sidebar-toggle d-lg-none btn btn-outline-primary mb-3" type="button" aria-expanded="false" aria-controls="shop-sidebar">
                <span class="visually-hidden">Apri filtri</span>
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5v-2z"/>
                </svg>
                Filtri
            </button>

            <!-- Sidebar -->
            <aside id="shop-sidebar" class="shop-sidebar" style="width: 280px; background: #fff; border: 1px solid #e4e7ec; border-radius: 12px; padding: 1.5rem; position: sticky; top: 100px; height: fit-content; flex-shrink: 0;" aria-labelledby="sidebar-heading">
                <h2 id="sidebar-heading" class="h5 mb-4">Filtri</h2>

                <!-- Filters Form -->
                <form class="shop-filters" method="get" role="search">
                    <input type="hidden" name="page" value="shop">

                    <!-- Search -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="shop-search">Ricerca</label>
                        <input type="text" class="form-control" id="shop-search" name="q" placeholder="Sim, PEC, corriere..." value="<?php echo htmlspecialchars($searchTerm, ENT_QUOTES); ?>" aria-describedby="search-help">
                        <small id="search-help" class="form-text text-muted">Cerca per nome o descrizione</small>
                    </div>

                    <!-- Price Range -->
                    <fieldset class="mb-3">
                        <legend class="form-label fw-semibold">Prezzo (€)</legend>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" class="form-control" id="shop-min-price" name="min_price" placeholder="Min" min="0" step="1" value="<?php echo $minPriceInput !== null ? htmlspecialchars((string) $minPriceInput, ENT_QUOTES) : ''; ?>">
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control" id="shop-max-price" name="max_price" placeholder="Max" min="0" step="1" value="<?php echo $maxPriceInput !== null ? htmlspecialchars((string) $maxPriceInput, ENT_QUOTES) : ''; ?>">
                            </div>
                        </div>
                    </fieldset>

                    <!-- Availability -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="shop-availability">Disponibilità</label>
                        <select id="shop-availability" name="availability" class="form-select">
                            <option value="">Tutti i prodotti</option>
                            <option value="in_stock" <?php echo $availabilityParam === 'in_stock' ? 'selected' : ''; ?>>Solo disponibili</option>
                        </select>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="shop-category">Categoria</label>
                        <select id="shop-category" name="category" class="form-select">
                            <option value="">Tutte le categorie</option>
                            <?php foreach ($categoryOptions as $key => $label): ?>
                                <option value="<?php echo htmlspecialchars($key, ENT_QUOTES); ?>" <?php echo $categoryParam === $key ? 'selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="shop-sort">Ordina per</label>
                        <select id="shop-sort" name="sort" class="form-select">
                            <option value="newest" <?php echo $sortParam === 'newest' ? 'selected' : ''; ?>>Più recenti</option>
                            <option value="price_asc" <?php echo $sortParam === 'price_asc' ? 'selected' : ''; ?>>Prezzo crescente</option>
                            <option value="price_desc" <?php echo $sortParam === 'price_desc' ? 'selected' : ''; ?>>Prezzo decrescente</option>
                            <option value="alpha" <?php echo $sortParam === 'alpha' ? 'selected' : ''; ?>>Alfabetico</option>
                            <option value="stock" <?php echo $sortParam === 'stock' ? 'selected' : ''; ?>>Disponibilità</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" type="submit">Applica filtri</button>
                        <a href="?page=shop" class="btn btn-outline-secondary">Rimuovi filtri</a>
                    </div>
                </form>

                <!-- Active Filters -->
                <?php if ($activeFilters): ?>
                    <div class="mt-4">
                        <h3 class="h6 mb-2">Filtri attivi</h3>
                        <div class="d-flex flex-wrap gap-1">
                            <?php foreach ($activeFilters as $key => $value): ?>
                                <span class="badge bg-primary-subtle text-primary"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $key)) . ': ' . $value, ENT_QUOTES); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Shop Stats -->
                <div class="shop-stats mt-4 pt-4 border-top">
                    <h3 class="h6 mb-3">Statistiche</h3>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="stat-card text-center p-3 bg-light rounded">
                                <div class="stat-value h4 mb-1"><?php echo (int) $metrics['total']; ?></div>
                                <div class="stat-label small text-muted">Prodotti</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="stat-card text-center p-3 bg-light rounded">
                                <div class="stat-value h4 mb-1"><?php echo (int) $metrics['available']; ?></div>
                                <div class="stat-label small text-muted">Disponibili</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="stat-card text-center p-3 bg-light rounded">
                                <div class="stat-value h4 mb-1"><?php echo ap_price_format((int) $metrics['average_price']); ?></div>
                                <div class="stat-label small text-muted">Prezzo medio</div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="shop-main" style="flex: 1; min-width: 0;">
                <?php if (empty($products)): ?>
                    <div class="empty-state text-center py-5" data-reveal>
                        <div class="empty-state-icon mb-3">
                            <svg width="64" height="64" fill="currentColor" viewBox="0 0 16 16" class="text-muted">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="M5 6.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0zm1.138-1.496a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 1 .5-.5z"/>
                            </svg>
                        </div>
                        <h3 class="mb-2">Nessun prodotto trovato</h3>
                        <p class="text-muted mb-4">Prova a modificare i filtri o torna più tardi.</p>
                        <a href="?page=shop" class="btn btn-primary">Rimuovi filtri</a>
                    </div>
                <?php else: ?>
                    <!-- Products Grid -->
                    <div class="products-grid" role="main" aria-labelledby="products-heading">
                        <header class="products-header d-flex justify-content-between align-items-center mb-4">
                            <h2 id="products-heading" class="h4 mb-0">Prodotti (<?php echo count($products); ?>)</h2>
                            <div class="products-sort d-none d-md-block">
                                <!-- Sort could be duplicated here if needed -->
                            </div>
                        </header>

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
                                <div class="col-lg-6 col-xl-4" data-reveal>
                                    <article class="product-card card h-100 border-0 shadow-sm">
                                        <!-- Product Image -->
                                        <figure class="product-image mb-3">
                                            <a href="<?php echo htmlspecialchars($productUrl, ENT_QUOTES); ?>" class="d-block">
                                                <div class="product-image-inner ratio ratio-4x3" style="background-image: url('<?php echo htmlspecialchars($product['image_url'] ?: 'assets/img/og-image.jpg', ENT_QUOTES); ?>'); border-radius: 8px;"></div>
                                            </a>
                                        </figure>

                                        <!-- Product Body -->
                                        <div class="card-body p-3">
                                            <!-- Badges -->
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <span class="badge bg-dark"><?php echo htmlspecialchars($categoryLabel, ENT_QUOTES); ?></span>
                                                <?php if ($badges): ?>
                                                    <div class="d-flex gap-1">
                                                        <?php foreach ($badges as $badge): ?>
                                                            <span class="badge bg-<?php echo htmlspecialchars($badge['variant'], ENT_QUOTES); ?>-subtle text-<?php echo htmlspecialchars($badge['variant'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($badge['label'], ENT_QUOTES); ?></span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Title -->
                                            <h3 class="product-title h6 mb-2">
                                                <a href="<?php echo htmlspecialchars($productUrl, ENT_QUOTES); ?>" class="text-decoration-none text-dark"><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></a>
                                            </h3>

                                            <!-- Description -->
                                            <p class="product-description text-muted small mb-3"><?php echo htmlspecialchars($product['description'], ENT_QUOTES); ?></p>

                                            <!-- Highlights -->
                                            <?php if ($highlights): ?>
                                                <ul class="product-highlights list-unstyled small text-muted mb-3">
                                                    <?php foreach (array_slice($highlights, 0, 3) as $highlight): ?>
                                                        <li class="mb-1">• <?php echo htmlspecialchars($highlight, ENT_QUOTES); ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>

                                            <!-- Price and SKU -->
                                            <div class="product-meta d-flex justify-content-between align-items-center mb-3">
                                                <span class="product-price fw-bold text-primary"><?php echo ap_price_format((int) $product['price_cents']); ?></span>
                                                <?php if (!empty($product['sku'])): ?>
                                                    <small class="text-muted">SKU: <?php echo htmlspecialchars($product['sku'], ENT_QUOTES); ?></small>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Rating -->
                                            <?php
                                            $ratingData = ap_get_product_rating((int) $product['id']);
                                            if ($ratingData['total_reviews'] > 0):
                                            ?>
                                                <div class="product-rating mb-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="rating-stars">
                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                <svg width="14" height="14" fill="<?php echo $i <= round($ratingData['average_rating']) ? 'currentColor' : 'none'; ?>" viewBox="0 0 16 16" class="text-warning">
                                                                    <path d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
                                                                </svg>
                                                            <?php endfor; ?>
                                                        </div>
                                                        <small class="text-muted"><?php echo number_format($ratingData['average_rating'], 1); ?> (<?php echo $ratingData['total_reviews']; ?>)</small>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Klarna -->
                                            <?php if (function_exists('ap_klarna_messaging_enabled') && ap_klarna_messaging_enabled()): ?>
                                                <div class="klarna-messaging mb-3">
                                                    <?php echo ap_render_klarna_messaging((int) $product['price_cents'], ap_klarna_messaging_placement('product')); ?>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Actions -->
                                            <div class="product-actions">
                                                <div class="d-flex gap-2 mb-2">
                                                    <a href="<?php echo htmlspecialchars($productUrl, ENT_QUOTES); ?>" class="btn btn-outline-primary btn-sm flex-fill d-flex align-items-center justify-content-center gap-2">
                                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                            <path d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5V5.5a.5.5 0 0 1 .271-.445z"/>
                                                        </svg>
                                                        Scopri di più
                                                    </a>
                                                    <?php if (ap_auth_current_user() !== null): ?>
                                                        <?php $isInWishlist = ap_is_in_wishlist((int) ap_auth_current_user()['id'], (int) $product['id']); ?>
                                                        <form method="post" class="wishlist-form">
                                                            <input type="hidden" name="ap_action" value="toggle_wishlist">
                                                            <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                                            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($currentUrl, ENT_QUOTES); ?>">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center justify-content-center" title="<?php echo $isInWishlist ? 'Rimuovi dalla wishlist' : 'Aggiungi alla wishlist'; ?>">
                                                                <svg width="16" height="16" fill="<?php echo $isInWishlist ? 'currentColor' : 'none'; ?>" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.5">
                                                                    <path d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                                <form method="post" class="add-to-cart-form">
                                                    <input type="hidden" name="ap_action" value="add_to_cart">
                                                    <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                                    <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($currentUrl, ENT_QUOTES); ?>">
                                                    <div class="row g-2 mb-2">
                                                        <div class="col-8">
                                                            <label class="visually-hidden" for="qty-<?php echo (int) $product['id']; ?>">Quantità</label>
                                                            <input type="number" id="qty-<?php echo (int) $product['id']; ?>" name="quantity" min="1" max="<?php echo (int) $product['stock']; ?>" value="1" class="form-control form-control-sm" <?php echo (int) $product['stock'] === 0 ? 'disabled' : ''; ?>>
                                                        </div>
                                                        <div class="col-4">
                                                            <small class="text-muted d-block">Disp: <?php echo (int) $product['stock']; ?></small>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-2" type="submit" <?php echo (int) $product['stock'] === 0 ? 'disabled' : ''; ?>>
                                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                            <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l.5 2H5V5H3.14zM6 5v2h2V5H6zm3 0v2h2V5H9zm3 0v2h1.36l.5-2H12zm1.11 3H12v2h.61l.5-2zM11 8H9v2h2V8zM8 8H6v2h2V8zM5 8H3.89l.5 2H5V8zm0 5a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm1 1a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
                                                        </svg>
                                                        <?php echo (int) $product['stock'] === 0 ? 'Esaurito' : 'Aggiungi al carrello'; ?>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php echo ap_render_pagination($totalPages, $pageParam, $baseUrl); ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</section>

<!-- JavaScript for Sidebar Toggle -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.querySelector('.shop-sidebar-toggle');
    const sidebar = document.getElementById('shop-sidebar');

    if (toggle && sidebar) {
        toggle.addEventListener('click', function() {
            const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', !isExpanded);
            sidebar.classList.toggle('d-none', isExpanded);
            sidebar.classList.toggle('d-block', !isExpanded);
        });
    }
});
</script>
