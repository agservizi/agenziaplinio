<?php
$servicesMenu = [];
$servicesDataPath = __DIR__ . '/../assets/data/services.json';
if (is_file($servicesDataPath)) {
    $servicesData = json_decode((string)file_get_contents($servicesDataPath), true);
    if (is_array($servicesData)) {
        foreach ($servicesData as $key => $service) {
            $servicesMenu[] = [
                'key' => $key,
                'title' => $service['title'] ?? 'Servizio',
                'body' => $service['body'] ?? ''
            ];
        }
    }
}
?>
<header id="site-header" class="topbar py-3" data-scroll-state="top">
    <div class="topbar-progress" aria-hidden="true"></div>
    <div class="container d-flex align-items-center justify-content-between">
        <a class="brand d-flex align-items-center" href="?page=home" data-scroll>
            <img src="assets/img/logo.png" class="brand-logo" alt="AG Servizi Via Plinio Il Vecchio 72">
        </a>
        <nav class="primary-nav" aria-label="Navigazione principale">
            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav-menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
            <ul id="nav-menu" class="nav-list list-unstyled mb-0">
                <li><a href="?page=home" data-scroll>Home</a></li>
                <li class="nav-item--mega">
                    <button class="nav-link nav-link--toggle" type="button" data-mega-toggle aria-expanded="false" aria-haspopup="true">
                        Servizi
                        <span class="chevron" aria-hidden="true"></span>
                    </button>
                    <?php if (!empty($servicesMenu)): ?>
                        <div class="mega-panel" data-mega-panel>
                            <div class="mega-panel__header">
                                <p class="eyebrow mb-1">Catalogo servizi</p>
                                <h5 class="mb-0">Seleziona un servizio per approfondire</h5>
                            </div>
                            <div class="mega-grid">
                                <?php foreach ($servicesMenu as $service): ?>
                                    <a class="mega-card" href="<?php echo ($service['key'] === 'payments' ? '?page=pagamenti-certificati' : ($service['key'] === 'topups' ? '?page=ricariche-multicanale' : ($service['key'] === 'digital' ? '?page=identita-digitale' : ($service['key'] === 'telco' ? '?page=soluzioni-voce-dati' : ($service['key'] === 'shipping' ? '?page=logistica-smart' : '?page=home#servizi'))))); ?>" data-scroll>
                                        <h6><?php echo htmlspecialchars($service['title'], ENT_QUOTES); ?></h6>
                                        <p><?php echo htmlspecialchars($service['body'], ENT_QUOTES); ?></p>
                                        <span class="mega-card__cta">Vai alla sezione</span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="nav-mobile-services">
                            <p class="nav-mobile-services__eyebrow">Catalogo servizi</p>
                            <div class="nav-mobile-services__grid">
                                <?php foreach ($servicesMenu as $service): ?>
                                    <a class="nav-mobile-services__item" href="<?php echo ($service['key'] === 'payments' ? '?page=pagamenti-certificati' : ($service['key'] === 'topups' ? '?page=ricariche-multicanale' : ($service['key'] === 'digital' ? '?page=identita-digitale' : ($service['key'] === 'telco' ? '?page=soluzioni-voce-dati' : ($service['key'] === 'shipping' ? '?page=logistica-smart' : '?page=home#servizi'))))); ?>" data-scroll>
                                        <strong><?php echo htmlspecialchars($service['title'], ENT_QUOTES); ?></strong>
                                        <span><?php echo htmlspecialchars($service['body'], ENT_QUOTES); ?></span>
                                        <span class="nav-mobile-services__cta">Vai alla sezione</span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </li>
                <li><a href="?page=shop">Shop</a></li>
                <li><a href="?page=home#vetrina" data-scroll>Vetrina</a></li>
                <li><a href="?page=chi-siamo">Chi siamo</a></li>
                <li><a href="?page=contatti">Contatti</a></li>
                <li class="nav-search-trigger">
                    <button
                        class="nav-search-btn"
                        type="button"
                        aria-label="Cerca nello shop"
                        aria-expanded="false"
                        aria-controls="topbar-search-panel"
                        data-topbar-search-toggle
                    >
                        <span class="visually-hidden">Apri ricerca prodotti</span>
                        <img src="assets/img/icon-search.png" alt="" aria-hidden="true" width="20" height="20">
                    </button>
                </li>
            </ul>
        </nav>
        <div class="topbar-search" id="topbar-search-panel" data-topbar-search>
            <form class="topbar-search__form" method="get" data-topbar-search-form>
                <input type="hidden" name="page" value="shop">
                <label class="visually-hidden" for="topbar-search-input">Cerca prodotti nello shop</label>
                <input
                    type="search"
                    class="topbar-search__input"
                    id="topbar-search-input"
                    name="q"
                    placeholder="Cerca prodotti dello shop"
                    autocomplete="off"
                    required
                    data-topbar-search-input
                >
                <button class="topbar-search__submit" type="submit">Cerca</button>
                <button class="topbar-search__close" type="button" aria-label="Chiudi ricerca" data-topbar-search-close>&times;</button>
            </form>
        </div>
        <div class="topbar-actions d-flex align-items-center gap-3">
            <button
                class="cart-pill"
                type="button"
                data-cart-sidebar-open
                aria-controls="cart-sidebar"
                aria-expanded="false"
            >
                <span>Carrello</span>
                <span class="cart-pill__count"><?php echo (int) ($cartCount ?? 0); ?></span>
            </button>
            <?php if (!empty($currentUser)): ?>
                <a class="account-link" href="?page=<?php echo ap_auth_is_admin() ? 'admin' : 'account'; ?>">
                    <?php echo htmlspecialchars($currentUser['name'], ENT_QUOTES); ?>
                </a>
            <?php else: ?>
                <a class="account-link" href="?page=account">Accedi</a>
            <?php endif; ?>
        </div>
    </div>
</header>
