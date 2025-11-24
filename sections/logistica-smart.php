<?php
/**
 * Sezione Logistica Smart
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
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="?page=home">Home</a></li>
                        <li class="breadcrumb-item"><a href="?page=home#servizi">Servizi</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Logistica Smart</li>
                    </ol>
                </nav>

                <!-- Hero Section Pulita -->
                <div class="hero-section-clean text-center mb-5">
                    <h1 class="display-4 fw-bold text-primary mb-3">
                        <i class="fas fa-truck me-3"></i>Logistica Smart
                    </h1>
                    <p class="lead text-muted mb-4 fs-5">
                        Presso la nostra agenzia gestiamo spedizioni di pacchi e corrispondenza con i principali corrieri italiani e internazionali, garantendo tracciabilità completa e massima affidabilità.
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

        <!-- Servizi di Logistica -->
        <div class="row g-4 mb-5">
            <!-- Spedizioni Nazionali -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Spedizioni Nazionali</h5>
                        </div>
                        <p class="card-text text-muted">
                            Consegne rapide in tutta Italia con i migliori corrieri:
                        </p>
                        <ul class="list-unstyled">
                            <li><span class="fw-medium">BRT - Bartolini</span></li>
                            <li><span class="fw-medium">Poste Italiane</span></li>
                            <li><span class="fw-medium">FedEx Express</span></li>
                        </ul>
                        <p class="card-text small text-info fw-medium">
                            Tracking in tempo reale e consegna garantita entro 24-48 ore.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Spedizioni Internazionali -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-globe"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Spedizioni Internazionali</h5>
                        </div>
                        <p class="card-text text-muted">
                            Servizi di spedizione verso tutti i paesi del mondo:
                        </p>
                        <ul class="list-unstyled">
                            <li><span class="fw-medium">FedEx International</span></li>
                            <li><span class="fw-medium">Poste Italiane EMS</span></li>
                            <li><span class="fw-medium">BRT Global</span></li>
                        </ul>
                        <p class="card-text small text-info fw-medium">
                            Documenti doganali inclusi e monitoraggio completo del percorso.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Servizi Aggiuntivi -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-box"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Servizi Aggiuntivi</h5>
                        </div>
                        <p class="card-text text-muted">
                            Servizi complementari per le tue spedizioni:
                        </p>
                        <ul class="list-unstyled">
                            <li><span class="fw-medium">Imballaggio professionale</span></li>
                            <li><span class="fw-medium">Assicurazione merci</span></li>
                            <li><span class="fw-medium">Ritiro a domicilio</span></li>
                            <li><span class="fw-medium">Consegna programmata</span></li>
                        </ul>
                        <p class="card-text small text-info fw-medium">
                            Massima protezione e flessibilità per ogni tipo di spedizione.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tracking e Monitoraggio -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-search-location"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Tracking e Monitoraggio</h5>
                        </div>
                        <p class="card-text text-muted">
                            Monitora le tue spedizioni in tempo reale:
                        </p>
                        <ul class="list-unstyled">
                            <li><span class="fw-medium">Tracking online 24/7</span></li>
                            <li><span class="fw-medium">Notifiche SMS/Email</span></li>
                            <li><span class="fw-medium">Stato consegna aggiornato</span></li>
                            <li><span class="fw-medium">Preavviso consegna</span></li>
                        </ul>
                        <p class="card-text small text-info fw-medium">
                            Sempre informato su ogni fase del trasporto.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Soluzioni Aziendali -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-building"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Soluzioni Aziendali</h5>
                        </div>
                        <p class="card-text text-muted">
                            Servizi dedicati per aziende e professionisti:
                        </p>
                        <ul class="list-unstyled">
                            <li><span class="fw-medium">Contratti dedicati</span></li>
                            <li><span class="fw-medium">Spedizioni bulk</span></li>
                            <li><span class="fw-medium">Logistica integrata</span></li>
                            <li><span class="fw-medium">Report mensili</span></li>
                        </ul>
                        <p class="card-text small text-info fw-medium">
                            Ottimizzazione costi e massima efficienza operativa.
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
                        <i class="fas fa-handshake me-2"></i>Hai Bisogno di Spedire?
                    </h3>
                    <p class="mb-4 fs-5">Siamo qui per aiutarti con qualsiasi tipo di spedizione. Contattaci per assistenza personalizzata.</p>
                    <a href="?page=contatti" class="btn">
                        <i class="fas fa-envelope me-2"></i>Richiedi Assistenza
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>