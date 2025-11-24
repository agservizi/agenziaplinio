<?php
/**
 * Sezione Identità Digitale
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
                        <i class="fas fa-id-card me-3"></i>Identità Digitale, PEC e Firma Digitale
                    </h1>
                    <p class="lead text-muted mb-4 fs-5">
                        Presso la nostra agenzia puoi attivare in modo semplice e veloce tutti i principali servizi digitali necessari per comunicare con la Pubblica Amministrazione e firmare documenti in totale sicurezza.
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

        <!-- Servizi di Identità Digitale -->
        <div class="row g-4 mb-5">
            <!-- SPID – La tua Identità Digitale -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">SPID – La tua Identità Digitale</h5>
                        </div>
                        <p class="card-text text-muted">
                            Ti attiviamo lo SPID Namirial, l'identità digitale che ti permette di accedere a tutti i servizi online della Pubblica Amministrazione e di moltissime aziende private.
                        </p>
                        <p class="card-text text-muted">
                            Riceverai le credenziali direttamente via email e potrai completare l'attivazione in pochi minuti tramite smartphone o computer. Una volta attivo, potrai accedere ovunque con la massima sicurezza grazie ai codici OTP generati dall'app dedicata.
                        </p>
                        <p class="card-text fw-medium">
                            In breve: accesso rapido, riconoscimento certo e totale protezione dei tuoi dati.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Firma Digitale – Valore Legale ai Tuoi Documenti -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-signature"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Firma Digitale – Valore Legale ai Tuoi Documenti</h5>
                        </div>
                        <p class="card-text text-muted">
                            Con la firma digitale Namirial puoi firmare documenti elettronici con pieno valore legale, esattamente come una firma autografa su carta.
                        </p>
                        <p class="card-text text-muted">
                            È disponibile sia in versione dispositivo fisico (smart card o token USB) sia in versione remota, così puoi firmare ovunque ti trovi.
                        </p>
                        <p class="card-text fw-medium">
                            Garantisce autenticità, integrità del documento e conformità alle normative europee (eIDAS).
                        </p>
                        <p class="card-text small text-info fw-medium">
                            Perfetta per contratti, pratiche, atti ufficiali e qualunque documento che richieda un'identificazione certa.
                        </p>
                    </div>
                </div>
            </div>

            <!-- PEC – La Posta Certificata con Valore Legale -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">PEC – La Posta Certificata con Valore Legale</h5>
                        </div>
                        <p class="card-text text-muted">
                            La PEC Namirial ti permette di inviare e ricevere comunicazioni con valore legale, equivalenti a una raccomandata con ricevuta di ritorno.
                        </p>
                        <p class="card-text text-muted">
                            È la soluzione ideale per comunicare con enti pubblici, professionisti, aziende e privati in modo rapido, tracciato e sicuro.
                        </p>
                        <p class="card-text fw-medium">
                            Ogni messaggio è protetto, certificato e archiviato, con ricevute che attestano l'invio e la consegna.
                        </p>
                        <p class="card-text small text-info fw-medium">
                            Riduci tempi e costi, eviti la carta e hai sempre tutto sotto controllo.
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
                        <i class="fas fa-handshake me-2"></i>Hai Bisogno di Servizi Digitali?
                    </h3>
                    <p class="mb-4 fs-5">Siamo qui per aiutarti con SPID, firma digitale e PEC. Contattaci per assistenza personalizzata.</p>
                    <a href="?page=contatti" class="btn">
                        <i class="fas fa-envelope me-2"></i>Richiedi Assistenza
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>