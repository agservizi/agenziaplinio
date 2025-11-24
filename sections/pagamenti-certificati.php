<?php
/**
 * Sezione Pagamenti Certificati
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

.service-badge {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border: none;
    border-radius: 20px;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
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
                        <i class="fas fa-credit-card me-3"></i>Servizi di Pagamento
                    </h1>
                    <p class="lead text-muted mb-4 fs-5">
                        Presso la nostra agenzia puoi effettuare tutti i principali pagamenti in modo semplice, veloce e con commissioni tra le più basse del mercato. Siamo qui per aiutarti e garantirti un'assistenza completa su ogni operazione.
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

        <!-- Servizi di Pagamento -->
        <div class="row g-4 mb-5">
            <!-- Bollettini Postali -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="service-badge">
                                <i class="fas fa-envelope"></i>
                                <span>Postale</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Bollettini Postali</h5>
                        </div>
                        <p class="card-text text-muted">
                            Puoi pagare i tuoi bollettini bianchi (TD 123 e TD 451) e premarcati (TD 674 e TD 896) senza stress e senza code.
                            Accettiamo oltre 20.000 beneficiari grazie alla rete A-Tono Payment Institute.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bollettini pagoPA -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="service-badge">
                                <i class="fas fa-university"></i>
                                <span>pagoPA</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-university"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Bollettini pagoPA</h5>
                        </div>
                        <p class="card-text text-muted">
                            Paghi comodamente qualsiasi avviso pagoPA:
                        </p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check-circle text-success me-2"></i><span class="fw-medium">Utenze</span></li>
                            <li><i class="fas fa-check-circle text-success me-2"></i><span class="fw-medium">Tasse</span></li>
                            <li><i class="fas fa-check-circle text-success me-2"></i><span class="fw-medium">Multe</span></li>
                            <li><i class="fas fa-check-circle text-success me-2"></i><span class="fw-medium">Ticket sanitari</span></li>
                            <li><i class="fas fa-check-circle text-success me-2"></i><span class="fw-medium">Pagamenti PA</span></li>
                        </ul>
                        <p class="card-text small text-muted">
                            <i class="fas fa-mobile-alt me-1"></i>Ti basta portarci l'avviso o mostrarlo dal telefono.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Deleghe F24 -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="service-badge">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span>F24</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Deleghe F24</h5>
                        </div>
                        <p class="card-text text-muted">
                            Hai un F24 da pagare? Pensiamo a tutto noi.
                            Gestiamo qualsiasi tipologia di imposta, tributo o contributo, in totale sicurezza.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bollettini Bancari -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="service-badge">
                                <i class="fas fa-piggy-bank"></i>
                                <span>Bancario</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-piggy-bank"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Bollettini Bancari</h5>
                        </div>
                        <p class="card-text text-muted">
                            Effettuiamo pagamenti verso qualsiasi beneficiario dotato di IBAN bancario o postale, anche con prenotazione.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bollettini MAV e RAV -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="service-badge">
                                <i class="fas fa-receipt"></i>
                                <span>MAV/RAV</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Bollettini MAV e RAV</h5>
                        </div>
                        <p class="card-text text-muted">
                            Puoi saldare MAV e RAV in pochi minuti.
                            Sono i bollettini emessi da banche e istituti di credito per pagamenti a distanza o riscossioni iscritte a ruolo.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bollo Auto -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="service-badge">
                                <i class="fas fa-car"></i>
                                <span>Auto</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-car"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Bollo Auto</h5>
                        </div>
                        <p class="card-text text-muted">
                            Da noi puoi pagare anche il tuo Bollo Auto in modo rapido e senza preoccupazioni.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Versamenti su Conti DropPay -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="service-badge">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>DropPay</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">Versamenti DropPay</h5>
                        </div>
                        <p class="card-text text-muted">
                            Effettuiamo per te versamenti di contanti direttamente sul tuo conto DropPay.
                        </p>
                    </div>
                </div>
            </div>

            <!-- DropPayCard -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="clean-card h-100">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="service-badge">
                                <i class="fas fa-credit-card"></i>
                                <span>Carta</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon-clean">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">DropPayCard</h5>
                        </div>
                        <p class="card-text text-muted">
                            Presso la nostra agenzia puoi:
                        </p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-plus-circle text-primary me-2"></i><span class="fw-medium">Acquistare la DropPayCard by Mastercard</span></li>
                            <li><i class="fas fa-sync-alt text-primary me-2"></i><span class="fw-medium">Ricaricare il tuo conto DropPay associato</span></li>
                        </ul>
                        <p class="card-text small text-info fw-medium">
                            <i class="fas fa-star me-1"></i>Perfetta per chi vuole una carta versatile e subito disponibile.
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
                        <i class="fas fa-handshake me-2"></i>Hai Bisogno di Effettuare un Pagamento?
                    </h3>
                    <p class="mb-4 fs-5">Siamo qui per aiutarti con qualsiasi tipo di pagamento. Contattaci per assistenza personalizzata.</p>
                    <a href="?page=contatti" class="btn">
                        <i class="fas fa-envelope me-2"></i>Richiedi Assistenza
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>