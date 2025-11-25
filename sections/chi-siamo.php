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

                    <!-- Mappa Interattiva -->
                    <div class="map-container mt-4">
                        <div 
                            data-map 
                            data-map-lat="40.6994991" 
                            data-map-lng="14.4851434"
                            style="width: 100%; height: 350px; border-radius: 12px; border: 1px solid #e9ecef; overflow: hidden;">
                        </div>
                        <p class="text-center mt-2 small text-muted">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            Via Plinio il Vecchio, 72 - Castellammare di Stabia (NA)
                        </p>
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