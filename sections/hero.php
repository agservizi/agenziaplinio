<?php
require_once __DIR__ . '/../components/cta.php';
?>
<section id="hero" class="hero-section d-flex align-items-center" data-parallax>
    <div class="hero-overlay"></div>
    <div class="container position-relative text-center text-md-start">
        <div class="row align-items-center">
            <div class="col-lg-7" data-reveal="fade-up" data-animate="hero-text">
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
                <div class="hero-card glass-panel">
                    <div class="hero-card__header d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-1">Servizi rapidi e certificati</h5>
                            <p class="text-muted small mb-0">Operativi 7/7 con presa in carico immediata</p>
                        </div>
                        <span class="badge rounded-pill bg-primary text-white fw-semibold">Risposta entro 24h</span>
                    </div>
                    <ul class="hero-card__list list-unstyled mb-4">
                        <li>
                            <span class="hero-card__icon" aria-hidden="true">&check;</span>
                            <div>
                                Pagamenti certificati con ricevuta digitale immediata
                            </div>
                        </li>
                        <li>
                            <span class="hero-card__icon" aria-hidden="true">&check;</span>
                            <div>
                                Attivazioni SPID, PEC e firma con verifica guidata
                            </div>
                        </li>
                        <li>
                            <span class="hero-card__icon" aria-hidden="true">&check;</span>
                            <div>
                                Soluzioni voce e dati con provisioning continuo
                            </div>
                        </li>
                        <li>
                            <span class="hero-card__icon" aria-hidden="true">&check;</span>
                            <div>
                                Logistica e spedizioni tracciate multi-corriere
                            </div>
                        </li>
                    </ul>
                    <div class="hero-card__footer d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-uppercase text-muted">Customer rating</small>
                            <p class="fs-5 fw-bold text-dark mb-0">4.9/5</p>
                        </div>
                        <div class="text-end">
                            <small class="text-uppercase text-muted">Ticket gestiti</small>
                            <p class="fs-5 fw-bold text-dark mb-0">2.300+/anno</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
