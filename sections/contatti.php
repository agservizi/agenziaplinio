<?php
/**
 * Pagina Contatti
 */
?>
<style>
/* Hero Section with Parallax Effect */
.contact-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    background-attachment: fixed;
    background-size: cover;
    border-radius: 20px;
    padding: 4rem 2rem;
    text-align: center;
    margin-bottom: 4rem;
    position: relative;
    overflow: hidden;
    color: white;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.contact-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.3);
    z-index: 1;
}

.contact-hero h1, .contact-hero p {
    position: relative;
    z-index: 2;
}

.contact-hero h1 {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    animation: fadeInUp 1s ease-out;
}

.contact-hero p {
    font-size: 1.3rem;
    max-width: 700px;
    margin: 0 auto;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    animation: fadeInUp 1.2s ease-out;
}

/* Contact Details Section */
.contact-details-full {
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
    border: none;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    padding: 3rem;
    margin-bottom: 4rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.contact-details-full:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.contact-channel-grid-full {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.contact-channel-full {
    background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 2rem;
    border-left: 5px solid #007bff;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.contact-channel-full::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #007bff, #28a745);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.contact-channel-full:hover::before {
    transform: scaleX(1);
}

.contact-channel-full:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,123,255,0.2);
}

.contact-channel-full__label {
    font-weight: 700;
    color: #495057;
    display: block;
    margin-bottom: 0.75rem;
    font-size: 1.1rem;
}

.contact-channel-full__value {
    color: #007bff;
    text-decoration: none;
    font-weight: 600;
    display: block;
    margin-bottom: 0.75rem;
    font-size: 1.05rem;
    transition: color 0.3s ease;
}

.contact-channel-full__value:hover {
    color: #0056b3;
    text-decoration: underline;
}

.contact-channel-full__meta {
    font-size: 0.9rem;
    color: #6c757d;
    margin: 0;
}

/* SLA Section */
.contact-sla-full {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.contact-sla-full li {
    text-align: center;
    padding: 1.5rem;
    background: linear-gradient(145deg, #e9ecef 0%, #dee2e6 100%);
    border-radius: 12px;
    transition: transform 0.3s ease;
}

.contact-sla-full li:hover {
    transform: scale(1.05);
}

.contact-sla-full__value {
    font-size: 2.5rem;
    font-weight: 800;
    color: #007bff;
    display: block;
    margin-bottom: 0.5rem;
}

.contact-sla-full__label {
    font-size: 0.95rem;
    color: #495057;
    font-weight: 600;
}

/* Meta List */
.contact-meta-list-full {
    list-style: none;
    padding: 0;
    margin-bottom: 2rem;
}

.contact-meta-list-full li {
    padding: 1rem 0;
    border-bottom: 2px solid #e9ecef;
    transition: border-color 0.3s ease;
}

.contact-meta-list-full li:hover {
    border-bottom-color: #007bff;
}

.contact-meta-list-full__label {
    font-weight: 700;
    color: #495057;
    display: block;
    margin-bottom: 0.25rem;
}

.contact-meta-list-full__value {
    color: #6c757d;
    margin-top: 0.25rem;
    font-size: 1.05rem;
}

/* Map Container */
.map-container-full {
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
    border: none;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    margin-bottom: 4rem;
    transition: transform 0.3s ease;
}

.map-container-full:hover {
    transform: translateY(-3px);
}

.map-container-full iframe {
    border-radius: 15px;
    border: 2px solid #e9ecef;
}

/* Directions Grid */
.directions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2.5rem;
    margin-bottom: 2rem;
}

.directions-item {
    padding: 2rem;
    background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    border-left: 5px solid #007bff;
    transition: all 0.3s ease;
    position: relative;
}

.directions-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.directions-item h5 {
    color: #495057;
    margin-bottom: 1.5rem;
    font-size: 1.2rem;
    font-weight: 700;
}

.directions-item ul {
    margin: 0;
    padding-left: 1.5rem;
}

.directions-item li {
    margin-bottom: 0.75rem;
    line-height: 1.6;
    font-size: 1.05rem;
}

/* Contact Form */
.contact-form-full {
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
    border: none;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    padding: 3rem;
    transition: transform 0.3s ease;
}

.contact-form-full:hover {
    transform: translateY(-3px);
}

.contact-form-full .eyebrow {
    color: #6c757d;
    font-size: 0.95rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.75rem;
}

.contact-form-full h3 {
    color: #495057;
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
}

.contact-form-full p {
    color: #6c757d;
    margin-bottom: 2.5rem;
    font-size: 1.1rem;
}

/* Buttons */
.ap-btn {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border: none;
    border-radius: 30px;
    padding: 0.875rem 2.5rem;
    font-weight: 700;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,123,255,0.3);
}

