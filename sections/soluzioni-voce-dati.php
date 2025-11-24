<?php
/**
 * Sezione Soluzioni Voce e Dati
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
                        <i class="fas fa-phone me-3"></i>Soluzioni Voce e Dati
                    </h1>
                    <p class="lead text-muted mb-4 fs-5">
                        Presso la nostra agenzia attiviamo linee telefoniche, connessioni internet e servizi di comunicazione per privati e aziende, con assistenza completa e portabilità guidata.
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

        <!-- Servizi di Voce e Dati -->
        <div class="row g-4 mb-5">
            <!-- Linee Telefoniche -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Linee Telefoniche</h5>
                        </div>
                        <p class="card-text text-muted">
                            Attiviamo linee mobili e fisse con i principali operatori italiani:
                        </p>
                        <ul class="list-unstyled">
                            <li><span class="fw-medium">TIM</span></li>
                            <li><span class="fw-medium">Vodafone</span></li>
                            <li><span class="fw-medium">WindTre</span></li>
                            <li><span class="fw-medium">Iliad</span></li>
                            <li><span class="fw-medium">Fastweb</span></li>
                            <li><span class="fw-medium">Tiscali</span></li>
                        </ul>
                        <p class="card-text small text-info fw-medium">
                            Portabilità assistita e attivazione in giornata.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Connessioni Internet -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-wifi"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Connessioni Internet</h5>
                        </div>
                        <p class="card-text text-muted">
                            Fibra ottica, ADSL e connessioni dedicate per casa e ufficio:
                        </p>
                        <ul class="list-unstyled">
                            <li><span class="fw-medium">Fibra FTTH fino a 2.5 Gbps</span></li>
                            <li><span class="fw-medium">ADSL fino a 20 Mbps</span></li>
                            <li><span class="fw-medium">Connessioni dedicate business</span></li>
                            <li><span class="fw-medium">Wi-Fi mesh e ripetitori</span></li>
                        </ul>
                        <p class="card-text small text-info fw-medium">
                            Installazione professionale e assistenza tecnica inclusa.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Device e Accessori -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-tablet-alt"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Device e Accessori</h5>
                        </div>
                        <p class="card-text text-muted">
                            Smartphone, tablet e accessori tech con rateizzazione:
                        </p>
                        <ul class="list-unstyled">
                            <li><span class="fw-medium">Smartphone di ultima generazione</span></li>
                            <li><span class="fw-medium">Tablet e e-reader</span></li>
                            <li><span class="fw-medium">Cuffie e auricolari wireless</span></li>
                            <li><span class="fw-medium">Power bank e caricatori</span></li>
                            <li><span class="fw-medium">Cover e protezioni</span></li>
                        </ul>
                        <p class="card-text small text-info fw-medium">
                            Rateizzazione senza interessi e consegna a domicilio.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Servizi Aziendali -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-building"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Servizi Aziendali</h5>
                        </div>
                        <p class="card-text text-muted">
                            Soluzioni complete per aziende e professionisti:
                        </p>
                        <ul class="list-unstyled">
                            <li><span class="fw-medium">Centralini VoIP</span></li>
                            <li><span class="fw-medium">Numeri verdi e fax</span></li>
                            <li><span class="fw-medium">VPN e sicurezza informatica</span></li>
                            <li><span class="fw-medium">Cloud storage e backup</span></li>
                        </ul>
                        <p class="card-text small text-info fw-medium">
                            Dashboard di monitoraggio e supporto dedicato.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Assistenza e Manutenzione -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-tools"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Assistenza e Manutenzione</h5>
                        </div>
                        <p class="card-text text-muted">
                            Supporto tecnico completo per tutti i tuoi dispositivi:
                        </p>
                        <ul class="list-unstyled">
                            <li><span class="fw-medium">Riparazione smartphone e tablet</span></li>
                            <li><span class="fw-medium">Configurazione dispositivi</span></li>
                            <li><span class="fw-medium">Ottimizzazione connessioni</span></li>
                            <li><span class="fw-medium">Consulenza tecnica</span></li>
                        </ul>
                        <p class="card-text small text-info fw-medium">
                            Intervento rapido e garanzia sui lavori.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action Pulita -->
        <div class="row">
            <div class="col-12">
                <div class="cta-clean text-center">
                    <h3 class="mb-3 fw-bold">
                        <i class="fas fa-handshake me-2"></i>Hai Bisogno di Soluzioni Telecom?
                    </h3>
                    <p class="mb-4 fs-5">Siamo qui per aiutarti con linee telefoniche, internet e dispositivi. Contattaci per assistenza personalizzata.</p>
                    <a href="?page=contatti" class="btn">
                        <i class="fas fa-envelope me-2"></i>Richiedi Assistenza
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>