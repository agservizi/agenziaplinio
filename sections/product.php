<?php
$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
$product = $productDetail ?? null;
if (!$product && $slug !== '') {
    $product = ap_find_product_by_slug($slug);
}
if (!$product && isset($_GET['id'])) {
    $productId = (int) $_GET['id'];
    if ($productId > 0) {
        $product = ap_find_product($productId);
    }
}
$isUnavailable = !$product || (int) $product['is_active'] !== 1;
?>
<section class="product-section section-padding">
    <div class="container">
        <nav class="mb-4" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?page=shop">Shop</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name'] ?? 'Prodotto', ENT_QUOTES); ?></li>
            </ol>
        </nav>
        <?php if ($isUnavailable): ?>
            <div class="empty-state text-center p-5">
                <h2 class="mb-3">Prodotto non disponibile</h2>
                <p class="text-muted mb-4">Il prodotto richiesto potrebbe essere stato rimosso o non è più attivo a catalogo.</p>
                <a class="ap-btn ap-btn--primary" href="?page=shop">Torna allo shop</a>
            </div>
        <?php else: ?>
            <?php
            $categoryLabel = ap_product_category_label($product);
            $badges = ap_product_badges($product);
            $highlights = ap_product_highlights($product);
            $heroImage = $product['image_url'] ?: 'assets/img/og-image.jpg';
            $categoryKey = ap_product_category_key($product);
            $relatedPool = ap_fetch_products(['sort' => 'newest']);
            $related = array_values(array_filter($relatedPool, function ($candidate) use ($product, $categoryKey) {
                if ((int) $candidate['id'] === (int) $product['id']) {
                    return false;
                }
                return ap_product_category_key($candidate) === $categoryKey;
            }));
            $related = array_slice($related, 0, 3);
            $isDigital = strtolower((string) $product['fulfillment_type']) === 'digital';
            $customFields = $isDigital ? ap_fetch_product_custom_fields((int) $product['id']) : [];
            ?>
            <div class="row g-5">
                <div class="col-xl-7">
                    <div class="product-hero-card rounded-4 border shadow-sm overflow-hidden mb-4">
                        <div class="p-4 p-lg-5 bg-body-secondary">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                <span class="badge bg-dark text-white"><?php echo htmlspecialchars($categoryLabel, ENT_QUOTES); ?></span>
                                <?php foreach ($badges as $badge): ?>
                                    <span class="badge bg-<?php echo htmlspecialchars($badge['variant'], ENT_QUOTES); ?>-subtle text-<?php echo htmlspecialchars($badge['variant'], ENT_QUOTES); ?>">
                                        <?php echo htmlspecialchars($badge['label'], ENT_QUOTES); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                            <h1 class="display-6 mb-3"><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></h1>
                            <p class="lead text-muted mb-0"><?php echo nl2br(htmlspecialchars($product['description'], ENT_QUOTES)); ?></p>
                        </div>
                        <div class="product-hero ratio ratio-16x9" style="background-image: url('<?php echo htmlspecialchars($heroImage, ENT_QUOTES); ?>'); background-size: cover; background-position: center;"></div>
                    </div>
                    <?php if (!empty($highlights)): ?>
                        <div class="product-highlights mb-4">
                            <h3 class="h5 mb-3">Caratteristiche principali</h3>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($highlights as $highlight): ?>
                                    <li class="list-group-item border-0 ps-0">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <?php echo htmlspecialchars($highlight, ENT_QUOTES); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <?php if ($related): ?>
                        <div class="related-products mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h3 class="h5 mb-0">Bundle consigliati</h3>
                                <a class="btn btn-link" href="?page=shop">Vedi tutto</a>
                            </div>
                            <div class="row g-3">
                                <?php foreach ($related as $relatedProduct): ?>
                                    <?php
                                    $relatedUrl = !empty($relatedProduct['slug'])
                                        ? '?page=product&slug=' . urlencode($relatedProduct['slug'])
                                        : '?page=product&id=' . (int) $relatedProduct['id'];
                                    ?>
                                    <div class="col-md-4">
                                        <div class="border rounded-3 p-3 h-100">
                                            <p class="fw-semibold mb-1"><?php echo htmlspecialchars($relatedProduct['name'], ENT_QUOTES); ?></p>
                                            <small class="text-muted d-block mb-2"><?php echo ap_price_format((int) $relatedProduct['price_cents']); ?></small>
                                            <a class="btn btn-sm btn-outline-primary" href="<?php echo htmlspecialchars($relatedUrl, ENT_QUOTES); ?>">Apri scheda</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-xl-5">
                    <div class="sticky-top" style="top: 110px;">
                        <div class="card shadow-lg border-0 mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">Investimento</span>
                                    <?php if (!empty($product['sku'])): ?>
                                        <span class="badge bg-light text-dark">SKU <?php echo htmlspecialchars($product['sku'], ENT_QUOTES); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex align-items-baseline gap-2 mb-3">
                                    <span class="display-6 mb-0"><?php echo ap_price_format((int) $product['price_cents']); ?></span>
                                    <small class="text-muted">+ IVA</small>
                                </div>
                                <?php if (function_exists('ap_render_klarna_messaging') && ap_klarna_messaging_enabled()): ?>
                                    <div class="klarna-onsite-wrapper small text-muted mb-3">
                                        <?php echo ap_render_klarna_messaging((int) $product['price_cents'], ap_klarna_messaging_placement('product')); ?>
                                    </div>
                                <?php endif; ?>
                                <ul class="list-unstyled small text-muted mb-4">
                                    <li class="mb-1">Disponibilità: <?php echo (int) $product['stock'] > 0 ? 'In pronta consegna' : 'Su richiesta (24h)'; ?></li>
                                    <li class="mb-1">Supporto priority incluso</li>
                                    <li>Contratto annuale o pay-per-use</li>
                                </ul>
                                <form method="post" class="d-flex flex-column gap-3">
                                    <input type="hidden" name="ap_action" value="add_to_cart">
                                    <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                    <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? '?page=product&slug=' . urlencode($slug), ENT_QUOTES); ?>">
                                    <div>
                                        <label class="form-label small" for="product-qty">Quantità</label>
                                        <input class="form-control" type="number" id="product-qty" name="quantity" min="1" max="<?php echo max(1, (int) $product['stock']); ?>" value="1" <?php echo (int) $product['stock'] === 0 ? 'disabled' : ''; ?>>
                                    </div>
                                    <?php if ($isDigital && !empty($customFields)): ?>
                                        <div class="custom-fields-section">
                                            <h4 class="h6 mb-3">Dettagli personalizzazione</h4>
                                            <?php foreach ($customFields as $field): ?>
                                                <div class="mb-3">
                                                    <label class="form-label small" for="custom_<?php echo (int) $field['id']; ?>">
                                                        <?php echo htmlspecialchars($field['field_label'], ENT_QUOTES); ?>
                                                        <?php if ((int) $field['is_required'] === 1): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    <?php if ($field['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="custom_<?php echo (int) $field['id']; ?>" name="custom_fields[<?php echo (int) $product['id']; ?>][<?php echo (int) $field['id']; ?>]" rows="3" <?php echo (int) $field['is_required'] === 1 ? 'required' : ''; ?>><?php echo htmlspecialchars($_POST['custom_fields'][$product['id']][$field['id']] ?? '', ENT_QUOTES); ?></textarea>
                                                    <?php elseif ($field['field_type'] === 'select' && !empty($field['field_options'])): ?>
                                                        <select class="form-select" id="custom_<?php echo (int) $field['id']; ?>" name="custom_fields[<?php echo (int) $product['id']; ?>][<?php echo (int) $field['id']; ?>]" <?php echo (int) $field['is_required'] === 1 ? 'required' : ''; ?>>
                                                            <option value="">Seleziona...</option>
                                                            <?php foreach (explode(',', $field['field_options']) as $option): ?>
                                                                <option value="<?php echo htmlspecialchars(trim($option), ENT_QUOTES); ?>" <?php echo (trim($option) === ($_POST['custom_fields'][$product['id']][$field['id']] ?? '')) ? 'selected' : ''; ?>><?php echo htmlspecialchars(trim($option), ENT_QUOTES); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php else: ?>
                                                        <input class="form-control" type="<?php echo $field['field_type'] === 'email' ? 'email' : ($field['field_type'] === 'number' ? 'number' : 'text'); ?>" id="custom_<?php echo (int) $field['id']; ?>" name="custom_fields[<?php echo (int) $product['id']; ?>][<?php echo (int) $field['id']; ?>]" value="<?php echo htmlspecialchars($_POST['custom_fields'][$product['id']][$field['id']] ?? '', ENT_QUOTES); ?>" <?php echo (int) $field['is_required'] === 1 ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <button class="ap-btn ap-btn--primary w-100" type="submit" <?php echo (int) $product['stock'] === 0 ? 'disabled' : ''; ?>>Aggiungi al carrello</button>
                                    <a class="ap-btn ap-btn--ghost w-100" href="?page=shop">Confronta altri servizi</a>
                                </form>
                                <?php if ((int) $product['stock'] === 0): ?>
                                    <div class="alert alert-warning mt-3 mb-0">
                                        Stiamo riassortendo questo prodotto. Prenotalo subito con il nostro team.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <p class="text-muted small mb-2">FAQ veloci</p>
                                <ul class="list-unstyled mb-0 small">
                                    <li class="mb-2"><strong>Delivery:</strong> onboarding guidato entro 24h dalla conferma.</li>
                                    <li class="mb-2"><strong>Contratti:</strong> onboarding 100% digitale con firma remota.</li>
                                    <li class="mb-0"><strong>Scalabilità:</strong> puoi ampliare o ridurre i volumi mese per mese.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
