<?php
$foundationDate = new DateTime('2016-06-01');
$currentDate = new DateTime('now');
$yearsInBusiness = max(1, (int) $foundationDate->diff($currentDate)->y);
?>
<section id="chi-siamo" class="about-section section-padding">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6" data-reveal>
                <div class="about-panel glass-panel">
                    <span class="about-badge">Servizi certificati end-to-end</span>
                    <h3>Mettiamo persone e dati al centro.</h3>
                    <p>Dal 2016 supportiamo cittadini, professionisti e PMI con una suite di servizi in continua evoluzione. Processi verificati, tecnologia proprietaria e relazioni umane reali.</p>
                    <ul class="list-unstyled about-checklist mb-4">
                        <li>
                            <span class="about-checklist__icon" aria-hidden="true">&check;</span>
                            Front office multicanale con tempi di risposta garantiti
                        </li>
                        <li>
                            <span class="about-checklist__icon" aria-hidden="true">&check;</span>
                            Team certificato per identit&agrave; digitali e onboarding assistito
                        </li>
                        <li>
                            <span class="about-checklist__icon" aria-hidden="true">&check;</span>
                            Partnership ufficiali con i principali brand telco nazionali
                        </li>
                    </ul>
                    <div class="about-meta">
                        <div class="about-meta__item">
                            <small>Avvio attivit&agrave;</small>
                            <p>2016</p>
                        </div>
                        <div class="about-meta__item">
                            <small>Case gestite/anno</small>
                            <p>4.500+</p>
                        </div>
                        <div class="about-meta__item">
                            <small>Customer score</small>
                            <p>4.9/5</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-reveal="fade-left">
                <div class="stats-grid">
                    <div class="stat-card">
                        <span class="stat-number"><?php echo htmlspecialchars((string) $yearsInBusiness, ENT_QUOTES); ?>+</span>
                        <p>Anni di attivit&agrave;</p>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">12k</span>
                        <p>Clienti soddisfatti</p>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">10</span>
                        <p>Partner certificati</p>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">4</span>
                        <p>Linee di servizio</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
