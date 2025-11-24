<?php
/**
 * Sezione Pagamenti Certificati
 */
?>
<?php
/**
 * Sezione Pagamenti Certificati
 */
?>
<style>
.payment-hero-bg {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow: hidden;
}

.payment-hero-bg::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translate(-50%, -50%) rotate(0deg); }
    50% { transform: translate(-50%, -50%) rotate(180deg); }
}

.modern-card {
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
    border: none;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
    background-size: 200% 100%;
    animation: gradient-shift 3s ease infinite;
}

@keyframes gradient-shift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.modern-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.modern-card .card-body {
    padding: 2rem;
}

.service-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-size: 1.5rem;
    margin-right: 1rem;
    flex-shrink: 0;
}

.modern-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 25px;
    padding: 0.5rem 1.5rem;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.cta-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 25px;
    padding: 3rem;
    position: relative;
    overflow: hidden;
}

.cta-section::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 50%);
    border-radius: 50%;
}

.cta-section .btn {
    background: white;
    color: #667eea;
    border: none;
    border-radius: 50px;
    padding: 1rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.cta-section .btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.cta-section .btn:hover::before {
    left: 100%;
}

.cta-section .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.decorative-shape {
    position: absolute;
    width: 100px;
    height: 100px;
    background: linear-gradient(45deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    border-radius: 50%;
    top: -50px;
    right: -50px;
    z-index: 0;
}

.floating-elements {
    position: relative;
}

.floating-elements::after {
    content: '💳💰📱';
    position: absolute;
    top: -20px;
    right: 20px;
    font-size: 2rem;
    opacity: 0.1;
    animation: bounce 4s ease-in-out infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}
</style>

<section class="section-padding" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); position: relative;">
    <!-- Elementi decorativi di sfondo -->
    <div class="decorative-shape"></div>
    <div class="decorative-shape" style="top: 20%; left: -30px; width: 80px; height: 80px;"></div>
    <div class="decorative-shape" style="bottom: 10%; right: 10%; width: 60px; height: 60px;"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Hero Section Moderna -->
                <div class="payment-hero-bg text-white rounded-4 p-5 mb-5 text-center position-relative">
                    <div class="position-relative z-index-1">
                        <h1 class="display-4 fw-bold mb-3 floating-elements">
                            <i class="fas fa-credit-card me-3"></i>Servizi di Pagamento
                        </h1>
                        <p class="lead mb-4 fs-5">
                            Presso la nostra agenzia puoi effettuare tutti i principali pagamenti in modo semplice, veloce e con commissioni tra le più basse del mercato. Siamo qui per aiutarti e garantirti un'assistenza completa su ogni operazione.
                        </p>
                        <div class="row g-3 justify-content-center">
                            <div class="col-auto">
                                <span class="modern-badge">
                                    <i class="fas fa-bolt me-2"></i>Rapido
                                </span>
                            </div>
                            <div class="col-auto">
                                <span class="modern-badge">
                                    <i class="fas fa-coins me-2"></i>Conveniente
                                </span>
                            </div>
                            <div class="col-auto">
                                <span class="modern-badge">
                                    <i class="fas fa-shield-alt me-2"></i>Sicuro
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servizi di Pagamento -->
        <div class="row g-4 mb-5">
            <!-- Bollettini Postali -->
            <div class="col-md-6 col-lg-4">
                <div class="modern-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon">
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
            <div class="col-md-6 col-lg-4">
                <div class="modern-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon">
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
            <div class="col-md-6 col-lg-4">
                <div class="modern-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon">
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
            <div class="col-md-6 col-lg-4">
                <div class="modern-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon">
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
            <div class="col-md-6 col-lg-4">
                <div class="modern-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon">
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
            <div class="col-md-6 col-lg-4">
                <div class="modern-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon">
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
            <div class="col-md-6 col-lg-4">
                <div class="modern-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon">
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
            <div class="col-md-6 col-lg-4">
                <div class="modern-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="service-icon">
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

        <!-- Call to Action Moderna -->
        <div class="row">
            <div class="col-12">
                <div class="cta-section text-white text-center position-relative">
                    <div class="position-relative z-index-1">
                        <h3 class="mb-3 fw-bold">
                            <i class="fas fa-handshake me-2"></i>Hai Bisogno di Effettuare un Pagamento?
                        </h3>
                        <p class="mb-4 fs-5">Siamo qui per aiutarti con qualsiasi tipo di pagamento. Contattaci per assistenza personalizzata.</p>
                        <a href="?page=contatti" class="btn btn-lg">
                            <i class="fas fa-envelope me-2"></i>Richiedi Assistenza
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>