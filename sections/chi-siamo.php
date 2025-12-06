<?php
/**
 * Pagina Chi Siamo
 */
$foundationDate = new DateTime('2016-06-01');
$currentDate = new DateTime('now');
$yearsInBusiness = max(1, (int) $foundationDate->diff($currentDate)->y);
?>

<section id="chi-siamo" class="ap-section ap-section--about ap-section--widescreen" data-theme="night">
    <div class="ap-about__hero" data-parallax-container>
        <div class="ap-about__hero-media" aria-hidden="true">
            <div class="ap-about__hero-overlay"></div>
        </div>
        <div class="ap-about__hero-content container" data-reveal="fade-up">
            <span class="ap-eyebrow">Chi siamo</span>
            <h1 data-reveal-text>Hub operativo nato per rendere quotidiani i servizi complessi</h1>
            <p>Dal 2016 curiamo identità digitali, pagamenti e logistica con un control center ibrido che sincronizza persone, AI e infrastrutture certificate.</p>
            <div class="ap-about__chips">
                <span>Workflow proprietari</span>
                <span>Lab compliance interna</span>
                <span>Task force telco & logistica</span>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="ap-about__modules" data-reveal="fade-up">
            <article class="ap-about__module">
                <h3>Mission</h3>
                <p>Costruiamo percorsi di onboarding e fulfillment misurabili mantenendo voce umana e controllo locale.</p>
                <ul class="ap-list ap-list--checks">
                    <li>Pod dedicati con escalation diretta CTO/COO</li>
                    <li>Layer digitale per firme, SPID, procure e compliance</li>
                    <li>Distribuzione ticket su 3 canali sincroni</li>
                </ul>
            </article>
            <article class="ap-about__module">
                <h3>Storia</h3>
                <p>Abbiamo trasformato una filiale fisica in una control room che integra dashboard, ritual e report vocali condivisi.</p>
                <ul class="ap-list ap-list--dots">
                    <li>2016 · apertura hub e prime attivazioni retail</li>
                    <li>2019 · lancio digital lab per identità e compliance</li>
                    <li>2021 · estensione telco/logistica con partner nazionali</li>
                </ul>
            </article>
            <article class="ap-about__module">
                <h3>Valori</h3>
                <p>Trasparenza dei KPI, ownership diffusa e ascolto diretto dei clienti.</p>
                <div class="ap-about__values">
                    <span>Accountability</span>
                    <span>Velocità</span>
                    <span>Human touch</span>
                </div>
            </article>
        </div>

        <div class="ap-about__grid" data-reveal="fade-up">
            <article class="ap-about__card ap-about__card--metrics">
                <div>
                    <small>Years active</small>
                    <strong><?php echo $yearsInBusiness; ?>+</strong>
                </div>
                <div>
                    <small>Clienti onboarded</small>
                    <strong>12k+</strong>
                </div>
                <div>
                    <small>Soddisfazione media</small>
                    <strong>4.9/5</strong>
                </div>
                <div>
                    <small>Partner certificati</small>
                    <strong>10+</strong>
                </div>
            </article>

            <article class="ap-about__card ap-about__card--timeline">
                <h3>Timeline</h3>
                <ol>
                    <li>
                        <strong>2016</strong>
                        <span>Launch hub operativo e servizi retail</span>
                    </li>
                    <li>
                        <strong>2019</strong>
                        <span>Digital lab per identità & compliance</span>
                    </li>
                    <li>
                        <strong>2021</strong>
                        <span>Telco & logistics con tracking integrato</span>
                    </li>
                    <li>
                        <strong>Today</strong>
                        <span>Control room con KPI pubblici e dashboard condivisa</span>
                    </li>
                </ol>
            </article>

            <article class="ap-about__card ap-about__card--map">
                <div class="ap-map">
                    <iframe
                        src="https://www.openstreetmap.org/export/embed.html?bbox=14.4849641,40.6983611,14.4853168,40.7006381&layer=mapnik&marker=40.6994991,14.4851434"
                        width="100%"
                        height="260"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen>
                    </iframe>
                    <div class="ap-map__meta">
                        <div>
                            <strong>Control room</strong>
                            <span>Via Plinio il Vecchio, 72 · Castellammare di Stabia</span>
                        </div>
                        <a href="https://maps.google.com/?q=Via+Plinio+Il+Vecchio+72,+80053+Castellammare+di+Stabia+NA" target="_blank" rel="noopener">Apri Maps</a>
                    </div>
                </div>
            </article>
        </div>

        <div class="ap-about__cta" data-reveal="fade-up">
            <div>
                <h3>Vuoi aprire un canale operativo condiviso?</h3>
                <p>Allineiamo team e stakeholder con war room digitali, ritual settimanali e insight vocali.</p>
            </div>
            <div class="ap-about__cta-actions">
                <a class="ap-btn ap-btn--primary" href="?page=contatti">Attiva una cabina di regia</a>
                <a class="ap-btn ap-btn--ghost" href="?page=servizi">Esplora capability</a>
            </div>
        </div>
    </div>
</section>