<?php
/**
 * Sezione Ricariche Multi-Canale
 */
?>
<style>
.clean-card {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    transition: all 0.2s ease;
    min-height: 350px;
    padding: 1.5rem;
}

.clean-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.service-icon-clean {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    font-size: 1.2rem;
    margin-right: 1rem;
    flex-shrink: 0;
}

.hero-section-clean {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 2.5rem;
}

.cta-clean {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border-radius: 12px;
    padding: 2rem;
    color: white;
}

.cta-clean .btn {
    background: white;
    color: #007bff;
    border: none;
    border-radius: 25px;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.2s ease;
}

.cta-clean .btn:hover {
    background: #f8f9fa;
    transform: translateY(-1px);
}
</style>

<section class="section-padding bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Hero Section Pulita -->
                <div class="hero-section-clean text-center mb-5">
                    <h1 class="display-4 fw-bold text-primary mb-3">
                        <i class="fas fa-mobile-alt me-3"></i>Ricariche Telefoniche
                    </h1>
                    <p class="lead text-muted mb-4 fs-5">
                        Presso la nostra agenzia puoi ricaricare il credito o attivare servizi per i principali operatori nazionali e internazionali.
                    </p>
                    <div class="row g-3 justify-content-center">
                        <div class="col-auto">
                            <span class="badge bg-primary px-3 py-2">
                                <i class="fas fa-clock me-1"></i>Rapido
                            </span>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-success px-3 py-2">
                                <i class="fas fa-euro-sign me-1"></i>Conveniente
                            </span>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-info px-3 py-2">
                                <i class="fas fa-shield-alt me-1"></i>Sicuro
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servizi di Ricarica -->
        <div class="row g-4 mb-5">
            <!-- Ricariche Telefoniche -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Ricariche Telefoniche</h5>
                        </div>
                        <p class="card-text text-muted">
                            Operatori nazionali e internazionali supportati:
                        </p>
                        <ul class="list-unstyled">
                            <li><span class="fw-medium">Bestcard</span></li>
                            <li><span class="fw-medium">CoopVoce</span></li>
                            <li><span class="fw-medium">Digi Mobil</span></li>
                            <li><span class="fw-medium">Fastweb</span></li>
                            <li><span class="fw-medium">Freecom</span></li>
                            <li><span class="fw-medium">Ho.Mobile</span></li>
                            <li><span class="fw-medium">Iliad</span></li>
                            <li><span class="fw-medium">Kena Mobile</span></li>
                            <li><span class="fw-medium">Lebara</span></li>
                            <li><span class="fw-medium">Linkem</span></li>
                            <li><span class="fw-medium">Lycamobile</span></li>
                            <li><span class="fw-medium">Nowtel</span></li>
                            <li><span class="fw-medium">Phoneall</span></li>
                            <li><span class="fw-medium">Più Ricarica</span></li>
                            <li><span class="fw-medium">PosteMobile</span></li>
                            <li><span class="fw-medium">Rabona Mobile</span></li>
                            <li><span class="fw-medium">TIM</span></li>
                            <li><span class="fw-medium">Tiscali</span></li>
                            <li><span class="fw-medium">Very Mobile</span></li>
                            <li><span class="fw-medium">Vodafone</span></li>
                            <li><span class="fw-medium">WindTre</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Pay TV e Intrattenimento -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-tv"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Pay TV e Intrattenimento</h5>
                        </div>
                        <p class="card-text text-muted">
                            Ricarichi rapide e immediate per le piattaforme di streaming più utilizzate:
                        </p>
                        <ul class="list-unstyled">
                            <li><span class="fw-medium">DAZN</span></li>
                            <li><span class="fw-medium">Disney+</span></li>
                            <li><span class="fw-medium">Eurosport</span></li>
                            <li><span class="fw-medium">Netflix</span></li>
                            <li><span class="fw-medium">Sky</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Carte Ricaricabili & Contenuti Digitali -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Carte Ricaricabili & Contenuti Digitali</h5>
                        </div>
                        <p class="card-text text-muted">
                            Per acquisti online, regali digitali e piattaforme internazionali:
                        </p>
                        <ul class="list-unstyled">
                            <li><span class="fw-medium">Airbnb</span></li>
                            <li><span class="fw-medium">Amazon</span></li>
                            <li><span class="fw-medium">Deliveroo</span></li>
                            <li><span class="fw-medium">FlixBus</span></li>
                            <li><span class="fw-medium">Fortnite</span></li>
                            <li><span class="fw-medium">H&M</span></li>
                            <li><span class="fw-medium">MuchBetter</span></li>
                            <li><span class="fw-medium">Neosurf</span></li>
                            <li><span class="fw-medium">Okto Cash</span></li>
                            <li><span class="fw-medium">Paysafecard</span></li>
                            <li><span class="fw-medium">Q8</span></li>
                            <li><span class="fw-medium">SixthContinent</span></li>
                            <li><span class="fw-medium">Uber</span></li>
                            <li><span class="fw-medium">Volagratis</span></li>
                            <li><span class="fw-medium">WGF Club</span></li>
                            <li><span class="fw-medium">Zalando</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Ricariche Conti Gioco -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-gamepad"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Ricariche Conti Gioco</h5>
                        </div>
                        <p class="card-text text-muted">
                            Per il tuo intrattenimento, puoi ricaricare i principali conti gioco:
                        </p>
                        <ul class="list-unstyled">
                            <li><span class="fw-medium">Betflag</span></li>
                            <li><span class="fw-medium">Betsson</span></li>
                            <li><span class="fw-medium">Betwin360</span></li>
                            <li><span class="fw-medium">Bwin</span></li>
                            <li><span class="fw-medium">Gioco Digitale</span></li>
                            <li><span class="fw-medium">GoldBet</span></li>
                            <li><span class="fw-medium">Lottomatica</span></li>
                            <li><span class="fw-medium">PartyPoker</span></li>
                            <li><span class="fw-medium">PokerStars</span></li>
                            <li><span class="fw-medium">Stanleybet</span></li>
                            <li><span class="fw-medium">William Hill</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Contenuti Digitali, Console e App -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-store"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Contenuti Digitali, Console e App</h5>
                        </div>
                        <p class="card-text text-muted">
                            Ricariche per store digitali, musica, console e applicazioni:
                        </p>
                        <ul class="list-unstyled">
                            <li><span class="fw-medium">Apple</span></li>
                            <li><span class="fw-medium">Electronic Arts</span></li>
                            <li><span class="fw-medium">Google Play</span></li>
                            <li><span class="fw-medium">Microsoft</span></li>
                            <li><span class="fw-medium">Nintendo eShop</span></li>
                            <li><span class="fw-medium">Roblox</span></li>
                            <li><span class="fw-medium">Sony</span></li>
                            <li><span class="fw-medium">Spotify Premium</span></li>
                            <li><span class="fw-medium">Steam</span></li>
                            <li><span class="fw-medium">Travel Mate</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action Pulita -->
        <div class="row">
            <div class="col-12">
                <div class="cta-clean text-center">
                    <h3 class="mb-3 fw-bold">
                        <i class="fas fa-handshake me-2"></i>Hai Bisogno di una Ricarica?
                    </h3>
                    <p class="mb-4 fs-5">Siamo qui per aiutarti con qualsiasi tipo di ricarica. Contattaci per assistenza personalizzata.</p>
                    <a href="?page=contatti" class="btn">
                        <i class="fas fa-envelope me-2"></i>Richiedi Assistenza
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>