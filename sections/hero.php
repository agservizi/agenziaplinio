<?php
require_once __DIR__ . '/../components/cta.php';
?>
<section id="hero" class="hero-section d-flex align-items-center" data-parallax>
    <div class="hero-overlay"></div>
    <div class="container position-relative text-center text-md-start">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center" data-reveal="fade-up" data-animate="hero-text">
                <p class="eyebrow mb-3">Agenzia multiservizi a 360&deg;</p>
                <h1 class="display-4 fw-bold mb-4">Servizi smart, esperienze premium.</h1>
                <p class="lead mb-5">Pagamenti certificati, servizi digitali, telefonia e spedizioni curati dal nostro team per privati e aziende.</p>
                <?php
                    echo renderCTAGroup(
                        [
                            'label' => 'Esplora servizi',
                            'href' => '#servizi',
                            'variant' => 'primary'
                        ],
                        [
                            'label' => 'Parla con noi',
                            'href' => '#contatti',
                            'variant' => 'ghost'
                        ]
                    );
                ?>
            </div>
            <div class="col-lg-5 mt-5 mt-lg-0" data-reveal="fade-left" data-animate="hero-panel">
                <div class="hero-card bg-white rounded-3 shadow-lg p-4">
                    <div class="hero-card__icon mb-3">
                        <i class="fas fa-star fa-2x text-primary"></i>
                    </div>
                    <h3 class="h4 fw-bold mb-3">Perché Scegliere Agenzia Plinio?</h3>
                    <ul class="list-unstyled mb-4">
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Servizi certificati e affidabili</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Assistenza personalizzata</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Commissioni competitive</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Supporto 24/7</span>
                        </li>
                    </ul>
                    <a href="#servizi" class="btn btn-primary w-100" data-scroll>
                        <i class="fas fa-arrow-right me-2"></i>Scopri i nostri servizi
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
