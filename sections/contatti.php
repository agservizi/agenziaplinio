<?php
/**
 * Pagina Contatti
 */
?>
<style>
.contact-hero {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 3rem;
    text-align: center;
    margin-bottom: 3rem;
}

.contact-hero h1 {
    color: #007bff;
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.contact-hero p {
    font-size: 1.2rem;
    color: #6c757d;
    max-width: 600px;
    margin: 0 auto;
}

.contact-details-full {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    padding: 2.5rem;
    margin-bottom: 3rem;
}

.contact-channel-grid-full {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.contact-channel-full {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    border-left: 4px solid #007bff;
}

.contact-channel-full__label {
    font-weight: 600;
    color: #495057;
    display: block;
    margin-bottom: 0.5rem;
}

.contact-channel-full__value {
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
    display: block;
    margin-bottom: 0.5rem;
}

.contact-channel-full__value:hover {
    text-decoration: underline;
}

.contact-channel-full__meta {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0;
}

.contact-sla-full {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.contact-sla-full li {
    text-align: center;
    padding: 1rem;
    background: #e9ecef;
    border-radius: 8px;
}

.contact-sla-full__value {
    font-size: 2rem;
    font-weight: 700;
    color: #007bff;
    display: block;
}

.contact-sla-full__label {
    font-size: 0.875rem;
    color: #495057;
}

.contact-meta-list-full {
    list-style: none;
    padding: 0;
    margin-bottom: 2rem;
}

.contact-meta-list-full li {
    padding: 0.75rem 0;
    border-bottom: 1px solid #e9ecef;
}

.contact-meta-list-full__label {
    font-weight: 600;
    color: #495057;
    display: block;
}

.contact-meta-list-full__value {
    color: #6c757d;
    margin-top: 0.25rem;
}

.map-container-full {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    margin-bottom: 3rem;
}

.map-container-full iframe {
    border-radius: 8px;
}

.contact-form-full {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    padding: 2.5rem;
}

.contact-form-full .eyebrow {
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.contact-form-full h3 {
    color: #495057;
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.contact-form-full p {
    color: #6c757d;
    margin-bottom: 2rem;
}

.form-feedback {
    margin-top: 1rem;
}

.privacy-note {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 1.5rem;
}

.ap-btn {
    background: #007bff;
    color: white;
    border: none;
    border-radius: 25px;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.2s ease;
}

.ap-btn:hover {
    background: #0056b3;
    transform: translateY(-1px);
}
</style>

<section class="section-padding bg-light">
    <div class="container">
        <!-- Hero Section -->
        <div class="contact-hero">
            <h1><i class="fas fa-envelope me-3"></i>Contatti</h1>
            <p>Team operations attivo 7/7 per supporto, consulenze e attivazioni. Rispondiamo in media entro 43 minuti.</p>
        </div>

        <!-- Contact Details -->
        <div class="contact-details-full">
            <div class="row">
                <div class="col-lg-8">
                    <h2>Parliamo in modo operativo</h2>
                    <p class="mb-4">Team operations attivo 7/7 per onboarding multiservizio, attivazioni digitali e supporto post vendita. Rispondiamo in media entro 43 minuti.</p>

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
                            <span class="contact-sla-full__value">12</span>
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
