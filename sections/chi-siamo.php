<?php
/**
 * Pagina Chi Siamo
 */
$foundationDate = new DateTime('2016-06-01');
$currentDate = new DateTime('now');
$yearsInBusiness = max(1, (int) $foundationDate->diff($currentDate)->y);
?>

<section id="chi-siamo" class="ap-section ap-section--about">
    <div class="container">
        <div class="ap-section__heading" data-reveal="fade-up">
            <span class="ap-eyebrow">Chi siamo</span>
            <h1>Processi omnicanale, persone reali, tecnologia proprietaria</h1>
            <p>Dal 2016 orchestriamo servizi digitali, spedizioni e pagamenti certificati per cittadini, professionisti e aziende della penisola sorrentina.</p>
        </div>

        <div class="ap-panels" data-reveal="fade-up">
            <article class="ap-panel ap-panel--highlight">
                <h2>La nostra missione</h2>
                <p class="lead">Garantiamo operatività continua con workflow validati, KPI pubblici e team certificati per identità digitali, spedizioni e telco.</p>
                <ul class="ap-list ap-list--checks">
                    <li>Front office multicanale h24 con presa in carico entro 24h</li>
                    <li>Onboarding SPID, CNS e firma digitale assistiti</li>
                    <li>Partnership nazionali per telco, pagamenti e logistica</li>
                    <li>Tracking spedizioni e assicurazione integrata</li>
                    <li>Supporto dedicato lun-sab con escalation dirette</li>
                </ul>
            </article>

            <aside class="ap-panel ap-panel--metrics">
                <div class="ap-metrics ap-metrics--stacked">
                    <div class="ap-metric">
                        <span class="ap-metric__value"><?php echo $yearsInBusiness; ?>+</span>
                        <span class="ap-metric__label">anni di operations</span>
                    </div>
                    <div class="ap-metric">
                        <span class="ap-metric__value">12k+</span>
                        <span class="ap-metric__label">clienti accompagnati</span>
                    </div>
                    <div class="ap-metric">
                        <span class="ap-metric__value">4.9/5</span>
                        <span class="ap-metric__label">soddisfazione media</span>
                    </div>
                    <div class="ap-metric">
                        <span class="ap-metric__value">10+</span>
                        <span class="ap-metric__label">partner certificati</span>
                    </div>
                </div>
                <div class="ap-map">
                    <iframe
                        src="https://www.openstreetmap.org/export/embed.html?bbox=14.4849641,40.6983611,14.4853168,40.7006381&layer=mapnik&marker=40.6994991,14.4851434"
                        width="100%"
                        height="280"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen>
                    </iframe>
                    <div class="ap-map__meta">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Via Plinio il Vecchio, 72 · Castellammare di Stabia</span>
                        <a href="https://maps.google.com/?q=Via+Plinio+Il+Vecchio+72,+80053+Castellammare+di+Stabia+NA" target="_blank" rel="noopener">Apri Maps</a>
                    </div>
                </div>
            </aside>
        </div>

        <div class="ap-panel ap-panel--cta" data-reveal="fade-up">
            <div>
                <h3>Hai bisogno di un laboratorio operativo?</h3>
                <p>Programmiamo onboarding, procurement documentale e attivazioni telco in pochi passaggi. Ti affianchiamo con uno specialist e KPI condivisi.</p>
            </div>
            <div class="ap-panel__actions">
                <a class="ap-btn ap-btn--primary" href="?page=contatti">
                    <i class="fas fa-envelope"></i>
                    <span>Parla con noi</span>
                </a>
                <a class="ap-btn ap-btn--ghost" href="?page=servizi">Sfoglia servizi</a>
            </div>
        </div>
    </div>
</section>