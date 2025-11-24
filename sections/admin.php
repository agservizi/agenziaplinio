<?php
if (!ap_auth_is_admin()) {
    echo '<section class="section-padding"><div class="container"><div class="empty-state p-5 text-center"><h2>Accesso negato</h2><p>Questa area è riservata allo staff Agenzia Plinio.</p></div></div></section>';
    return;
}
$section = $_GET['section'] ?? 'dashboard';
$adminBasePath = $adminBasePath ?? '?page=admin';
$adminUrl = static function (array $params = [], string $hash = '') use ($adminBasePath): string {
    $url = $adminBasePath;
    if (!empty($params)) {
        $separator = str_contains($adminBasePath, '?') ? '&' : '?';
        $url .= $separator . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }
    if ($hash !== '') {
        $url .= '#' . ltrim($hash, '#');
    }
    return $url;
};
if (!isset($_GET['section']) && isset($_GET['id'])) {
    header('Location: ' . $adminUrl(['section' => 'edit_product', 'id' => (int) $_GET['id']]));
    exit;
}
$products = ap_fetch_products([]);
$statusFilter = $_GET['status'] ?? 'all';
$shippingFilter = $_GET['shipping'] ?? 'all';
$searchTerm = trim((string) ($_GET['q'] ?? ''));
$orderFilters = [];
if ($statusFilter !== 'all') {
    $orderFilters['status'] = $statusFilter;
}
if ($shippingFilter !== 'all') {
    $orderFilters['shipping_status'] = $shippingFilter;
}
if ($searchTerm !== '') {
    $orderFilters['search'] = $searchTerm;
}
$ordersPerPage = 10;
$ordersPage = max(1, (int) ($_GET['orders_page'] ?? 1));
$ordersTotal = ap_count_orders(null, $orderFilters);
$ordersPages = max(1, (int) ceil($ordersTotal / $ordersPerPage));
if ($ordersPage > $ordersPages) {
    $ordersPage = $ordersPages;
}
$orderFiltersPaginated = $orderFilters;
$orderFiltersPaginated['limit'] = $ordersPerPage;
$orderFiltersPaginated['offset'] = ($ordersPage - 1) * $ordersPerPage;
$orders = ap_fetch_orders(null, $orderFiltersPaginated);
$buildOrdersPageUrl = function (int $page) use ($searchTerm, $statusFilter, $shippingFilter, $adminUrl) {
    $query = ['orders_page' => max(1, $page)];
    if ($searchTerm !== '') {
        $query['q'] = $searchTerm;
    }
    if ($statusFilter !== 'all') {
        $query['status'] = $statusFilter;
    }
    if ($shippingFilter !== 'all') {
        $query['shipping'] = $shippingFilter;
    }
    return $adminUrl($query);
};
$insights = ap_admin_insights();
$shippingQueue = ap_fetch_orders(null, ['shipping_status' => 'preparing', 'limit' => 5]);
$paymentAlerts = ap_fetch_orders(null, ['payment_status' => 'pending', 'limit' => 5]);
$editingId = isset($_GET['product']) ? (int) $_GET['product'] : (isset($_GET['id']) ? (int) $_GET['id'] : null);
$editingProduct = $editingId ? ap_find_product($editingId) : null;
$categoryOptions = ap_product_category_options();
$customFields = $editingProduct ? ap_fetch_product_custom_fields($editingId) : [];
$totalRevenue = (int) ($insights['total_revenue'] ?? 0);
$recentRevenue = (int) ($insights['recent_revenue'] ?? 0);
$averageOrder = (int) ($insights['average_order'] ?? 0);
$activeProducts = count(array_filter($products, function ($product) {
    return (int) $product['is_active'] === 1;
}));
$pendingShipmentsCount = (int) ($insights['pending_shipments'] ?? 0);
$awaitingPaymentsCount = (int) ($insights['awaiting_payments'] ?? 0);
$orderStatuses = ['pending' => 'In attesa', 'processing' => 'In lavorazione', 'completed' => 'Completato', 'cancelled' => 'Annullato'];
$shippingStatuses = ['preparing' => 'In preparazione', 'in_transit' => 'In transito', 'delivered' => 'Consegnato', 'issue' => 'In verifica'];
$shippingMethods = ap_shipping_methods();
$coupons = ap_fetch_coupons();
$couponEditingId = isset($_GET['coupon']) ? (int) $_GET['coupon'] : null;
$editingCoupon = $couponEditingId ? ap_find_coupon_by_id($couponEditingId) : null;
$abandonedCarts = ap_fetch_abandoned_carts(8);
$abandonedPendingCount = 0;
foreach ($abandonedCarts as $abandonedCart) {
    if (empty($abandonedCart['notified_at'])) {
        $abandonedPendingCount++;
    }
}
$announcementsAdmin = ap_fetch_announcements(['include_inactive' => true]);
$announcementSlides = array_chunk($announcementsAdmin, 4);
$announcementEditingId = isset($_GET['announcement']) ? (int) $_GET['announcement'] : null;
$announcementEditing = $announcementEditingId ? ap_find_announcement($announcementEditingId) : null;
$faqsAdmin = ap_fetch_faqs();
$faqSlides = array_chunk($faqsAdmin, 3);
$faqEditingId = isset($_GET['faq']) ? (int) $_GET['faq'] : null;
$faqEditing = $faqEditingId ? ap_find_faq($faqEditingId) : null;
$newsIconOptions = [
    '⚡️' => 'Energia / Promo lampo',
    '🚀' => 'Lancio / Upgrade',
    '🎁' => 'Regalo / Bonus',
    '🛰️' => 'Servizi digitali',
    '📦' => 'Spedizioni / Logistica',
    '💳' => 'Pagamenti / Finanza',
    '🛡️' => 'Sicurezza / Compliance',
    '☎️' => 'Supporto clienti',
];
?>
<section class="admin-section section-padding">
    <div class="container-xxl">
        <div class="section-heading mb-5">
            <p class="eyebrow mb-2">Area admin</p>
            <h2 class="mb-1">Panoramica ecommerce</h2>
            <p class="text-muted">Gestisci catalogo prodotti, disponibilità e ordini in tempo reale.</p>
        </div>
        <?php if ($section === 'dashboard'): ?>
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card">
                    <p class="stat-label">Ordini totali</p>
                    <h3><?php echo (int) ($insights['total_orders'] ?? 0); ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <p class="stat-label">Fatturato complessivo</p>
                    <h3><?php echo ap_price_format($totalRevenue); ?></h3>
                    <small class="text-muted">Ultimi 30gg: <?php echo ap_price_format($recentRevenue); ?></small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <p class="stat-label">Ticket medio</p>
                    <h3><?php echo ap_price_format($averageOrder); ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <p class="stat-label">Prodotti attivi</p>
                    <h3><?php echo $activeProducts; ?></h3>
                </div>
            </div>
        </div>
        <div class="admin-card mb-5">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="mb-0">Spedizioni da evadere</h4>
                        <span class="badge bg-light text-dark"><?php echo $pendingShipmentsCount; ?></span>
                    </div>
                    <?php if (empty($shippingQueue)): ?>
                        <p class="text-muted small mb-0">Nessuna spedizione in sospeso.</p>
                    <?php else: ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($shippingQueue as $queueOrder): ?>
                                <li class="border rounded-3 p-3 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-semibold">#<?php echo (int) $queueOrder['id']; ?> · <?php echo htmlspecialchars($queueOrder['customer_name'], ENT_QUOTES); ?></span>
                                        <span class="small text-muted"><?php echo htmlspecialchars($queueOrder['shipping_method'] ?? 'standard', ENT_QUOTES); ?></span>
                                    </div>
                                    <div class="small text-muted">Totale <?php echo ap_price_format((int) $queueOrder['total_cents']); ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="mb-0">Pagamenti in attesa</h4>
                        <span class="badge bg-light text-dark"><?php echo $awaitingPaymentsCount; ?></span>
                    </div>
                    <?php if (empty($paymentAlerts)): ?>
                        <p class="text-muted small mb-0">Tutti i pagamenti sono allineati.</p>
                    <?php else: ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($paymentAlerts as $pendingOrder): ?>
                                <li class="border rounded-3 p-3 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-semibold">#<?php echo (int) $pendingOrder['id']; ?> · <?php echo htmlspecialchars($pendingOrder['customer_name'], ENT_QUOTES); ?></span>
                                        <span class="badge bg-warning-subtle text-warning text-uppercase"><?php echo htmlspecialchars($pendingOrder['payment_method'] ?? 'manual', ENT_QUOTES); ?></span>
                                    </div>
                                    <div class="small text-muted">Richiede follow-up · <?php echo ap_price_format((int) $pendingOrder['total_cents']); ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($section === 'annunci'): ?>
        <div class="admin-card mb-5" id="annunci">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0"><?php echo $announcementEditing ? 'Modifica news topbar' : 'Barra news in evidenza'; ?></h4>
                    <small class="text-muted">Messaggi che scorrono sopra la topbar pubblica</small>
                </div>
                <?php if ($announcementEditing): ?>
                    <a class="small" href="<?php echo htmlspecialchars($adminUrl([], 'annunci'), ENT_QUOTES); ?>">Annulla modifica</a>
                <?php endif; ?>
            </div>
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-5">
                    <form method="post" class="h-100 d-flex flex-column">
                        <input type="hidden" name="ap_action" value="admin_save_announcement">
                        <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl([], 'annunci'), ENT_QUOTES); ?>">
                        <?php if ($announcementEditing): ?>
                            <input type="hidden" name="announcement_id" value="<?php echo (int) $announcementEditing['id']; ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label" for="announcement_message">Messaggio</label>
                            <textarea class="form-control" id="announcement_message" name="message" rows="3" maxlength="280" placeholder="Es. Nuova promo SPID attiva per tutto il weekend" required><?php echo htmlspecialchars($announcementEditing['message'] ?? '', ENT_QUOTES); ?></textarea>
                            <small class="text-muted">Max 280 caratteri · usa il selettore qui sotto per inserire icone neon</small>
                        </div>
                        <div class="mb-4" data-announcement-icon-picker data-target="#announcement_message">
                            <label class="form-label">Selettore icone</label>
                            <div class="input-group">
                                <select class="form-select" aria-label="Icona da inserire" data-icon-select>
                                    <option value="">Scegli un'icona</option>
                                    <?php foreach ($newsIconOptions as $symbol => $label): ?>
                                        <option value="<?php echo htmlspecialchars($symbol, ENT_QUOTES); ?>"><?php echo htmlspecialchars($symbol . ' · ' . $label, ENT_QUOTES); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-outline-primary" type="button" data-insert-icon>Aggiungi</button>
                            </div>
                            <small class="text-muted">Il simbolo viene convertito automaticamente in &lt;span data-icon&gt;.</small>
                        </div>
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="announcement_active" name="is_active" <?php echo isset($announcementEditing['is_active']) ? ((int) $announcementEditing['is_active'] === 1 ? 'checked' : '') : 'checked'; ?>>
                            <label class="form-check-label" for="announcement_active">Visibile sul sito</label>
                        </div>
                        <button class="btn btn-primary w-100 mt-auto" type="submit"><?php echo $announcementEditing ? 'Aggiorna news' : 'Pubblica news'; ?></button>
                    </form>
                </div>
                <div class="col-lg-7">
                    <?php if (empty($announcementsAdmin)): ?>
                        <p class="text-muted mb-0">Ancora nessuna news impostata.</p>
                    <?php else: ?>
                        <div class="admin-news-slider" data-news-slider>
                            <button class="btn btn-outline-secondary btn-sm rounded-circle admin-slider-nav" type="button" aria-label="News precedente" data-news-slider-prev disabled>&lsaquo;</button>
                            <div class="admin-news-slider__viewport" data-news-slider-viewport>
                                <div class="admin-news-slider__track">
                                    <?php foreach ($announcementSlides as $slide): ?>
                                        <div class="admin-news-slide">
                                            <ul class="list-unstyled mb-0">
                                                <?php foreach ($slide as $news): ?>
                                                    <?php
                                                        $timestamp = $news['updated_at'] ?: $news['created_at'];
                                                        $publishedLabel = $timestamp ? date('d/m H:i', strtotime((string) $timestamp)) : '-';
                                                    ?>
                                                    <li class="border rounded-3 p-3 mb-2 d-flex flex-column flex-md-row justify-content-between gap-3">
                                                        <div class="flex-grow-1">
                                                            <div class="fw-semibold"><?php echo ap_format_announcement_message((string) $news['message']); ?></div>
                                                            <div class="small text-muted">Pubblicata il <?php echo $publishedLabel; ?></div>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2 text-nowrap">
                                                            <span class="badge <?php echo (int) $news['is_active'] === 1 ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'; ?>">
                                                                <?php echo (int) $news['is_active'] === 1 ? 'Visibile' : 'Nascosta'; ?>
                                                            </span>
                                                            <a class="btn btn-sm btn-outline-secondary" href="<?php echo htmlspecialchars($adminUrl(['announcement' => (int) $news['id']], 'annunci'), ENT_QUOTES); ?>">Modifica</a>
                                                            <form method="post" onsubmit="return confirm('Rimuovere definitivamente questa news?');" class="mb-0">
                                                                <input type="hidden" name="ap_action" value="admin_delete_announcement">
                                                                <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl([], 'annunci'), ENT_QUOTES); ?>">
                                                                <input type="hidden" name="announcement_id" value="<?php echo (int) $news['id']; ?>">
                                                                <button class="btn btn-sm btn-danger" type="submit">Elimina</button>
                                                            </form>
                                                        </div>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <button class="btn btn-outline-secondary btn-sm rounded-circle admin-slider-nav" type="button" aria-label="News successiva" data-news-slider-next>&rsaquo;</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($section === 'faq-bot'): ?>
        <div class="admin-card mb-5" id="faq-bot">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0"><?php echo $faqEditing ? 'Modifica risposta bot' : 'FAQ Chatbot'; ?></h4>
                    <small class="text-muted">Gestisci le risposte informative per il widget in basso a destra</small>
                </div>
                <?php if ($faqEditing): ?>
                    <a class="small" href="<?php echo htmlspecialchars($adminUrl([], 'faq-bot'), ENT_QUOTES); ?>">Annulla modifica</a>
                <?php endif; ?>
            </div>
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-5">
                    <form method="post" class="h-100 d-flex flex-column">
                        <input type="hidden" name="ap_action" value="admin_save_faq">
                        <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl([], 'faq-bot'), ENT_QUOTES); ?>">
                        <?php if ($faqEditing): ?>
                            <input type="hidden" name="faq_id" value="<?php echo (int) $faqEditing['id']; ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label" for="faq_question">Domanda</label>
                            <input class="form-control" type="text" id="faq_question" name="question" maxlength="255" required value="<?php echo htmlspecialchars($faqEditing['question'] ?? '', ENT_QUOTES); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="faq_answer">Risposta</label>
                            <textarea class="form-control" id="faq_answer" name="answer" rows="4" required><?php echo htmlspecialchars($faqEditing['answer'] ?? '', ENT_QUOTES); ?></textarea>
                            <small class="text-muted">Puoi usare più paragrafi, verranno mostrati nel bot.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="faq_category">Categoria (facoltativa)</label>
                            <input class="form-control" type="text" id="faq_category" name="category" value="<?php echo htmlspecialchars($faqEditing['category'] ?? '', ENT_QUOTES); ?>" placeholder="Es. Servizi digitali">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="faq_keywords">Parole chiave</label>
                            <input class="form-control" type="text" id="faq_keywords" name="keywords" value="<?php echo htmlspecialchars($faqEditing['keywords'] ?? '', ENT_QUOTES); ?>" placeholder="Es. SPID, firma digitale">
                            <small class="text-muted">Usate per migliorare la ricerca interna.</small>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label" for="faq_sort">Ordine</label>
                                <input class="form-control" type="number" id="faq_sort" name="sort_order" value="<?php echo htmlspecialchars((string) ($faqEditing['sort_order'] ?? 0), ENT_QUOTES); ?>" min="0">
                            </div>
                            <div class="col-sm-6 d-flex align-items-center">
                                <div class="form-check form-switch mt-3 mt-sm-0">
                                    <input class="form-check-input" type="checkbox" id="faq_active" name="is_active" <?php echo isset($faqEditing['is_active']) ? ((int) $faqEditing['is_active'] === 1 ? 'checked' : '') : 'checked'; ?>>
                                    <label class="form-check-label" for="faq_active">Visibile ai clienti</label>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary w-100 mt-auto" type="submit"><?php echo $faqEditing ? 'Aggiorna risposta' : 'Aggiungi FAQ'; ?></button>
                    </form>
                </div>
                <div class="col-lg-7">
                    <?php if (empty($faqsAdmin)): ?>
                        <p class="text-muted mb-0">Nessuna FAQ registrata. Aggiungi le prime domande per attivare il bot.</p>
                    <?php else: ?>
                        <div class="admin-news-slider admin-faq-slider" data-news-slider>
                            <button class="btn btn-outline-secondary btn-sm rounded-circle admin-slider-nav" type="button" aria-label="FAQ precedenti" data-news-slider-prev disabled>&lsaquo;</button>
                            <div class="admin-news-slider__viewport" data-news-slider-viewport>
                                <div class="admin-news-slider__track">
                                    <?php foreach ($faqSlides as $slide): ?>
                                        <div class="admin-news-slide">
                                            <ul class="list-unstyled mb-0">
                                                <?php foreach ($slide as $faq): ?>
                                                    <li class="border rounded-3 p-3 mb-3">
                                                        <article class="admin-faq-item d-flex flex-column gap-2">
                                                            <div>
                                                                <strong><?php echo htmlspecialchars($faq['question'], ENT_QUOTES); ?></strong>
                                                                <div class="small text-muted">
                                                                    <?php if (!empty($faq['category'])): ?>
                                                                        <span><?php echo htmlspecialchars($faq['category'], ENT_QUOTES); ?></span> ·
                                                                    <?php endif; ?>
                                                                    Ordine <?php echo (int) $faq['sort_order']; ?>
                                                                </div>
                                                            </div>
                                                            <p class="small text-muted mb-0"><?php echo nl2br(htmlspecialchars(mb_strimwidth($faq['answer'], 0, 220, '…'), ENT_QUOTES)); ?></p>
                                                            <div class="d-flex align-items-center gap-2 text-nowrap">
                                                                <span class="badge <?php echo (int) $faq['is_active'] === 1 ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'; ?>">
                                                                    <?php echo (int) $faq['is_active'] === 1 ? 'Attiva' : 'Nascosta'; ?>
                                                                </span>
                                                                <a class="btn btn-sm btn-outline-secondary" href="<?php echo htmlspecialchars($adminUrl(['faq' => (int) $faq['id']], 'faq-bot'), ENT_QUOTES); ?>">Modifica</a>
                                                                <form method="post" onsubmit="return confirm('Rimuovere questa FAQ dal bot?');" class="mb-0">
                                                                    <input type="hidden" name="ap_action" value="admin_delete_faq">
                                                                    <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl([], 'faq-bot'), ENT_QUOTES); ?>">
                                                                    <input type="hidden" name="faq_id" value="<?php echo (int) $faq['id']; ?>">
                                                                    <button class="btn btn-sm btn-danger" type="submit">Elimina</button>
                                                                </form>
                                                            </div>
                                                        </article>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <button class="btn btn-outline-secondary btn-sm rounded-circle admin-slider-nav" type="button" aria-label="FAQ successive" data-news-slider-next>&rsaquo;</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($section === 'dashboard'): ?>
        <div class="admin-card mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-0">Carrelli abbandonati</h4>
                    <small class="text-muted">Promemoria automatici dopo 3 ore di inattività</small>
                </div>
                <span class="badge bg-light text-dark">Da contattare <?php echo $abandonedPendingCount; ?></span>
            </div>
            <?php if (empty($abandonedCarts)): ?>
                <p class="text-muted mb-0">Nessun carrello salvato al momento.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Articoli</th>
                                <th>Valore</th>
                                <th>Ultima attività</th>
                                <th>Stato</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($abandonedCarts as $cartRow): ?>
                                <?php
                                    $itemsSnapshot = json_decode((string) ($cartRow['cart_snapshot'] ?? '[]'), true) ?: [];
                                    $itemCount = count($itemsSnapshot);
                                    $emailLabel = $cartRow['email'] ?: 'Sessione ' . substr($cartRow['session_id'], 0, 8);
                                    $lastActivity = $cartRow['last_activity'] ? date('d/m H:i', strtotime((string) $cartRow['last_activity'])) : '-';
                                    $statusPending = empty($cartRow['notified_at']);
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($emailLabel, ENT_QUOTES); ?></strong>
                                        <?php if (!empty($cartRow['user_id'])): ?>
                                            <div class="small text-muted">User #<?php echo (int) $cartRow['user_id']; ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $itemCount; ?> prodotti</td>
                                    <td>
                                        <?php echo ap_price_format((int) ($cartRow['total_cents'] ?? 0)); ?>
                                        <?php if ((int) ($cartRow['discount_cents'] ?? 0) > 0): ?>
                                            <div class="small text-success">- <?php echo ap_price_format((int) $cartRow['discount_cents']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $lastActivity; ?></td>
                                    <td>
                                        <span class="badge <?php echo $statusPending ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success'; ?>">
                                            <?php echo $statusPending ? 'In attesa' : 'Promemoria inviato'; ?>
                                        </span>
                                        <?php if (!empty($cartRow['notified_at'])): ?>
                                            <div class="small text-muted"><?php echo date('d/m H:i', strtotime((string) $cartRow['notified_at'])); ?></div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if ($section === 'catalogo'): ?>
        <div class="row g-4 align-items-stretch">
            <div class="col-md-6 col-xl-5">
                <div class="admin-card h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0"><?php echo $editingProduct ? 'Modifica prodotto' : 'Nuovo prodotto'; ?></h4>
                        <?php if ($editingProduct): ?>
                            <a class="small" href="<?php echo htmlspecialchars($adminUrl([], 'catalogo'), ENT_QUOTES); ?>">Annulla modifica</a>
                        <?php endif; ?>
                    </div>
                    <form method="post" class="product-form" enctype="multipart/form-data">
                        <input type="hidden" name="ap_action" value="admin_save_product">
                        <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl([], 'catalogo'), ENT_QUOTES); ?>">
                        <?php if ($editingProduct): ?>
                            <input type="hidden" name="product_id" value="<?php echo (int) $editingProduct['id']; ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label" for="prod_name">Nome</label>
                            <input class="form-control" type="text" id="prod_name" name="name" required value="<?php echo htmlspecialchars($editingProduct['name'] ?? '', ENT_QUOTES); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_price">Prezzo (€)</label>
                            <input class="form-control" type="number" step="0.01" id="prod_price" name="price" required value="<?php echo isset($editingProduct['price_cents']) ? number_format($editingProduct['price_cents'] / 100, 2, '.', '') : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_slug">Slug</label>
                            <input class="form-control" type="text" id="prod_slug" name="slug" value="<?php echo htmlspecialchars($editingProduct['slug'] ?? '', ENT_QUOTES); ?>" placeholder="sim-dati-europa-100gb">
                            <small class="text-muted">Lascia vuoto per generarlo automaticamente.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_category">Categoria shop</label>
                            <input class="form-control" type="text" id="prod_category" name="category_key" value="<?php echo htmlspecialchars($editingProduct['category_key'] ?? '', ENT_QUOTES); ?>" placeholder="es. certificati, visure, pratiche">
                            <small class="text-muted">Serve per i filtri avanzati della pagina shop. Lascia vuoto per auto-categorizzazione.</small>
                        </div>
                        <?php $editingFulfillment = strtolower((string) ($editingProduct['fulfillment_type'] ?? 'digital')); ?>
                        <div class="mb-3">
                            <label class="form-label" for="prod_fulfillment">Tipologia evasione</label>
                            <select class="form-select" id="prod_fulfillment" name="fulfillment_type" required>
                                <option value="digital" <?php echo $editingFulfillment === 'digital' ? 'selected' : ''; ?>>Servizio digitale</option>
                                <option value="physical" <?php echo $editingFulfillment === 'physical' ? 'selected' : ''; ?>>Prodotto fisico / spedizione</option>
                            </select>
                            <small class="text-muted">Determina badge e workflow logistico, predefinito digitale.</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="prod_sku">SKU</label>
                                    <input class="form-control" type="text" id="prod_sku" name="sku" value="<?php echo htmlspecialchars($editingProduct['sku'] ?? '', ENT_QUOTES); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="prod_stock">Stock</label>
                                    <input class="form-control" type="number" id="prod_stock" name="stock" value="<?php echo htmlspecialchars((string) ($editingProduct['stock'] ?? 0), ENT_QUOTES); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_image">URL immagine</label>
                            <input class="form-control" type="text" id="prod_image" name="image_url" value="<?php echo htmlspecialchars($editingProduct['image_url'] ?? '', ENT_QUOTES); ?>" placeholder="assets/img/og-image.jpg">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_image_file">Carica immagine</label>
                            <input class="form-control" type="file" id="prod_image_file" name="image_file" accept="image/png,image/jpeg,image/webp">
                            <small class="text-muted">Seleziona un file per sostituire l'immagine attuale.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_description">Descrizione</label>
                            <textarea class="form-control" id="prod_description" name="description" rows="4"><?php echo htmlspecialchars($editingProduct['description'] ?? '', ENT_QUOTES); ?></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Campi personalizzati (per prodotti digitali)</label>
                            <small class="text-muted d-block mb-3">Definisci i campi che il cliente deve compilare per evadere l'ordine.</small>
                            <div id="custom-fields-container">
                                <?php foreach ($customFields as $field): ?>
                                    <div class="custom-field-item border rounded p-3 mb-3" data-field-id="<?php echo (int) $field['id']; ?>">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label">Nome campo</label>
                                                <input class="form-control" type="text" name="custom_fields[<?php echo (int) $field['id']; ?>][name]" value="<?php echo htmlspecialchars($field['field_name'], ENT_QUOTES); ?>" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Etichetta</label>
                                                <input class="form-control" type="text" name="custom_fields[<?php echo (int) $field['id']; ?>][label]" value="<?php echo htmlspecialchars($field['field_label'], ENT_QUOTES); ?>" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Tipo</label>
                                                <select class="form-select" name="custom_fields[<?php echo (int) $field['id']; ?>][type]">
                                                    <option value="text" <?php echo $field['field_type'] === 'text' ? 'selected' : ''; ?>>Testo</option>
                                                    <option value="textarea" <?php echo $field['field_type'] === 'textarea' ? 'selected' : ''; ?>>Area testo</option>
                                                    <option value="select" <?php echo $field['field_type'] === 'select' ? 'selected' : ''; ?>>Selezione</option>
                                                    <option value="checkbox" <?php echo $field['field_type'] === 'checkbox' ? 'selected' : ''; ?>>Checkbox</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Opzioni (per select)</label>
                                                <input class="form-control" type="text" name="custom_fields[<?php echo (int) $field['id']; ?>][options]" value="<?php echo htmlspecialchars($field['field_options'] ?? '', ENT_QUOTES); ?>" placeholder="Opzione1,Opzione2">
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">Obbligatorio</label>
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" name="custom_fields[<?php echo (int) $field['id']; ?>][required]" <?php echo (int) $field['is_required'] === 1 ? 'checked' : ''; ?>>
                                                </div>
                                            </div>
                                            <div class="col-md-1 text-end">
                                                <button type="button" class="btn btn-sm btn-danger remove-field">Rimuovi</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-outline-primary" id="add-custom-field">Aggiungi campo</button>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="prod_active" name="is_active" <?php echo isset($editingProduct['is_active']) ? ((int) $editingProduct['is_active'] === 1 ? 'checked' : '') : 'checked'; ?>>
                            <label class="form-check-label" for="prod_active">Prodotto visibile nello shop</label>
                        </div>
                        <button class="btn btn-primary w-100" type="submit"><?php echo $editingProduct ? 'Aggiorna' : 'Pubblica prodotto'; ?></button>
                    </form>
                </div>
            </div>
            <div class="col-md-6 col-xl-7">
                <div class="admin-card h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="mb-0">Catalogo prodotti</h4>
                            <span class="badge bg-light text-dark"><?php echo count($products); ?> prodotti</span>
                        </div>
                    </div>
                    <?php if (empty($products)): ?>
                        <p class="text-muted mb-0">Ancora nessun prodotto nel catalogo.</p>
                    <?php else: ?>
                        <div class="table-responsive flex-grow-1">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Prezzo</th>
                                        <th>Categoria</th>
                                        <th>Stato</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td>#<?php echo (int) $product['id']; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></strong>
                                                <?php if (!empty($product['sku'])): ?>
                                                    <div class="small text-muted">SKU: <?php echo htmlspecialchars($product['sku'], ENT_QUOTES); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo ap_price_format((int) $product['price_cents']); ?></td>
                                            <td><?php echo htmlspecialchars($product['category_key'] ?? '-', ENT_QUOTES); ?></td>
                                            <td>
                                                <span class="badge <?php echo (int) $product['is_active'] === 1 ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'; ?>">
                                                    <?php echo (int) $product['is_active'] === 1 ? 'Attivo' : 'Nascosto'; ?>
                                                </span>
                                            </td>
                                    <td>
                                                <a class="btn btn-sm btn-outline-primary" href="<?php echo htmlspecialchars($adminUrl(['id' => (int) $product['id']], 'edit_product'), ENT_QUOTES); ?>">Modifica</a>
                                                <form method="post" onsubmit="return confirm('Eliminare definitivamente questo prodotto?');" class="d-inline ms-2">
                                                    <input type="hidden" name="ap_action" value="admin_delete_product">
                                                    <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl([], 'catalogo'), ENT_QUOTES); ?>">
                                                    <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
                                                    <button class="btn btn-sm btn-danger" type="submit">Elimina</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
            <?php endif; ?>
        <?php if ($section === 'edit_product'): ?>
        <div class="row g-4 align-items-stretch">
            <div class="col-12">
                <div class="admin-card h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0"><?php echo $editingProduct ? 'Modifica prodotto' : 'Nuovo prodotto'; ?></h4>
                        <a class="small" href="<?php echo htmlspecialchars($adminUrl([], 'catalogo'), ENT_QUOTES); ?>">Annulla modifica</a>
                    </div>
                    <form method="post" class="product-form" enctype="multipart/form-data">
                        <input type="hidden" name="ap_action" value="admin_save_product">
                        <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl([], 'catalogo'), ENT_QUOTES); ?>">
                        <?php if ($editingProduct): ?>
                            <input type="hidden" name="product_id" value="<?php echo (int) $editingProduct['id']; ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label" for="prod_name">Nome</label>
                            <input class="form-control" type="text" id="prod_name" name="name" required value="<?php echo htmlspecialchars($editingProduct['name'] ?? '', ENT_QUOTES); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_price">Prezzo (€)</label>
                            <input class="form-control" type="number" step="0.01" id="prod_price" name="price" required value="<?php echo isset($editingProduct['price_cents']) ? number_format($editingProduct['price_cents'] / 100, 2, '.', '') : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_slug">Slug</label>
                            <input class="form-control" type="text" id="prod_slug" name="slug" value="<?php echo htmlspecialchars($editingProduct['slug'] ?? '', ENT_QUOTES); ?>" placeholder="sim-dati-europa-100gb">
                            <small class="text-muted">Lascia vuoto per generarlo automaticamente.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_category">Categoria shop</label>
                            <input class="form-control" type="text" id="prod_category" name="category_key" value="<?php echo htmlspecialchars($editingProduct['category_key'] ?? '', ENT_QUOTES); ?>" placeholder="es. certificati, visure, pratiche">
                            <small class="text-muted">Serve per i filtri avanzati della pagina shop. Lascia vuoto per auto-categorizzazione.</small>
                        </div>
                        <?php $editingFulfillment = strtolower((string) ($editingProduct['fulfillment_type'] ?? 'digital')); ?>
                        <div class="mb-3">
                            <label class="form-label" for="prod_fulfillment">Tipologia evasione</label>
                            <select class="form-select" id="prod_fulfillment" name="fulfillment_type" required>
                                <option value="digital" <?php echo $editingFulfillment === 'digital' ? 'selected' : ''; ?>>Servizio digitale</option>
                                <option value="physical" <?php echo $editingFulfillment === 'physical' ? 'selected' : ''; ?>>Prodotto fisico / spedizione</option>
                            </select>
                            <small class="text-muted">Determina badge e workflow logistico, predefinito digitale.</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="prod_sku">SKU</label>
                                    <input class="form-control" type="text" id="prod_sku" name="sku" value="<?php echo htmlspecialchars($editingProduct['sku'] ?? '', ENT_QUOTES); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="prod_stock">Stock</label>
                                    <input class="form-control" type="number" id="prod_stock" name="stock" value="<?php echo htmlspecialchars((string) ($editingProduct['stock'] ?? 0), ENT_QUOTES); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_image">URL immagine</label>
                            <input class="form-control" type="text" id="prod_image" name="image_url" value="<?php echo htmlspecialchars($editingProduct['image_url'] ?? '', ENT_QUOTES); ?>" placeholder="assets/img/og-image.jpg">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_image_file">Carica immagine</label>
                            <input class="form-control" type="file" id="prod_image_file" name="image_file" accept="image/png,image/jpeg,image/webp">
                            <small class="text-muted">Seleziona un file per sostituire l'immagine attuale.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="prod_description">Descrizione</label>
                            <textarea class="form-control" id="prod_description" name="description" rows="4"><?php echo htmlspecialchars($editingProduct['description'] ?? '', ENT_QUOTES); ?></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Campi personalizzati (per prodotti digitali)</label>
                            <small class="text-muted d-block mb-3">Definisci i campi che il cliente deve compilare per evadere l'ordine.</small>
                            <div id="custom-fields-container">
                                <?php foreach ($customFields as $field): ?>
                                    <div class="custom-field-item border rounded p-3 mb-3" data-field-id="<?php echo (int) $field['id']; ?>">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label">Nome campo</label>
                                                <input class="form-control" type="text" name="custom_fields[<?php echo (int) $field['id']; ?>][name]" value="<?php echo htmlspecialchars($field['field_name'], ENT_QUOTES); ?>" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Etichetta</label>
                                                <input class="form-control" type="text" name="custom_fields[<?php echo (int) $field['id']; ?>][label]" value="<?php echo htmlspecialchars($field['field_label'], ENT_QUOTES); ?>" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Tipo</label>
                                                <select class="form-select" name="custom_fields[<?php echo (int) $field['id']; ?>][type]">
                                                    <option value="text" <?php echo $field['field_type'] === 'text' ? 'selected' : ''; ?>>Testo</option>
                                                    <option value="textarea" <?php echo $field['field_type'] === 'textarea' ? 'selected' : ''; ?>>Area testo</option>
                                                    <option value="select" <?php echo $field['field_type'] === 'select' ? 'selected' : ''; ?>>Selezione</option>
                                                    <option value="checkbox" <?php echo $field['field_type'] === 'checkbox' ? 'selected' : ''; ?>>Checkbox</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Opzioni (per select)</label>
                                                <input class="form-control" type="text" name="custom_fields[<?php echo (int) $field['id']; ?>][options]" value="<?php echo htmlspecialchars($field['field_options'] ?? '', ENT_QUOTES); ?>" placeholder="Opzione1,Opzione2">
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">Obbligatorio</label>
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" name="custom_fields[<?php echo (int) $field['id']; ?>][required]" <?php echo (int) $field['is_required'] === 1 ? 'checked' : ''; ?>>
                                                </div>
                                            </div>
                                            <div class="col-md-1 text-end">
                                                <button type="button" class="btn btn-sm btn-danger remove-field">Rimuovi</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-outline-primary" id="add-custom-field">Aggiungi campo</button>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="prod_active" name="is_active" <?php echo isset($editingProduct['is_active']) ? ((int) $editingProduct['is_active'] === 1 ? 'checked' : '') : 'checked'; ?>>
                            <label class="form-check-label" for="prod_active">Prodotto visibile nello shop</label>
                        </div>
                        <button class="btn btn-primary w-100" type="submit"><?php echo $editingProduct ? 'Aggiorna' : 'Pubblica prodotto'; ?></button>
                    </form>
                </div>
            </div>
        </div>
            <?php endif; ?>
        <?php if ($section === 'ordini'): ?>
        <div class="admin-card">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
                <div>
                    <h4 class="mb-0">Ordini</h4>
                    <span class="badge bg-light text-dark"><?php echo $ordersTotal; ?> risultati</span>
                </div>
                <form class="row g-2 align-items-center flex-grow-1" method="get" action="<?php echo htmlspecialchars($adminBasePath, ENT_QUOTES); ?>">
                    <input type="hidden" name="orders_page" value="1">
                    <div class="col-lg-4">
                        <input class="form-control" type="search" name="q" value="<?php echo htmlspecialchars($searchTerm, ENT_QUOTES); ?>" placeholder="Cerca ID, email o nome">
                    </div>
                    <div class="col-lg-3">
                        <select class="form-select" name="status">
                            <option value="all">Tutti gli stati</option>
                            <?php foreach ($orderStatuses as $key => $label): ?>
                                <option value="<?php echo $key; ?>" <?php echo $statusFilter === $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <select class="form-select" name="shipping">
                            <option value="all">Tutte le spedizioni</option>
                            <?php foreach ($shippingStatuses as $key => $label): ?>
                                <option value="<?php echo $key; ?>" <?php echo $shippingFilter === $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-2 d-flex gap-2 align-items-center">
                        <button class="btn btn-outline-secondary flex-grow-1" type="submit">Filtra</button>
                        <a class="btn btn-link text-nowrap p-0" href="<?php echo htmlspecialchars($adminUrl(), ENT_QUOTES); ?>">Reset</a>
                    </div>
                </form>
            </div>
            <?php if (empty($orders)): ?>
                    <p class="text-muted mb-0">Ancora nessun ordine.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Totale</th>
                                <th>Pagamento</th>
                                <th>Spedizione</th>
                                <th class="d-none d-lg-table-cell">Enterprise</th>
                                <th>Allegati digitali</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <?php
                                    $addons = ap_order_meta_array($order['addons'] ?? null);
                                    $opsChannels = ap_order_meta_array($order['ops_channels'] ?? null);
                                    $attachments = ap_order_procurement_files($order['procurement_files'] ?? null);
                                    $slaBadges = [
                                        'core' => 'Assistenza standard 24h',
                                        'priority' => 'Gestione prioritaria 12h',
                                        'mission' => 'Supporto critico 4h',
                                    ];
                                    $windowLabels = [
                                        'standard' => '08:00-18:00',
                                        'early' => '06:00-09:00',
                                        'late' => '18:00-22:00',
                                    ];
                                    $addonLabels = array_map(static function ($value) {
                                        return ucwords(str_replace('-', ' ', (string) $value));
                                    }, $addons);
                                    $opsLabels = array_map(static function ($value) {
                                        return ucfirst((string) $value);
                                    }, $opsChannels);
                                    $orderItems = ap_fetch_order_items((int) $order['id']);
                                    $digitalProducts = array_filter($orderItems, function($item) {
                                        $product = ap_find_product((int) $item['product_id']);
                                        return $product && strtolower((string) $product['fulfillment_type']) === 'digital';
                                    });
                                ?>
                                <tr>
                                    <td>#<?php echo (int) $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name'] ?? '', ENT_QUOTES); ?></td>
                                    <td>
                                        <?php echo ap_price_format((int) $order['total_cents']); ?>
                                        <?php if ((int) ($order['discount_cents'] ?? 0) > 0): ?>
                                            <?php $couponLabel = !empty($order['coupon_code']) ? 'Coupon ' . htmlspecialchars($order['coupon_code'], ENT_QUOTES) : 'Coupon'; ?>
                                            <div class="small text-success"><?php echo $couponLabel; ?> · -<?php echo ap_price_format((int) $order['discount_cents']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-dot status-<?php echo htmlspecialchars($order['payment_status'] ?? 'pending', ENT_QUOTES); ?>"><?php echo ucfirst($order['payment_status'] ?? 'pending'); ?></span>
                                        <div class="small text-muted"><?php echo strtoupper($order['payment_method'] ?? '-'); ?></div>
                                    </td>
                                    <td>
                                        <span class="status-dot status-<?php echo htmlspecialchars($order['shipping_status'] ?? 'preparing', ENT_QUOTES); ?>"><?php echo $shippingStatuses[$order['shipping_status'] ?? 'preparing'] ?? 'In preparazione'; ?></span>
                                        <?php if (!empty($order['tracking_code'])): ?>
                                            <div class="small text-muted">Tracking: <?php echo htmlspecialchars($order['tracking_code'], ENT_QUOTES); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <?php if (!empty($order['po_number'])): ?>
                                            <div class="small">PO: <?php echo htmlspecialchars($order['po_number'], ENT_QUOTES); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($order['cost_center'])): ?>
                                            <div class="small text-muted">Centro: <?php echo htmlspecialchars($order['cost_center'], ENT_QUOTES); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($order['sla_plan'])): ?>
                                            <span class="badge bg-primary-subtle text-primary me-1 mt-1 d-inline-block"><?php echo htmlspecialchars($slaBadges[$order['sla_plan']] ?? strtoupper($order['sla_plan']), ENT_QUOTES); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($order['delivery_window'])): ?>
                                            <span class="badge bg-light text-dark mt-1 d-inline-block">Slot <?php echo htmlspecialchars($windowLabels[$order['delivery_window']] ?? $order['delivery_window'], ENT_QUOTES); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($addons)): ?>
                                            <div class="small mt-2">
                                                Add-on: <?php echo htmlspecialchars(implode(', ', $addonLabels), ENT_QUOTES); ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($opsChannels)): ?>
                                            <div class="small text-muted">Ops: <?php echo htmlspecialchars(implode(', ', $opsLabels), ENT_QUOTES); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($order['contract_ref'])): ?>
                                            <div class="small">Contratto: <?php echo htmlspecialchars($order['contract_ref'], ENT_QUOTES); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($attachments)): ?>
                                            <div class="small mt-2">Documenti:
                                                <?php foreach ($attachments as $file): ?>
                                                    <a class="d-block" href="<?php echo htmlspecialchars($file['path'], ENT_QUOTES); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($file['name'] ?? basename($file['path']), ENT_QUOTES); ?></a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($digitalProducts)): ?>
                                            <?php foreach ($digitalProducts as $item): ?>
                                                <?php $product = ap_find_product((int) $item['product_id']); ?>
                                                <div class="mb-2">
                                                    <strong><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></strong>
                                                    <?php if (!empty($item['digital_file_path'])): ?>
                                                        <div class="small text-success">Allegato: <a href="?page=download&order_id=<?php echo (int) $order['id']; ?>&product_id=<?php echo (int) $item['product_id']; ?>" target="_blank">Scarica</a></div>
                                                    <?php else: ?>
                                                        <form method="post" enctype="multipart/form-data" class="d-flex gap-2">
                                                            <input type="hidden" name="ap_action" value="admin_upload_digital_file">
                                                            <input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>">
                                                            <input type="hidden" name="product_id" value="<?php echo (int) $item['product_id']; ?>">
                                                            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl(), ENT_QUOTES); ?>">
                                                            <input class="form-control form-control-sm" type="file" name="digital_file" accept=".pdf,.doc,.docx,.jpg,.png" required>
                                                            <button class="btn btn-sm btn-outline-primary" type="submit">Carica</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="post" class="row g-2 align-items-center">
                                            <input type="hidden" name="ap_action" value="admin_update_order">
                                            <input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>">
                                            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl(), ENT_QUOTES); ?>">
                                            <div class="col-md-6">
                                                <label class="visually-hidden" for="order-status-<?php echo (int) $order['id']; ?>">Stato ordine</label>
                                                <select class="form-select form-select-sm" id="order-status-<?php echo (int) $order['id']; ?>" name="status">
                                                    <?php foreach ($orderStatuses as $key => $label): ?>
                                                        <option value="<?php echo $key; ?>" <?php echo ($order['status'] === $key) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="visually-hidden" for="ship-status-<?php echo (int) $order['id']; ?>">Stato spedizione</label>
                                                <select class="form-select form-select-sm" id="ship-status-<?php echo (int) $order['id']; ?>" name="shipping_status">
                                                    <?php foreach ($shippingStatuses as $key => $label): ?>
                                                        <option value="<?php echo $key; ?>" <?php echo (($order['shipping_status'] ?? 'preparing') === $key) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="visually-hidden" for="ship-method-<?php echo (int) $order['id']; ?>">Metodo spedizione</label>
                                                <select class="form-select form-select-sm" id="ship-method-<?php echo (int) $order['id']; ?>" name="shipping_method">
                                                    <?php foreach ($shippingMethods as $key => $option): ?>
                                                        <option value="<?php echo $key; ?>" <?php echo (($order['shipping_method'] ?? 'standard') === $key) ? 'selected' : ''; ?>><?php echo $option['label']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="visually-hidden" for="tracking-<?php echo (int) $order['id']; ?>">Tracking</label>
                                                <input class="form-control form-control-sm" type="text" id="tracking-<?php echo (int) $order['id']; ?>" name="tracking_code" placeholder="Tracking" value="<?php echo htmlspecialchars($order['tracking_code'] ?? '', ENT_QUOTES); ?>">
                                            </div>
                                            <div class="col-12 text-end">
                                                <button class="btn btn-sm btn-outline-secondary" type="submit">Aggiorna</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($ordersPages > 1): ?>
                    <nav class="mt-3">
                        <ul class="pagination pagination-sm mb-0 flex-wrap">
                            <?php $prevPage = max(1, $ordersPage - 1); ?>
                            <li class="page-item <?php echo $ordersPage === 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo htmlspecialchars($buildOrdersPageUrl($prevPage), ENT_QUOTES); ?>" aria-label="Pagina precedente">&laquo;</a>
                            </li>
                            <?php for ($pageNumber = 1; $pageNumber <= $ordersPages; $pageNumber++): ?>
                                <li class="page-item <?php echo $pageNumber === $ordersPage ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo htmlspecialchars($buildOrdersPageUrl($pageNumber), ENT_QUOTES); ?>"><?php echo $pageNumber; ?></a>
                                </li>
                            <?php endfor; ?>
                            <?php $nextPage = min($ordersPages, $ordersPage + 1); ?>
                            <li class="page-item <?php echo $ordersPage === $ordersPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo htmlspecialchars($buildOrdersPageUrl($nextPage), ENT_QUOTES); ?>" aria-label="Pagina successiva">&raquo;</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if ($section === 'coupons'): ?>
        <div class="admin-card mt-5" id="coupons">
            <div class="row g-4 align-items-start">
                <div class="col-lg-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0"><?php echo $editingCoupon ? 'Modifica coupon' : 'Nuovo coupon'; ?></h4>
                        <?php if ($editingCoupon): ?>
                            <a class="small" href="<?php echo htmlspecialchars($adminUrl([], 'coupons'), ENT_QUOTES); ?>">Annulla modifica</a>
                        <?php endif; ?>
                    </div>
                    <?php
                        $couponAmountValue = $editingCoupon ? number_format(((int) ($editingCoupon['amount_cents'] ?? 0)) / 100, 2, '.', '') : '';
                        $couponMinValue = $editingCoupon ? number_format(((int) ($editingCoupon['min_total_cents'] ?? 0)) / 100, 2, '.', '') : '';
                        $couponStartsValue = ($editingCoupon && !empty($editingCoupon['starts_at'])) ? date('Y-m-d\TH:i', strtotime((string) $editingCoupon['starts_at'])) : '';
                        $couponEndsValue = ($editingCoupon && !empty($editingCoupon['ends_at'])) ? date('Y-m-d\TH:i', strtotime((string) $editingCoupon['ends_at'])) : '';
                        $couponCodeValue = $editingCoupon ? (string) $editingCoupon['code'] : ap_generate_unique_coupon_code();
                    ?>
                    <form method="post" class="coupon-form">
                        <input type="hidden" name="ap_action" value="admin_save_coupon">
                        <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($adminUrl([], 'coupons'), ENT_QUOTES); ?>">
                        <?php if ($editingCoupon): ?>
                            <input type="hidden" name="coupon_id" value="<?php echo (int) $editingCoupon['id']; ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label" for="coupon_code_admin">Codice</label>
                            <input class="form-control" type="text" id="coupon_code_admin" name="code" required value="<?php echo htmlspecialchars($couponCodeValue, ENT_QUOTES); ?>" placeholder="PB2024">
                            <?php if (!$editingCoupon): ?>
                                <small class="text-muted">Generato automaticamente per assicurare l'univocità, modificabile se necessario.</small>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="coupon_desc_admin">Descrizione</label>
                            <input class="form-control" type="text" id="coupon_desc_admin" name="description" value="<?php echo htmlspecialchars($editingCoupon['description'] ?? '', ENT_QUOTES); ?>" placeholder="Visibile solo allo staff">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="coupon_type_admin">Tipo</label>
                                <select class="form-select" id="coupon_type_admin" name="type">
                                    <option value="fixed" <?php echo (($editingCoupon['type'] ?? 'fixed') === 'fixed') ? 'selected' : ''; ?>>Importo fisso</option>
                                    <option value="percent" <?php echo (($editingCoupon['type'] ?? '') === 'percent') ? 'selected' : ''; ?>>Percentuale</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="coupon_value_fixed">Valore fisso (€)</label>
                                <input class="form-control" type="number" step="0.01" min="0" id="coupon_value_fixed" name="amount" value="<?php echo $couponAmountValue; ?>">
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label" for="coupon_value_percent">Sconto (%)</label>
                                <input class="form-control" type="number" min="0" max="100" id="coupon_value_percent" name="percent" value="<?php echo htmlspecialchars((string) ($editingCoupon['percent'] ?? 0), ENT_QUOTES); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="coupon_min_total">Ordine minimo (€)</label>
                                <input class="form-control" type="number" step="0.01" min="0" id="coupon_min_total" name="min_total" value="<?php echo $couponMinValue; ?>">
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label" for="coupon_max_usage">Limite utilizzi</label>
                                <input class="form-control" type="number" min="0" id="coupon_max_usage" name="max_redemptions" value="<?php echo htmlspecialchars((string) ($editingCoupon['max_redemptions'] ?? ''), ENT_QUOTES); ?>" placeholder="Illimitato">
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="coupon_active" name="is_active" <?php echo isset($editingCoupon['is_active']) ? ((int) $editingCoupon['is_active'] === 1 ? 'checked' : '') : 'checked'; ?>>
                                    <label class="form-check-label" for="coupon_active">Coupon attivo</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label" for="coupon_start">Valido dal</label>
                                <input class="form-control" type="datetime-local" id="coupon_start" name="starts_at" value="<?php echo $couponStartsValue; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="coupon_end">Valido fino al</label>
                                <input class="form-control" type="datetime-local" id="coupon_end" name="ends_at" value="<?php echo $couponEndsValue; ?>">
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">Compila solo il campo relativo al tipo selezionato.</small>
                        <button class="btn btn-primary w-100 mt-3" type="submit"><?php echo $editingCoupon ? 'Aggiorna coupon' : 'Crea coupon'; ?></button>
                    </form>
                </div>
                <div class="col-lg-7">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Archivio coupon</h4>
                        <span class="badge bg-light text-dark">Totale <?php echo count($coupons); ?></span>
                    </div>
                    <?php if (empty($coupons)): ?>
                        <p class="text-muted mb-0">Ancora nessun codice promo. Creane uno per attivare campagne.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Codice</th>
                                        <th>Tipo</th>
                                        <th>Valore</th>
                                        <th>Minimo</th>
                                        <th>Utilizzi</th>
                                        <th>Stato</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($coupons as $couponRow): ?>
                                        <?php
                                            $isActive = (int) ($couponRow['is_active'] ?? 0) === 1;
                                            $valueLabel = ($couponRow['type'] ?? 'fixed') === 'percent'
                                                ? ((int) $couponRow['percent']) . '%'
                                                : ap_price_format((int) ($couponRow['amount_cents'] ?? 0));
                                            $minLabel = (int) ($couponRow['min_total_cents'] ?? 0) > 0
                                                ? ap_price_format((int) $couponRow['min_total_cents'])
                                                : 'Nessun minimo';
        
                                            $usageCount = (int) ($couponRow['redemptions_count'] ?? 0);
                                            $usageLimit = $couponRow['max_redemptions'] ?? null;
                                            $usageLabel = $usageLimit ? sprintf('%d / %d', $usageCount, (int) $usageLimit) : (string) $usageCount;
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($couponRow['code'], ENT_QUOTES); ?></strong>
                                                <?php if (!empty($couponRow['description'])): ?>
                                                    <div class="small text-muted"><?php echo htmlspecialchars($couponRow['description'], ENT_QUOTES); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars(ucfirst((string) $couponRow['type']), ENT_QUOTES); ?></td>
                                            <td><?php echo htmlspecialchars($valueLabel, ENT_QUOTES); ?></td>
                                            <td><?php echo htmlspecialchars($minLabel, ENT_QUOTES); ?></td>
                                            <td><?php echo htmlspecialchars($usageLabel, ENT_QUOTES); ?></td>
                                            <td>
                                                <span class="badge <?php echo $isActive ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'; ?>"><?php echo $isActive ? 'Attivo' : 'Pausa'; ?></span>
                                                <?php if (!empty($couponRow['ends_at'])): ?>
                                                    <div class="small text-muted">Fino al <?php echo date('d/m/Y H:i', strtotime((string) $couponRow['ends_at'])); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <a class="btn btn-sm btn-outline-primary" href="<?php echo htmlspecialchars($adminUrl(['coupon' => (int) $couponRow['id']], 'coupons'), ENT_QUOTES); ?>">Modifica</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <script>
            (function () {
                const typeField = document.getElementById('coupon_type_admin');
                if (!typeField) {
                    return;
                }
                const fixedField = document.getElementById('coupon_value_fixed');
                const percentField = document.getElementById('coupon_value_percent');
                const toggleFields = () => {
                    const type = typeField.value;
                    if (fixedField) {
                        fixedField.parentElement.style.opacity = type === 'fixed' ? '1' : '0.5';
                    }
                    if (percentField) {
                        percentField.parentElement.style.opacity = type === 'percent' ? '1' : '0.5';
                    }
                };
                typeField.addEventListener('change', toggleFields);
                toggleFields();
            })();
        </script>
        <script>
            (function () {
                let fieldCounter = 0;
                const container = document.getElementById('custom-fields-container');
                const addBtn = document.getElementById('add-custom-field');
                if (!container || !addBtn) {
                    return;
                }

                const createFieldHTML = (id) => `
                    <div class="custom-field-item border rounded p-3 mb-3" data-field-id="${id}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Nome campo</label>
                                <input class="form-control" type="text" name="custom_fields[${id}][name]" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Etichetta</label>
                                <input class="form-control" type="text" name="custom_fields[${id}][label]" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tipo</label>
                                <select class="form-select" name="custom_fields[${id}][type]">
                                    <option value="text">Testo</option>
                                    <option value="textarea">Area testo</option>
                                    <option value="select">Selezione</option>
                                    <option value="checkbox">Checkbox</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Opzioni (per select)</label>
                                <input class="form-control" type="text" name="custom_fields[${id}][options]" placeholder="Opzione1,Opzione2">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Obbligatorio</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="custom_fields[${id}][required]">
                                </div>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-sm btn-danger remove-field">Rimuovi</button>
                            </div>
                        </div>
                    </div>
                `;

                addBtn.addEventListener('click', () => {
                    container.insertAdjacentHTML('beforeend', createFieldHTML(`new_${fieldCounter++}`));
                });

                container.addEventListener('click', (e) => {
                    if (e.target.classList.contains('remove-field')) {
                        e.target.closest('.custom-field-item').remove();
                    }
                });
            })();
        </script>
    </div>
</section>