.ap-btn:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,123,255,0.4);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .contact-hero {
        padding: 2rem 1rem;
    }
    .contact-hero h1 {
        font-size: 2.5rem;
    }
    .contact-details-full, .contact-form-full {
        padding: 2rem;
    }
    .directions-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<section class="section-padding bg-light">
    <div class="container">
        <!-- Hero Section -->
        <div class="contact-hero">
            <h1><i class="fas fa-envelope me-3"></i>Contatti</h1>
            <p>Team operations attivo 7/7 per supporto, consulenze e attivazioni. Rispondiamo entro 1 ora.</p>
        </div>

        <!-- Contact Details -->
        <div class="contact-details-full">
            <div class="row">
                <div class="col-lg-8">
                    <h2>Parliamo in modo operativo</h2>
                    <p class="mb-4">Team operations attivo 7/7 per onboarding multiservizio, attivazioni digitali e supporto post vendita. Rispondiamo entro 1 ora.</p>

                    <!-- Quick Action Buttons -->
                    <div class="quick-actions mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="tel:+390810584542" class="btn btn-success w-100 py-3 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fas fa-phone"></i>
                                    <span>Chiama Ora</span>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="https://wa.me/393773798570" target="_blank" rel="noopener" class="btn btn-success w-100 py-3 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fab fa-whatsapp"></i>
                                    <span>WhatsApp</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="contact-channel-grid-full">
                        <article class="contact-channel-full">
                            <span class="contact-channel-full__label">Linea diretta</span>
                            <a class="contact-channel-full__value" href="tel:+390810584542">+39 081 058 45 42</a>
                            <p class="contact-channel-full__meta">Lun-Ven · 08:30 — 19:15 (chiuso 13:15 — 16:00) · Sab 09:15 — 13:00</p>
                        </article>
                        <article class="contact-channel-full">
                            <span class="contact-channel-full__label">WhatsApp Business</span>
                            <a class="contact-channel-full__value" href="https://wa.me/393773798570" target="_blank" rel="noopener">+39 377 379 85 70</a>
                            <p class="contact-channel-full__meta">Ticket istantanei, file e vocali</p>
                        </article>
                        <article class="contact-channel-full">
                            <span class="contact-channel-full__label">Email operativa</span>
                            <a class="contact-channel-full__value" href="mailto:info@agenziaplinio.it">info@agenziaplinio.it</a>
                            <p class="contact-channel-full__meta">Monitoraggio continuo</p>
                        </article>
                        <article class="contact-channel-full">
                            <span class="contact-channel-full__label">PEC & Compliance</span>
                            <a class="contact-channel-full__value" href="mailto:agserviziviaplinio@sicurezzapostale.it">agserviziviaplinio@sicurezzapostale.it</a>
                            <p class="contact-channel-full__meta">Documenti ufficiali e procure</p>
                        </article>
                    </div>

                    <ul class="contact-sla-full">
                        <li>
                            <span class="contact-sla-full__value">24h</span>
                            <span class="contact-sla-full__label">presa in carico media</span>
                        </li>
                        <li>
                            <span class="contact-sla-full__value">2</span>
                            <span class="contact-sla-full__label">specialist dedicati</span>
                        </li>
                        <li>
                            <span class="contact-sla-full__value">3</span>
                            <span class="contact-sla-full__label">canali prioritari</span>
                        </li>
                    </ul>

                    <ul class="contact-meta-list-full">
                        <li>
                            <span class="contact-meta-list-full__label">Sede operativa</span>
                            <span class="contact-meta-list-full__value">Via Plinio Il Vecchio, 72 · 80053 Castellammare di Stabia (NA)</span>
                        </li>
                        <li>
                            <span class="contact-meta-list-full__label">Sportello clienti</span>
                            <span class="contact-meta-list-full__value">Lun-Ven 09:00 — 18:30 · Sab 09:00 — 13:00</span>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <!-- Mappa -->
                    <div class="map-container-full">
                        <iframe
                            src="https://www.openstreetmap.org/export/embed.html?bbox=14.4849641,40.6983611,14.4853168,40.7006381&layer=mapnik&marker=40.6994991,14.4851434"
                            width="100%"
                            height="300"
                            style="border-radius: 8px; border: 1px solid #e9ecef;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                        <p class="text-center mt-2 small text-muted">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            Via Plinio il Vecchio, 72 - Castellammare di Stabia (NA)
                        </p>
                        <div class="d-grid gap-2 mt-3">
                            <a href="https://maps.google.com/?q=Via+Plinio+Il+Vecchio+72,+80053+Castellammare+di+Stabia+NA" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">
                                <i class="fab fa-google me-1"></i>Apri in Google Maps
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Come Raggiungerci -->
        <div class="contact-details-full">
            <div class="row">
                <div class="col-lg-8">
                    <h2>Come raggiungerci</h2>
                    <p class="mb-4">Siamo facilmente raggiungibili da Napoli e dalla penisola sorrentina. Ecco tutte le indicazioni per arrivare in sede.</p>

                    <div class="directions-grid">
                        <div class="directions-item">
                            <h5><i class="fas fa-car me-2 text-primary"></i>In auto</h5>
                            <ul class="list-unstyled">
                                <li><strong>Dall'autostrada A3:</strong> Uscita Castellammare di Stabia, direzione centro città</li>
                                <li><strong>Da Napoli:</strong> SS145 Sorrentina, uscita Castellammare centro</li>
                                <li><strong>Parcheggio:</strong> Gratuito in zona, segnaletica stradale</li>
                            </ul>
                        </div>

                        <div class="directions-item">
                            <h5><i class="fas fa-bus me-2 text-primary"></i>Mezzi pubblici</h5>
                            <ul class="list-unstyled">
                                <li><strong>Bus urbano:</strong> Linee EAV dal porto commerciale, fermata Piazza Matteotti. Da lì, raggiungere Via Plinio Il Vecchio, 72 a piedi (circa 500 m).</li>
                                <li><strong>Treno:</strong> Stazione Castellammare di Stabia (linea Circumvesuviana)</li>
                                <li><strong>Aliscafi/Traghetti:</strong> Arrivo al porto commerciale di Castellammare di Stabia da isole (Capri, Ischia, Procida) o da Napoli/Salerno. Da lì, raggiungere Via Plinio Il Vecchio, 72 a piedi o con bus locali (circa 1 km).</li>
                            </ul>
                        </div>

                        <div class="directions-item">
                            <h5><i class="fas fa-clock me-2 text-primary"></i>Orari estesi</h5>
                            <ul class="list-unstyled">
                                <li><strong>Lunedì-Venerdì:</strong> 08:30-19:15 (pausa 13:15-16:00)</li>
                                <li><strong>Sabato:</strong> 09:15-13:00</li>
                                <li><strong>Domenica:</strong> Chiuso</li>
                                <li><strong>Festivi:</strong> Su appuntamento</li>
                            </ul>
                        </div>

                        <div class="directions-item">
                            <h5><i class="fas fa-info-circle me-2 text-primary"></i>Info utili</h5>
                            <ul class="list-unstyled">
                                <li><strong>Accessibilità:</strong> Ingresso a piano terra</li>
                                <li><strong>Sicurezza:</strong> Videosorveglianza 24/7</li>
                                <li><strong>Caffè:</strong> Bar nelle vicinanze</li>
                                <li><strong>WiFi:</strong> Gratuito per i clienti</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bg-light p-4 rounded">
                        <h5 class="mb-3">Distanze</h5>
                        <ul class="list-unstyled">
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span>Napoli Centro</span>
                                <strong>25 km</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span>Sorrento</span>
                                <strong>15 km</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span>Pompei</span>
                                <strong>12 km</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span>Salerno</span>
                                <strong>45 km</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2">
                                <span>Capri</span>
                                <strong>35 km</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form-full">
            <span class="eyebrow">Richiesta guidata</span>
            <h3>Pianifica una consulenza dedicata</h3>
            <p>Inviaci il perimetro del progetto: ti ricontattiamo con un piano operativo, checklist degli oneri e tempi di go-live.</p>
            <form method="post" data-contact-form novalidate>
                <input type="hidden" name="form_scope" value="contact">
                <input type="hidden" name="ap_token" value="<?php echo htmlspecialchars($formToken ?? '', ENT_QUOTES); ?>">
                <div class="honeypot">
                    <label for="company_website">Lascia vuoto</label>
                    <input type="text" id="company_website" name="company_website" tabindex="-1" autocomplete="off">
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label" for="name">Nome e cognome</label>
                        <input class="form-control" type="text" id="name" name="name" placeholder="Mario Rossi" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control" type="email" id="email" name="email" placeholder="nome@email.it" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="phone">Telefono</label>
                        <input class="form-control" type="tel" id="phone" name="phone" placeholder="+39">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="service">Servizio</label>
                        <select class="form-select" id="service" name="service">
                            <option value="pagamenti">Pagamenti</option>
                            <option value="ricariche">Ricariche</option>
                            <option value="digitali">Servizi digitali</option>
                            <option value="telefonia">Telefonia</option>
                            <option value="spedizioni">Spedizioni</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="company">Azienda / Ente</label>
                        <input class="form-control" type="text" id="company" name="company" placeholder="Ag Servizi S.r.l.">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="priority">Priorit&agrave;</label>
                        <select class="form-select" id="priority" name="priority">
                            <option value="standard">Standard (entro 24h)</option>
                            <option value="fast-track">Fast track (entro 8h)</option>
                            <option value="emergenza">Emergenza operativa</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="appointment_request">Tipo richiesta</label>
                        <select class="form-select" id="appointment_request" name="appointment_request">
                            <option value="contact">Contatto informativo</option>
                            <option value="consultation">Consulenza guidata</option>
                            <option value="appointment">Appuntamento in sede</option>
                            <option value="emergency">Emergenza operativa</option>
                        </select>
                    </div>
                    <div class="col-12 appointment-dates" style="display: none;">
                        <label class="form-label">Date disponibili per appuntamento</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input class="form-control" type="date" id="preferred_date" name="preferred_date" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                                <small class="text-muted">Data preferita</small>
                            </div>
                            <div class="col-md-6">
                                <select class="form-select" id="preferred_time" name="preferred_time">
                                    <option value="">Seleziona orario</option>
                                    <option value="09:00">09:00 - 10:00</option>
                                    <option value="10:00">10:00 - 11:00</option>
                                    <option value="11:00">11:00 - 12:00</option>
                                    <option value="14:00">14:00 - 15:00</option>
                                    <option value="15:00">15:00 - 16:00</option>
                                    <option value="16:00">16:00 - 17:00</option>
                                    <option value="17:00">17:00 - 18:00</option>
                                </select>
                                <small class="text-muted">Orario preferita (solo lunedì-venerdì)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="message">Messaggio</label>
                        <textarea class="form-control" id="message" name="message" rows="4" placeholder="Raccontaci cosa ti serve" required></textarea>
                    </div>
                </div>
                <div class="form-feedback" data-form-feedback aria-live="polite"></div>
                <div class="mt-4">
                    <p class="privacy-note">Inoltrando la richiesta accetti il trattamento dei dati ai sensi del Reg. UE 2016/679. Riceverai copia del ticket via email.</p>
                    <button class="ap-btn ap-btn--primary" type="submit" data-ripple="true">Invia richiesta</button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
// Appointment form toggle
document.getElementById('appointment_request').addEventListener('change', function() {
    const appointmentDates = document.querySelector('.appointment-dates');
    if (this.value === 'appointment') {
        appointmentDates.style.display = 'block';
        document.getElementById('preferred_date').required = true;
        document.getElementById('preferred_time').required = true;
    } else {
        appointmentDates.style.display = 'none';
        document.getElementById('preferred_date').required = false;
        document.getElementById('preferred_time').required = false;
    }
});

// Set minimum date to tomorrow
document.getElementById('preferred_date').min = new Date(Date.now() + 24 * 60 * 60 * 1000).toISOString().split('T')[0];

// Disable weekends in date picker
document.getElementById('preferred_date').addEventListener('input', function() {
    const selectedDate = new Date(this.value);
    const dayOfWeek = selectedDate.getDay();
    if (dayOfWeek === 0 || dayOfWeek === 6) { // Sunday = 0, Saturday = 6
        alert('Gli appuntamenti sono disponibili solo dal lunedì al venerdì.');
        this.value = '';
    }
});
</script>
