<?php
/**
 * Pagina Chi Siamo
 */
$foundationDate = new DateTime('2016-06-01');
$currentDate = new DateTime('now');
$yearsInBusiness = max(1, (int) $foundationDate->diff($currentDate)->y);
?>

<section id="chi-siamo" class="ap-section ap-section--about ap-section--grid-bg">
    <div class="container">
        <div class="ap-about__intro" data-reveal="fade-up">
            <span class="ap-eyebrow">Chi siamo</span>
            <div>
                <h1>Hub operativo nato nel 2016 per rendere quotidiani i servizi complessi</h1>
                <p>Abbiamo trasformato un’agenzia tradizionale in una control room ibrida dove persone, AI e processi certificati gestiscono identità digitali, spedizioni e pagamenti per tutta la penisola sorrentina.</p>
            </div>
            <div class="ap-about__chips">
                <span>Workflow proprietari</span>
                <span>Lab compliance interna</span>
                <span>Task force telco & logistica</span>
            </div>
        </div>

        <div class="ap-about__grid" data-reveal="fade-up">
            <article class="ap-about__card">
                <h2>Missione e metrica</h2>
                <p class="lead">Costruiamo percorsi di onboarding e fulfillment misurabili, mantenendo voce umana e controllo locale.</p>
                <ul class="ap-list ap-list--checks">
                    <li>Pod dedicati con escalation diretta CTO/COO</li>
                    <li>Layer digitale per firme, SPID, procure e compliance</li>
                    <li>Distribuzione ticket su 3 canali sincroni</li>
                    <li>Delivery logistica con monitoraggio live e assicurazione integrata</li>
                </ul>
            </article>

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
                <h3>Roadmap sintetica</h3>
                <ol>
                    <li>
                        <strong>2016 · Launch</strong>
                        <span>Apertura hub fisico e primi servizi retail</span>
                    </li>
                    <li>
                        <strong>2019 · Digital lab</strong>
                        <span>Proprietary workflow per identità digitale e compliance</span>
                    </li>
                    <li>
                        <strong>2021 · Telco & logistics</strong>
                        <span>Partner nazionali e tracking integrato</span>
                    </li>
                    <li>
                        <strong>Today · Control room</strong>
                        <span>Pod ibridi con KPI pubblici e dashboard condivisa</span>
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