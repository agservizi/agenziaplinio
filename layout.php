<?php
$siteName = $site['name'] ?? 'Agenzia Plinio';
$pageTitle = $meta['title'] ?? $site['default_title'];
$pageDescription = $meta['description'] ?? $site['default_description'];
$ogImage = $meta['og_image'] ?? 'assets/img/og-image.jpg';
$chatbotFaqsRaw = function_exists('ap_active_faqs') ? ap_active_faqs() : [];
$chatbotSlugify = function_exists('ap_slugify')
    ? 'ap_slugify'
    : static function (string $value): string {
        if (function_exists('iconv')) {
            $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
            if ($transliterated !== false) {
                $value = $transliterated;
            }
        }
        $value = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $value));
        return trim($value, '-');
    };
$chatbotFaqs = array_map(static function (array $faq) use ($chatbotSlugify): array {
    $keywords = array_values(array_filter(array_map('trim', preg_split('/[,;]+/', $faq['keywords'] ?? '') ?: [])));
    $category = $faq['category'] !== null && $faq['category'] !== '' ? (string) $faq['category'] : 'Generale';
    return [
        'id' => (int) ($faq['id'] ?? 0),
        'question' => (string) ($faq['question'] ?? ''),
        'answer' => (string) ($faq['answer'] ?? ''),
        'category' => $category,
        'category_slug' => $category !== '' ? $chatbotSlugify($category) : 'generale',
        'keywords' => $keywords,
        'sort_order' => (int) ($faq['sort_order'] ?? 0),
    ];
}, $chatbotFaqsRaw);
$bodyClasses = ['theme-shell'];
if (!empty($newsTickerItems)) {
    $bodyClasses[] = 'has-news-ticker';
}
if (!empty($chatbotFaqs)) {
    $bodyClasses[] = 'has-chatbot';
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES); ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES); ?>">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($siteName, ENT_QUOTES); ?>">
    <script type="application/ld+json">
        <?php echo json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/maplibre-gl@2.4.0/dist/maplibre-gl.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?php echo implode(' ', $bodyClasses); ?>">
    <?php include __DIR__ . '/components/preloader.php'; ?>
    <?php if (!empty($newsTickerItems)): ?>
        <?php include __DIR__ . '/components/news-ticker.php'; ?>
    <?php endif; ?>
    <?php include __DIR__ . '/includes/topbar.php'; ?>
    <main id="page-content" data-page="<?php echo htmlspecialchars($pageKey, ENT_QUOTES); ?>">
        <?php echo $pageContent; ?>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <?php include __DIR__ . '/components/toast-stack.php'; ?>
    <button id="back-to-top" aria-label="Torna all'inizio">↑</button>
    <div id="service-modal" class="ap-modal" aria-hidden="true">
        <div class="ap-modal__overlay" data-modal-close></div>
        <div class="ap-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="service-modal-title">
            <button type="button" class="ap-modal__close" data-modal-close>&times;</button>
            <div class="ap-modal__content">
                <h4 id="service-modal-title"></h4>
                <p id="service-modal-body"></p>
            </div>
        </div>
    </div>
    <?php include __DIR__ . '/components/cart-sidebar.php'; ?>
    <?php include __DIR__ . '/components/chatbot.php'; ?>
    <?php if (!empty($flashMessages)): ?>
        <div class="toast-stack position-fixed bottom-0 start-0 p-3" style="z-index: 1100;">
            <?php foreach ($flashMessages as $toast): ?>
                <div class="toast align-items-center text-white bg-<?php echo htmlspecialchars($toast['type'] === 'success' ? 'success' : ($toast['type'] === 'error' ? 'danger' : 'info'), ENT_QUOTES); ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <?php echo htmlspecialchars($toast['message'] ?? '', ENT_QUOTES); ?>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Chiudi"></button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <script>
        window.__AP_PAGE_META__ = <?php echo json_encode([
            'page' => $pageKey,
            'formFeedback' => $formFeedback,
            'toasts' => $flashMessages,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
        window.__AP_CHATBOT__ = <?php echo json_encode([
            'faqs' => $chatbotFaqs,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-show toasts
            const toasts = document.querySelectorAll('.toast');
            toasts.forEach(function(toast) {
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
            });
        });
    </script>
    <script src="https://unpkg.com/maplibre-gl@2.4.0/dist/maplibre-gl.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <?php if (function_exists('ap_klarna_messaging_enabled') && ap_klarna_messaging_enabled()): ?>
        <script
            async
            data-environment="<?php echo htmlspecialchars(ap_env('KLARNA_ENV', 'playground'), ENT_QUOTES); ?>"
            src="https://js.klarna.com/web-sdk/v1/klarna.js"
            data-client-id="<?php echo htmlspecialchars(ap_klarna_onsite_client_id(), ENT_QUOTES); ?>"
        ></script>
    <?php endif; ?>
    <script src="assets/js/main.js" type="module"></script>
</body>
</html>
