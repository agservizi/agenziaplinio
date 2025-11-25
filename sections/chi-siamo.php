<?php
/**
 * Pagina Chi Siamo
 */
$foundationDate = new DateTime('2016-06-01');
$currentDate = new DateTime('now');
$yearsInBusiness = max(1, (int) $foundationDate->diff($currentDate)->y);
?>
<style>
.about-hero {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 3rem;
    text-align: center;
    margin-bottom: 3rem;
}

.about-hero h1 {
    color: #007bff;
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.about-hero p {
    font-size: 1.2rem;
    color: #6c757d;
    max-width: 600px;
    margin: 0 auto;
}

.about-panel-full {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    padding: 2.5rem;
    margin-bottom: 3rem;
}

.about-checklist {
    list-style: none;
    padding: 0;
}

.about-checklist li {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding-left: 0;
}

.about-checklist__icon {
    color: #28a745;
    font-weight: bold;
    margin-right: 1rem;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.stats-grid-full {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.stat-card-full {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    transition: transform 0.2s ease;
}

.stat-card-full:hover {
    transform: translateY(-5px);
}

.stat-number-full {
    font-size: 3rem;
    font-weight: 700;
    color: #007bff;
    display: block;
    margin-bottom: 0.5rem;
}

.team-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 3rem;
    margin-bottom: 3rem;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.team-member {
    background: #ffffff;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.team-member img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    margin-bottom: 1rem;
    object-fit: cover;
}

.team-skills {
    margin-top: 1rem;
}

.skill-badge {
    display: inline-block;
    background: #e9ecef;
    color: #495057;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    margin-right: 0.25rem;
    margin-bottom: 0.25rem;
}

.cta-about {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border-radius: 12px;
    padding: 3rem;
    color: white;
    text-align: center;
}

.cta-about h3 {
    margin-bottom: 1rem;
    font-weight: 700;
}

.cta-about p {
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

.cta-about .btn {
    background: white;
    color: #007bff;
    border: none;
    border-radius: 25px;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.2s ease;
}

.cta-about .btn:hover {
    background: #f8f9fa;
    transform: translateY(-1px);
}

/* Timeline Styles */
.timeline-container {
    position: relative;
    max-width: 800px;
    margin: 0 auto;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #007bff, #28a745);
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 8px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #007bff;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #007bff;
}

.timeline-content {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.timeline-content h4 {
    color: #007bff;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

/* Values Section */
.values-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 3rem;
    margin-bottom: 3rem;
}

.value-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    transition: transform 0.2s ease;
    height: 100%;
}

.value-card:hover {
    transform: translateY(-5px);
}

.value-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 2rem;
}

.value-card h4 {
    color: #007bff;
    margin-bottom: 1rem;
}

/* Certifications */
.certifications-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

.certification-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.certification-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #28a745, #20c997);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 1rem;
    flex-shrink: 0;
}

.certification-content h5 {
    margin-bottom: 0.25rem;
    color: #007bff;
}

/* Partners */
.partners-showcase {
    background: #ffffff;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.partner-logos {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

.partner-logo {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.partner-logo span {
    margin-left: 1rem;
    font-weight: 500;
    color: #495057;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .timeline {
        padding-left: 20px;
    }

    .timeline-marker {
        left: -17px;
    }

    .values-section {
        padding: 2rem 1rem;
    }
}

/* Detailed Testimonials */
.testimonials-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 3rem;
    margin-bottom: 3rem;
}

.testimonial-detailed {
    background: #ffffff;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    height: 100%;
    border-left: 4px solid #007bff;
}

.testimonial-header {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

.testimonial-avatar {
    margin-right: 1rem;
    flex-shrink: 0;
}

.testimonial-info h5 {
    margin-bottom: 0.25rem;
    color: #007bff;
}

.testimonial-rating {
    margin-top: 0.5rem;
}

.testimonial-quote {
    font-style: italic;
    font-size: 1.1rem;
    line-height: 1.6;
    color: #495057;
    margin-bottom: 1.5rem;
    position: relative;
    padding-left: 1rem;
}

.testimonial-quote::before {
    content: '"';
    font-size: 4rem;
    color: #e9ecef;
    position: absolute;
    left: -10px;
    top: -20px;
    font-family: Georgia, serif;
}

.testimonial-meta {
    padding-top: 1rem;
    border-top: 1px solid #e9ecef;
}

/* Advanced Metrics */
.advanced-metrics {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 3rem;
    margin-bottom: 3rem;
    color: white;
}

.metrics-dashboard {
    max-width: 1000px;
    margin: 0 auto;
}

.metric-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.metric-item {
    text-align: center;
}

.metric-chart {
    margin-bottom: 1rem;
}

.chart-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    border: 4px solid rgba(255,255,255,0.3);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    position: relative;
}

.chart-circle::before {
    content: '';
    position: absolute;
    top: -4px;
    left: -4px;
    right: -4px;
    bottom: -4px;
    border-radius: 50%;
    background: conic-gradient(#ffffff 0% 99.5%, transparent 99.5% 100%);
    mask: radial-gradient(farthest-side, transparent calc(100% - 4px), black calc(100% - 4px));
    -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 4px), black calc(100% - 4px));
}

.chart-number {
    font-size: 1.5rem;
    font-weight: bold;
    z-index: 1;
    position: relative;
}

.chart-label {
    font-size: 0.8rem;
    opacity: 0.9;
    margin-top: 0.25rem;
}

.metric-description {
    font-size: 0.9rem;
    opacity: 0.9;
    line-height: 1.4;
}

.metric-highlights {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
}

.highlight-item {
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    backdrop-filter: blur(10px);
}

.highlight-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.highlight-label {
    font-size: 0.9rem;
    opacity: 0.9;
    margin-bottom: 0.5rem;
}

.highlight-trend {
    font-size: 0.8rem;
    color: #28a745;
    font-weight: 500;
}
</style>

<section class="section-padding bg-light">
    <div class="container">
        <!-- Hero Section -->
        <div class="about-hero">
            <h1><i class="fas fa-users me-3"></i>Chi Siamo</h1>
            <p>Dal 2016 al servizio della comunità locale con passione, competenza e innovazione. Scopri la nostra storia e i valori che ci guidano.</p>
        </div>

        <!-- About Panel -->
        <div class="about-panel-full">
            <div class="row">
                <div class="col-lg-8">
                    <h2 class="mb-4">La Nostra Missione</h2>
                    <p class="lead mb-4">Mettiamo persone e dati al centro di tutto quello che facciamo. Dal 2016 supportiamo cittadini, professionisti e PMI con una suite di servizi in continua evoluzione, processi verificati e tecnologia proprietaria.</p>

                    <h3 class="mb-3">Cosa Ci Rende Speciali</h3>
                    <ul class="about-checklist">
                        <li>
                            <span class="about-checklist__icon">&check;</span>
                            <div>Front office multicanale con tempi di risposta garantiti entro 24 ore</div>
                        </li>
                        <li>
                            <span class="about-checklist__icon">&check;</span>
                            <div>Team certificato per identità digitali e onboarding assistito</div>
                        </li>
                        <li>
                            <span class="about-checklist__icon">&check;</span>
                            <div>Partnership ufficiali con i principali brand telco nazionali</div>
                        </li>
                        <li>
                            <span class="about-checklist__icon">&check;</span>
                            <div>Servizi di spedizione con tracking completo e assicurazione integrata</div>
                        </li>
                        <li>
                            <span class="about-checklist__icon">&check;</span>
                            <div>Supporto clienti disponibile dal lunedì al sabato</div>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <div class="about-meta text-center">
                        <div class="about-meta__item mb-4">
                            <small class="text-muted d-block">Anno di Fondazione</small>
                            <p class="h2 text-primary mb-0">2016</p>
                        </div>
                        <div class="about-meta__item mb-4">
                            <small class="text-muted d-block">Clienti Soddisfatti</small>
                            <p class="h2 text-success mb-0">12k+</p>
                        </div>
                        <div class="about-meta__item mb-4">
                            <small class="text-muted d-block">Valutazione Clienti</small>
                            <p class="h2 text-warning mb-0">4.9/5</p>
                        </div>
                        <div class="about-meta__item">
                            <small class="text-muted d-block">Partner Certificati</small>
                            <p class="h2 text-info mb-0">10+</p>
                        </div>
                    </div>

                    <!-- Mappa OpenStreetMap -->
                    <div class="map-container mt-4">
                        <iframe
                            src="https://www.openstreetmap.org/export/embed.html?bbox=14.4849641,40.6983611,14.4853168,40.7006381&layer=mapnik&marker=40.6994991,14.4851434"
                            width="100%"
                            height="350"
                            style="border-radius: 12px; border: 1px solid #e9ecef;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                        <p class="text-center mt-2 small text-muted">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            Via Plinio il Vecchio, 72 - Castellammare di Stabia (NA)
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- La Nostra Storia -->
        <div class="about-panel-full">
            <h2 class="text-center mb-5">La Nostra Storia</h2>
            <div class="timeline-container">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <h4>2016 - La Fondazione</h4>
                            <p>Nasce AG SERVIZI come punto di riferimento per i servizi digitali nella zona di Castellammare di Stabia. Iniziamo con l'obiettivo di semplificare la vita quotidiana dei cittadini attraverso soluzioni tecnologiche accessibili.</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <h4>2017 - Prima Espansione</h4>
                            <p>Otteniamo le prime partnership ufficiali con i principali operatori telefonici. Introduciamo il servizio di assistenza personalizzata per privati e piccole imprese.</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <h4>2019 - Certificazione ISO 27001</h4>
                            <p>Raggiungiamo lo standard internazionale per la sicurezza delle informazioni, garantendo la massima protezione dei dati dei nostri clienti.</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <h4>2020 - Digital Transformation</h4>
                            <p>Durante la pandemia, diventiamo pionieri nell'assistenza remota. Introduciamo servizi digitali avanzati e formazione online per cittadini e imprese.</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <h4>2022 - Nuovo Showroom</h4>
                            <p>Apriamo la nuova sede in Via Plinio il Vecchio con spazi dedicati alla consulenza personalizzata e formazione digitale.</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <h4>2024 - Leadership Locale</h4>
                            <p>Celebrimo 8 anni di servizio con oltre 12.000 clienti soddisfatti. Rafforziamo il nostro ruolo di punto di riferimento per l'innovazione digitale sul territorio.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- I Nostri Valori -->
        <div class="values-section">
            <h2 class="text-center mb-5">I Nostri Valori</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4>Sicurezza</h4>
                        <p>La privacy e la sicurezza dei dati sono al centro di tutto quello che facciamo. Ogni processo è certificato e monitorato.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>Prossimità</h4>
                        <p>Siamo parte della comunità locale. Conosciamo le esigenze specifiche del territorio e ci adattiamo alle necessità reali.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h4>Innovazione</h4>
                        <p>Investiamo continuamente in nuove tecnologie per offrire servizi all'avanguardia, sempre un passo avanti alle esigenze.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h4>Fiducia</h4>
                        <p>Costruiamo relazioni durature basate sulla trasparenza, l'onestà e il rispetto per i nostri clienti e partner.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Certificazioni e Partnership -->
        <div class="about-panel-full">
            <div class="row">
                <div class="col-lg-8">
                    <h2 class="mb-4">Certificazioni e Partnership</h2>
                    <p class="lead mb-4">La nostra competenza è riconosciuta da istituzioni e partner leader del settore.</p>

                    <div class="certifications-grid">
                        <div class="certification-item">
                            <div class="certification-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="certification-content">
                                <h5>RAO - Registration Authority Officer</h5>
                                <p>Soggetto autorizzato alla verifica dell'identità personale per SPID, firme digitali remote e tutti i servizi digitali nazionali.</p>
                                <small class="text-muted">Certificazione ufficiale per identità digitali</small>
                            </div>
                        </div>
                        <div class="certification-item">
                            <div class="certification-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div class="certification-content">
                                <h5>Iliad</h5>
                                <p>Rivenditore autorizzato per servizi di telefonia mobile, internet e offerte convergenti senza costi nascosti.</p>
                                <small class="text-muted">Operatore telefonico italiano leader in trasparenza</small>
                            </div>
                        </div>
                        <div class="certification-item">
                            <div class="certification-icon">
                                <i class="fas fa-sim-card"></i>
                            </div>
                            <div class="certification-content">
                                <h5>Windtre</h5>
                                <p>Rivenditore autorizzato per servizi di telefonia mobile, internet fisso e mobile, con copertura nazionale estesa.</p>
                                <small class="text-muted">Operatore integrato per soluzioni complete</small>
                            </div>
                        </div>
                        <div class="certification-item">
                            <div class="certification-icon">
                                <i class="fas fa-wifi"></i>
                            </div>
                            <div class="certification-content">
                                <h5>Fastweb</h5>
                                <p>Rivenditore autorizzato per servizi di connessione internet ad alta velocità, telefonia fissa e soluzioni smart home.</p>
                                <small class="text-muted">Provider di telecomunicazioni per famiglie e imprese</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="partners-showcase">
                        <h5 class="mb-3">I Nostri Partner</h5>
                        <div class="partner-logos">
                            <button class="partner-logo btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modal-droppoint">
                                <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                                <span>DropPoint</span>
                            </button>
                            <button class="partner-logo btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modal-namirial">
                                <i class="fas fa-signature fa-2x text-info"></i>
                                <span>Namirial</span>
                            </button>
                            <button class="partner-logo btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modal-aruba-business">
                                <i class="fas fa-server fa-2x text-success"></i>
                                <span>Aruba Business</span>
                            </button>
                            <button class="partner-logo btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modal-iliad">
                                <i class="fas fa-mobile-alt fa-2x text-warning"></i>
                                <span>Iliad</span>
                            </button>
                            <button class="partner-logo btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modal-windtre">
                                <i class="fas fa-sim-card fa-2x text-danger"></i>
                                <span>Windtre</span>
                            </button>
                            <button class="partner-logo btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modal-fastweb">
                                <i class="fas fa-wifi fa-2x text-info"></i>
                                <span>Fastweb</span>
                            </button>
                            <button class="partner-logo btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modal-a2a-energia">
                                <i class="fas fa-bolt fa-2x text-warning"></i>
                                <span>A2A Energia</span>
                            </button>
                            <button class="partner-logo btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modal-enel-energia">
                                <i class="fas fa-lightbulb fa-2x text-success"></i>
                                <span>Enel Energia</span>
                            </button>
                            <button class="partner-logo btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modal-brt">
                                <i class="fas fa-truck fa-2x text-primary"></i>
                                <span>Brt</span>
                            </button>
                            <button class="partner-logo btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modal-inpost">
                                <i class="fas fa-box fa-2x text-secondary"></i>
                                <span>Inpost</span>
                            </button>
                            <button class="partner-logo btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modal-posteitaliane">
                                <i class="fab fa-telegram-plane fa-2x text-info"></i>
                                <span>PosteItaliane</span>
                            </button>
                            <button class="partner-logo btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modal-fedex">
                                <i class="fas fa-plane fa-2x text-primary"></i>
                                <span>Fedex</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Partner Modals -->
        <!-- DropPoint Modal -->
        <div class="modal fade" id="modal-droppoint" tabindex="-1" aria-labelledby="modal-droppoint-label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-droppoint-label">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>DropPoint
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="lead">DropPoint è la piattaforma digitale per servizi di pagamento e valore aggiunto nel retail, offrendo soluzioni convenienti per esercenti e cittadini.</p>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Servizi Offeriti:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Bollettini postali e bancari</li>
                                    <li><i class="fas fa-check text-success me-2"></i>PagoPA</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Mav e Rav</li>
                                    <li><i class="fas fa-check text-success me-2"></i>F24</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Ricariche telefoniche</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Biglietti trasporto pubblico</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Vantaggi:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-star text-warning me-2"></i>Costi più bassi del mercato</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Nessun costo di attivazione</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Commissioni più alte</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Un solo POS per tutto</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        <a href="?page=contatti" class="btn btn-primary">Richiedi Informazioni</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Namirial Modal -->
        <div class="modal fade" id="modal-namirial" tabindex="-1" aria-labelledby="modal-namirial-label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-namirial-label">
                            <i class="fas fa-signature text-info me-2"></i>Namirial
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="lead">Leader europeo nelle soluzioni di firma digitale e identità elettronica.</p>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Servizi Offerti:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Firma digitale qualificata</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Firma digitale remota</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Certificati digitali</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Validazione documenti</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Vantaggi:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-star text-warning me-2"></i>Conformità legale</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Sicurezza massima</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Facilità d'uso</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Supporto completo</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        <a href="?page=contatti" class="btn btn-primary">Richiedi Informazioni</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aruba Business Modal -->
        <div class="modal fade" id="modal-aruba-business" tabindex="-1" aria-labelledby="modal-aruba-business-label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-aruba-business-label">
                            <i class="fas fa-server text-success me-2"></i>Aruba Business
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="lead">Soluzioni cloud e hosting enterprise per aziende di ogni dimensione.</p>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Servizi Offerti:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Cloud hosting</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Email business</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Certificati SSL</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Backup e disaster recovery</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Vantaggi:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-star text-warning me-2"></i>Reliability 99.9%</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Sicurezza certificata</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Supporto 24/7</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Scalabilità</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        <a href="?page=contatti" class="btn btn-primary">Richiedi Informazioni</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Iliad Modal -->
        <div class="modal fade" id="modal-iliad" tabindex="-1" aria-labelledby="modal-iliad-label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-iliad-label">
                            <i class="fas fa-mobile-alt text-warning me-2"></i>Iliad
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="lead">Operatore telefonico italiano che offre servizi mobili senza costi nascosti.</p>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Servizi Offerti:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Telefonia mobile</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Internet mobile</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Offerte convergenti</li>
                                    <li><i class="fas fa-check text-success me-2"></i>SIM prepagate e abbonamento</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Vantaggi:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-star text-warning me-2"></i>Trasparenza totale</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Nessun costo nascosto</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Rete 4G/5G</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Assistenza diretta</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        <a href="?page=contatti" class="btn btn-primary">Richiedi Informazioni</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Windtre Modal -->
        <div class="modal fade" id="modal-windtre" tabindex="-1" aria-labelledby="modal-windtre-label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-windtre-label">
                            <i class="fas fa-sim-card text-danger me-2"></i>Windtre
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="lead">Operatore integrato che offre servizi mobili e fissi con copertura nazionale estesa.</p>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Servizi Offerti:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Telefonia mobile</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Internet fisso</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Telefonia fissa</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Soluzioni business</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Vantaggi:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-star text-warning me-2"></i>Copertura capillare</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Servizi integrati</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Velocità elevate</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Assistenza dedicata</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        <a href="?page=contatti" class="btn btn-primary">Richiedi Informazioni</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fastweb Modal -->
        <div class="modal fade" id="modal-fastweb" tabindex="-1" aria-labelledby="modal-fastweb-label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-fastweb-label">
                            <i class="fas fa-wifi text-info me-2"></i>Fastweb
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="lead">Provider di telecomunicazioni che offre connessioni internet ad alta velocità e soluzioni smart home.</p>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Servizi Offerti:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Fibra ottica</li>
                                    <li><i class="fas fa-check text-success me-2"></i>ADSL</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Telefonia fissa</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Soluzioni smart home</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Vantaggi:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-star text-warning me-2"></i>Velocità fino a 10 Gbps</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Stabilità di connessione</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Domotica integrata</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Assistenza tecnica</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        <a href="?page=contatti" class="btn btn-primary">Richiedi Informazioni</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- A2A Energia Modal -->
        <div class="modal fade" id="modal-a2a-energia" tabindex="-1" aria-labelledby="modal-a2a-energia-label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-a2a-energia-label">
                            <i class="fas fa-bolt text-warning me-2"></i>A2A Energia
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="lead">Fornitore di energia elettrica e gas con soluzioni sostenibili per famiglie e imprese.</p>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Servizi Offerti:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Energia elettrica</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Gas naturale</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Energia rinnovabile</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Servizi di efficienza energetica</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Vantaggi:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-star text-warning me-2"></i>Energia verde</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Tariffe competitive</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Assistenza clienti</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Sostenibilità</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        <a href="?page=contatti" class="btn btn-primary">Richiedi Informazioni</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enel Energia Modal -->
        <div class="modal fade" id="modal-enel-energia" tabindex="-1" aria-labelledby="modal-enel-energia-label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-enel-energia-label">
                            <i class="fas fa-lightbulb text-success me-2"></i>Enel Energia
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="lead">Principale fornitore di energia elettrica in Italia con soluzioni innovative per il risparmio energetico.</p>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Servizi Offerti:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Energia elettrica</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Gas naturale</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Soluzioni fotovoltaiche</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Servizi di mobilità elettrica</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Vantaggi:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-star text-warning me-2"></i>Innovazione tecnologica</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Efficienza energetica</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>App di controllo</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Servizio clienti 24/7</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        <a href="?page=contatti" class="btn btn-primary">Richiedi Informazioni</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brt Modal -->
        <div class="modal fade" id="modal-brt" tabindex="-1" aria-labelledby="modal-brt-label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-brt-label">
                            <i class="fas fa-truck text-primary me-2"></i>Brt
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="lead">Servizio di corriere espresso leader in Italia per consegne rapide e affidabili.</p>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Servizi Offerti:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Consegne espresso</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Servizi internazionali</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Tracking pacchi</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Soluzioni e-commerce</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Vantaggi:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-star text-warning me-2"></i>Rapidità di consegna</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Rete capillare</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Affidabilità</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Assicurazione merci</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        <a href="?page=contatti" class="btn btn-primary">Richiedi Informazioni</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inpost Modal -->
        <div class="modal fade" id="modal-inpost" tabindex="-1" aria-labelledby="modal-inpost-label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-inpost-label">
                            <i class="fas fa-box text-secondary me-2"></i>Inpost
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="lead">Rete di locker automatici per il ritiro e la spedizione di pacchi in modo sicuro e conveniente.</p>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Servizi Offerti:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Locker automatici</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Ritiro 24/7</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Spedizioni</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Tracking digitale</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Vantaggi:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-star text-warning me-2"></i>Accessibilità 24/7</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Sicurezza massima</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Convenienza</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Eco-sostenibilità</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        <a href="?page=contatti" class="btn btn-primary">Richiedi Informazioni</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- PosteItaliane Modal -->
        <div class="modal fade" id="modal-posteitaliane" tabindex="-1" aria-labelledby="modal-posteitaliane-label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-posteitaliane-label">
                            <i class="fab fa-telegram-plane text-info me-2"></i>PosteItaliane
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="lead">Servizi postali e finanziari italiani con una rete capillare su tutto il territorio nazionale.</p>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Servizi Offerti:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Servizi postali</li>
                                    <li><i class="fas fa-check text-success me-2"></i>PostePay</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Assicurazioni</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Servizi finanziari</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Vantaggi:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-star text-warning me-2"></i>Copertura universale</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Affidabilità storica</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Servizi integrati</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Accessibilità</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        <a href="?page=contatti" class="btn btn-primary">Richiedi Informazioni</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fedex Modal -->
        <div class="modal fade" id="modal-fedex" tabindex="-1" aria-labelledby="modal-fedex-label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-fedex-label">
                            <i class="fas fa-plane text-primary me-2"></i>Fedex
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="lead">Leader mondiale nelle spedizioni express con consegna affidabile in oltre 220 paesi.</p>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Servizi Offerti:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Spedizioni express</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Consegne internazionali</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Logistica integrata</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Soluzioni e-commerce</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Vantaggi:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-star text-warning me-2"></i>Consegna garantita</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Copertura globale</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Tracking avanzato</li>
                                    <li><i class="fas fa-star text-warning me-2"></i>Servizio premium</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        <a href="?page=contatti" class="btn btn-primary">Richiedi Informazioni</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Chi Siamo -->
        <div class="about-panel-full">
            <h2 class="text-center mb-5">Domande Frequenti su di Noi</h2>
            <div class="accordion" id="aboutFaq">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#about-faq1">
                            Da quanto tempo operate sul territorio?
                        </button>
                    </h2>
                    <div id="about-faq1" class="accordion-collapse collapse show" data-bs-parent="#aboutFaq">
                        <div class="accordion-body">
                            Operiamo sul territorio di Castellammare di Stabia e zona dal 2016, celebrando quest'anno il nostro ottavo anniversario. In questi anni abbiamo assistito oltre 12.000 clienti soddisfatti.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#about-faq2">
                            Qual è la vostra mission aziendale?
                        </button>
                    </h2>
                    <div id="about-faq2" class="accordion-collapse collapse" data-bs-parent="#aboutFaq">
                        <div class="accordion-body">
                            La nostra mission è semplificare la vita quotidiana dei cittadini attraverso servizi digitali accessibili e sicuri. Vogliamo essere il ponte tra le persone e la tecnologia, rendendo i servizi complessi semplici e affidabili.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#about-faq3">
                            Come scegliete i vostri partner commerciali?
                        </button>
                    </h2>
                    <div id="about-faq3" class="accordion-collapse collapse" data-bs-parent="#aboutFaq">
                        <div class="accordion-body">
                            Selezioniamo partner che condividono i nostri valori di qualità, sicurezza e servizio al cliente. Collaboriamo esclusivamente con aziende certificate e leader di mercato per garantire ai nostri clienti il massimo livello di affidabilità.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#about-faq4">
                            Quali certificazioni possedete?
                        </button>
                    </h2>
                    <div id="about-faq4" class="accordion-collapse collapse" data-bs-parent="#aboutFaq">
                        <div class="accordion-body">
                            Siamo certificati come RAO (Registration Authority Officer) per la verifica dell'identità personale necessaria per SPID, firme digitali e servizi digitali. Inoltre siamo rivenditori autorizzati di Iliad (telefonia mobile senza costi nascosti), Windtre (servizi integrati mobile e fisso) e Fastweb (connessioni internet ad alta velocità e smart home), garantendo ai nostri clienti le migliori soluzioni di telecomunicazione.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#about-faq5">
                            Come garantire la sicurezza dei dati?
                        </button>
                    </h2>
                    <div id="about-faq5" class="accordion-collapse collapse" data-bs-parent="#aboutFaq">
                        <div class="accordion-body">
                            La sicurezza è la nostra priorità assoluta. Utilizziamo crittografia end-to-end e monitoriamo costantemente i sistemi. I dati dei clienti sono protetti con i più alti standard di sicurezza informatica.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid-full">
            <div class="stat-card-full">
                <span class="stat-number-full"><?php echo htmlspecialchars((string) $yearsInBusiness, ENT_QUOTES); ?>+</span>
                <p class="mb-0">Anni di Esperienza</p>
            </div>
            <div class="stat-card-full">
                <span class="stat-number-full">4.500+</span>
                <p class="mb-0">Pratiche Gestite Annualmente</p>
            </div>
            <div class="stat-card-full">
                <span class="stat-number-full">4</span>
                <p class="mb-0">Linee di Servizio</p>
            </div>
            <div class="stat-card-full">
                <span class="stat-number-full">99.5%</span>
                <p class="mb-0">Tasso di Soddisfazione</p>
            </div>
        </div>

        <!-- Team Section -->
        <div class="team-section">
            <h2 class="text-center mb-5">Il Nostro Team</h2>
            <div class="team-grid">
                <div class="team-member">
                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxjaXJjbGUgY3g9IjUwIiBjeT0iMzUiIHI9IjE1IiBmaWxsPSIjMzM4OEZGIi8+CjxwYXRoIGQ9Ik0yMCA3NVEyMCA2MCAzNSA2MEM1MCA2MCA1MCA3MCA1MCA4NUM1MCA5NSA2NSA5NSA4MCA5NUM4NSA4NSA4NSA3NSA4MCA3NVoiIGZpbGw9IiMzMzg4RkYiLz4KPC9zdmc+" alt="Giuseppe Rossi">
                    <h4>Giuseppe Rossi</h4>
                    <p class="text-muted mb-2">Fondatore & CEO</p>
                    <p class="small">Esperto in servizi digitali e telecomunicazioni con oltre 15 anni di esperienza. Guida la visione strategica dell'azienda e le relazioni con i partner principali.</p>
                    <div class="team-skills">
                        <span class="skill-badge">Leadership</span>
                        <span class="skill-badge">Strategia</span>
                        <span class="skill-badge">Telecomunicazioni</span>
                    </div>
                </div>
                <div class="team-member">
                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxjaXJjbGUgY3g9IjUwIiBjeT0iMzUiIHI9IjE1IiBmaWxsPSIjRUI0NjkyIi8+CjxwYXRoIGQ9Ik0yMCA3NVEyMCA2MCAzNSA2MEM1MCA2MCA1MCA3MCA1MCA4NUM1MCA5NSA2NSA5NSA4MCA5NUM4NSA4NSA4NSA3NSA4MCA3NVoiIGZpbGw9IiNFQjQ2OTIiLz4KPC9zdmc+" alt="Maria Bianchi">
                    <h4>Maria Bianchi</h4>
                    <p class="text-muted mb-2">Responsabile Servizi Clienti</p>
                    <p class="small">Specializzata in customer care e gestione relazioni, garantisce un servizio eccellente. Coordina il team di assistenza e forma il personale.</p>
                    <div class="team-skills">
                        <span class="skill-badge">Customer Care</span>
                        <span class="skill-badge">Formazione</span>
                        <span class="skill-badge">Gestione Team</span>
                    </div>
                </div>
                <div class="team-member">
                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxjaXJjbGUgY3g9IjUwIiBjeT0iMzUiIHI9IjE1IiBmaWxsPSIjMjA5Q0U5Ii8+CjxwYXRoIGQ9Ik0yMCA3NVEyMCA2MCAzNSA2MEM1MCA2MCA1MCA3MCA1MCA4NUM1MCA5NSA2NSA5NSA4MCA5NUM4NSA4NSA4NSA3NSA4MCA3NVoiIGZpbGw9IiMyMDlDRjkiLz4KPC9zdmc+" alt="Luca Verdi">
                    <h4>Luca Verdi</h4>
                    <p class="text-muted mb-2">Tecnico Specializzato</p>
                    <p class="small">Esperto in attivazioni digitali e supporto tecnico. Specializzato in SPID, PEC, firma digitale e assistenza tecnica avanzata.</p>
                    <div class="team-skills">
                        <span class="skill-badge">SPID/PEC</span>
                        <span class="skill-badge">Firma Digitale</span>
                        <span class="skill-badge">Supporto Tecnico</span>
                    </div>
                </div>
                <div class="team-member">
                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iMzUiIHI9IjE1IiBmaWxsPSIjMTdhMmI4Ii8+CjxwYXRoIGQ9Ik0yMCA3NVEyMCA2MCAzNSA2MEM1MCA2MCA1MCA3MCA1MCA4NUM1MCA5NSA2NSA5NSA4MCA5NUM4NSA4NSA4NSA3NSA4MCA3NVoiIGZpbGw9IiMxN2EyYjgiLz4KPC9zdmc+" alt="Sara Neri">
                    <h4>Sara Neri</h4>
                    <p class="text-muted mb-2">Specialista Pagamenti Digitali</p>
                    <p class="small">Esperta in soluzioni di pagamento elettronico e fintech. Gestisce partnership con istituti bancari e piattaforme di pagamento digitali.</p>
                    <div class="team-skills">
                        <span class="skill-badge">Pagamenti</span>
                        <span class="skill-badge">Fintech</span>
                        <span class="skill-badge">Partnership</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Testimonial Approfonditi -->
        <div class="testimonials-section">
            <h2 class="text-center mb-5">Cosa Dicono di Noi</h2>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="testimonial-detailed">
                        <div class="testimonial-header">
                            <div class="testimonial-avatar">
                                <i class="fas fa-user-circle fa-3x text-primary"></i>
                            </div>
                            <div class="testimonial-info">
                                <h5>Antonio Marino</h5>
                                <p class="text-muted mb-1">Titolare Pizzeria "Da Antonio"</p>
                                <div class="testimonial-rating">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                </div>
                            </div>
                        </div>
                        <blockquote class="testimonial-quote">
                            "AG SERVIZI mi ha aiutato a digitalizzare completamente la mia attività. Dal POS moderno ai pagamenti contactless, fino alla PEC aziendale. Il loro supporto è stato fondamentale per crescere nel mercato attuale. Servizio professionale e sempre disponibile."
                        </blockquote>
                        <div class="testimonial-meta">
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt me-1"></i>Cliente dal 2018
                                <i class="fas fa-map-marker-alt ms-3 me-1"></i>Castellammare di Stabia
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="testimonial-detailed">
                        <div class="testimonial-header">
                            <div class="testimonial-avatar">
                                <i class="fas fa-user-circle fa-3x text-success"></i>
                            </div>
                            <div class="testimonial-info">
                                <h5>Dott.ssa Elena Russo</h5>
                                <p class="text-muted mb-1">Studio Legale Russo & Associati</p>
                                <div class="testimonial-rating">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                </div>
                            </div>
                        </div>
                        <blockquote class="testimonial-quote">
                            "La competenza tecnica e l'affidabilità sono impressionanti. Hanno gestito la migrazione di tutto il nostro sistema documentale alla firma digitale con una precisione assoluta. Il team è preparato e sa ascoltare le esigenze specifiche di uno studio legale."
                        </blockquote>
                        <div class="testimonial-meta">
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt me-1"></i>Cliente dal 2020
                                <i class="fas fa-map-marker-alt ms-3 me-1"></i>Sorrento
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="testimonial-detailed">
                        <div class="testimonial-header">
                            <div class="testimonial-avatar">
                                <i class="fas fa-user-circle fa-3x text-info"></i>
                            </div>
                            <div class="testimonial-info">
                                <h5>Famiglia Esposito</h5>
                                <p class="text-muted mb-1">Famiglia con 3 figli</p>
                                <div class="testimonial-rating">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                </div>
                            </div>
                        </div>
                        <blockquote class="testimonial-quote">
                            "Hanno organizzato un corso di alfabetizzazione digitale gratuito per me e mio marito. Ora riusciamo a fare tutto online: dallo SPID alle pratiche INPS. La loro pazienza e disponibilità hanno fatto la differenza per persone come noi meno abituate alla tecnologia."
                        </blockquote>
                        <div class="testimonial-meta">
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt me-1"></i>Partecipanti dal 2021
                                <i class="fas fa-map-marker-alt ms-3 me-1"></i>Castellammare di Stabia
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="testimonial-detailed">
                        <div class="testimonial-header">
                            <div class="testimonial-avatar">
                                <i class="fas fa-user-circle fa-3x text-warning"></i>
                            </div>
                            <div class="testimonial-info">
                                <h5>Marco Santoro</h5>
                                <p class="text-muted mb-1">Freelance Web Developer</p>
                                <div class="testimonial-rating">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                </div>
                            </div>
                        </div>
                        <blockquote class="testimonial-quote">
                            "Come professionista IT apprezzo la loro competenza tecnica. Mi hanno aiutato con configurazioni avanzate di rete e sicurezza informatica. Il supporto è sempre tempestivo e le soluzioni proposte sono sempre all'avanguardia."
                        </blockquote>
                        <div class="testimonial-meta">
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt me-1"></i>Cliente dal 2019
                                <i class="fas fa-map-marker-alt ms-3 me-1"></i>Vico Equense
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metriche Avanzate -->
        <div class="advanced-metrics">
            <h2 class="text-center mb-5">I Nostri Risultati in Numeri</h2>
            <div class="metrics-dashboard">
                <div class="metric-row">
                    <div class="metric-item">
                        <div class="metric-chart">
                            <div class="chart-circle" data-percent="99.5">
                                <span class="chart-number">99.5%</span>
                                <span class="chart-label">Soddisfazione</span>
                            </div>
                        </div>
                        <p class="metric-description">Tasso di soddisfazione clienti basato su oltre 4.500 pratiche gestite annualmente</p>
                    </div>
                    <div class="metric-item">
                        <div class="metric-chart">
                            <div class="chart-circle" data-percent="24">
                                <span class="chart-number">< 24h</span>
                                <span class="chart-label">Risposta Media</span>
                            </div>
                        </div>
                        <p class="metric-description">Tempo medio di risposta alle richieste, garantito per tutti i canali</p>
                    </div>
                    <div class="metric-item">
                        <div class="metric-chart">
                            <div class="chart-circle" data-percent="100">
                                <span class="chart-number">100%</span>
                                <span class="chart-label">Certificato</span>
                            </div>
                        </div>
                        <p class="metric-description">Processi certificati per la sicurezza delle informazioni</p>
                    </div>
                </div>
                <div class="metric-highlights">
                    <div class="highlight-item">
                        <div class="highlight-number">12.000+</div>
                        <div class="highlight-label">Clienti Soddisfatti</div>
                        <div class="highlight-trend">↗️ +15% annuo</div>
                    </div>
                    <div class="highlight-item">
                        <div class="highlight-number">500+</div>
                        <div class="highlight-label">Corsi Formazione</div>
                        <div class="highlight-trend">↗️ +25% annuo</div>
                    </div>
                    <div class="highlight-item">
                        <div class="highlight-number">10+</div>
                        <div class="highlight-label">Partner Certificati</div>
                        <div class="highlight-trend">↗️ Costante</div>
                    </div>
                    <div class="highlight-item">
                        <div class="highlight-number">8</div>
                        <div class="highlight-label">Anni di Attività</div>
                        <div class="highlight-trend">↗️ In crescita</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="cta-about">
            <h3>Hai Bisogno di Assistenza?</h3>
            <p>Siamo qui per aiutarti con qualsiasi domanda sui nostri servizi. Contattaci per una consulenza gratuita.</p>
            <a href="?page=contatti" class="btn">
                <i class="fas fa-envelope me-2"></i>Contattaci Ora
            </a>
        </div>
    </div>
</section>

<script>
// Animate metric circles on scroll
document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'pulse 2s ease-in-out';
            }
        });
    }, observerOptions);

    // Observe metric circles
    document.querySelectorAll('.chart-circle').forEach(circle => {
        observer.observe(circle);
    });

    // Add animation keyframes
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .partner-logo {
            transition: all 0.3s ease;
            border-radius: 8px;
            padding: 10px;
            margin: 5px;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            min-height: 80px;
            justify-content: center;
        }

        .partner-logo:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            background-color: rgba(255,255,255,0.05);
        }

        .partner-logo i {
            font-size: 2rem;
            margin-bottom: 5px;
        }

        .partner-logo span {
            font-size: 0.85rem;
            font-weight: 500;
        }
    `;
    document.head.appendChild(style);
});
</script>
