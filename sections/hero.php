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
                <!-- Card rimossa come richiesto -->
            </div>
        </div>
    </div>
</section>
