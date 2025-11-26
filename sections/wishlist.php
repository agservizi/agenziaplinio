<?php
/**
 * Wishlist Page Section
 * Displays user's saved products with remove options and add to cart functionality.
 */

// Check if user is logged in
$user = ap_auth_current_user();
if (!$user) {
    // Redirect to login or show message
    header('Location: ?page=account&login=required');
    exit;
}

// Current URL for redirects
$currentUrl = $_SERVER['REQUEST_URI'] ?? '?page=wishlist';

// Get user's wishlist
$wishlistItems = ap_get_user_wishlist((int) $user['id'], ['active_only' => true]);

// Get wishlist count
$wishlistCount = ap_get_wishlist_count((int) $user['id']);
?>
<section id="wishlist" class="wishlist-section section-padding" aria-labelledby="wishlist-heading">
    <div class="container-xxl">
        <!-- Wishlist Header -->
        <header class="wishlist-header text-center mb-5" data-reveal>
            <p class="eyebrow mb-2">Area clienti</p>
            <h1 id="wishlist-heading" class="mb-3">La mia wishlist</h1>
            <p class="lead text-muted">I tuoi prodotti preferiti salvati per acquisti futuri.</p>
        </header>

        <!-- Wishlist Content -->
        <div class="wishlist-content">
            <?php if (empty($wishlistItems)): ?>
                <div class="empty-wishlist text-center py-5" data-reveal>
                    <div class="empty-wishlist-icon mb-3">
                        <svg width="64" height="64" fill="currentColor" viewBox="0 0 16 16" class="text-muted">
                            <path d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2">La tua wishlist è vuota</h3>
                    <p class="text-muted mb-4">Aggiungi prodotti che ti interessano per tenerli d'occhio.</p>
                    <a href="?page=shop" class="btn btn-primary">Esplora il catalogo</a>
                </div>
            <?php else: ?>
                <!-- Wishlist Stats -->
                <div class="wishlist-stats mb-4" data-reveal>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="stat-card p-3 bg-light rounded">
                                <div class="stat-value h4 mb-1"><?php echo count($wishlistItems); ?></div>
                                <div class="stat-label small text-muted">Prodotti salvati</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-card p-3 bg-light rounded">
                                <div class="stat-value h4 mb-1"><?php echo count(array_filter($wishlistItems, fn($item) => $item['stock'] > 0)); ?></div>
                                <div class="stat-label small text-muted">Disponibili ora</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wishlist Grid -->
                <div class="wishlist-grid" role="main" aria-labelledby="wishlist-products-heading">
                    <h2 id="wishlist-products-heading" class="h4 mb-4">Prodotti salvati</h2>

                    <div class="row g-4">
                        <?php foreach ($wishlistItems as $item): ?>
                            <?php
                            $productSlug = $item['slug'] ?? '';
                            $productUrl = $productSlug !== ''
                                ? '?page=product&slug=' . urlencode($productSlug)
                                : '?page=product&id=' . (int) $item['id'];
                            $badges = ap_product_badges($item);
                            $highlights = ap_product_highlights($item);
                            $categoryLabel = ap_product_category_label($item);
                            $isAvailable = (int) $item['stock'] > 0;
                            ?>
                            <div class="col-lg-6 col-xl-4" data-reveal>
                                <article class="wishlist-item-card card h-100 border-0 shadow-sm">
                                    <!-- Product Image -->
                                    <figure class="product-image mb-3">
                                        <a href="<?php echo htmlspecialchars($productUrl, ENT_QUOTES); ?>" class="d-block">
                                            <div class="product-image-inner ratio ratio-4x3" style="background-image: url('<?php echo htmlspecialchars($item['image_url'] ?: 'assets/img/og-image.jpg', ENT_QUOTES); ?>'); border-radius: 8px;"></div>
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
                                            <a href="<?php echo htmlspecialchars($productUrl, ENT_QUOTES); ?>" class="text-decoration-none text-dark"><?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?></a>
                                        </h3>

                                        <!-- Description -->
                                        <p class="product-description text-muted small mb-3"><?php echo htmlspecialchars($item['description'], ENT_QUOTES); ?></p>

                                        <!-- Highlights -->
                                        <?php if ($highlights): ?>
                                            <ul class="product-highlights list-unstyled small text-muted mb-3">
                                                <?php foreach (array_slice($highlights, 0, 3) as $highlight): ?>
                                                    <li class="mb-1">• <?php echo htmlspecialchars($highlight, ENT_QUOTES); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>

                                        <!-- Price and Stock -->
                                        <div class="product-meta d-flex justify-content-between align-items-center mb-3">
                                            <span class="product-price fw-bold text-primary"><?php echo ap_price_format((int) $item['price_cents']); ?></span>
                                            <small class="text-<?php echo $isAvailable ? 'success' : 'danger'; ?>">
                                                <?php echo $isAvailable ? 'Disponibile' : 'Esaurito'; ?>
                                            </small>
                                        </div>

                                        <!-- Actions -->
                                        <div class="product-actions d-flex gap-2">
                                            <a href="<?php echo htmlspecialchars($productUrl, ENT_QUOTES); ?>" class="btn btn-outline-primary btn-sm flex-fill">
                                                Scopri di più
                                            </a>
                                            <?php if ($isAvailable): ?>
                                                <form method="post" class="add-to-cart-form">
                                                    <input type="hidden" name="ap_action" value="add_to_cart">
                                                    <input type="hidden" name="product_id" value="<?php echo (int) $item['id']; ?>">
                                                    <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($currentUrl, ENT_QUOTES); ?>">
                                                    <button class="btn btn-primary btn-sm" type="submit" title="Aggiungi al carrello">
                                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                            <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l.5 2H5V5H3.14zM6 5v2h2V5H6zm3 0v2h2V5H9zm3 0v2h1.36l.5-2H12zm1.11 3H12v2h.61l.5-2zM11 8H9v2h2V8zM8 8H6v2h2V8zM5 8H3.89l.5 2H5V8zm0 5a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm1 1a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="post" class="remove-from-wishlist-form">
                                                <input type="hidden" name="ap_action" value="remove_from_wishlist">
                                                <input type="hidden" name="product_id" value="<?php echo (int) $item['id']; ?>">
                                                <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($currentUrl, ENT_QUOTES); ?>">
                                                <button class="btn btn-outline-danger btn-sm" type="submit" title="Rimuovi dalla wishlist">
                                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>