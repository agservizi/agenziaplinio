<section class="contact-section section-padding" id="contatti" aria-labelledby="contatti-title">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5" data-reveal>
                <div class="contact-details glass-panel">
                    <span class="contact-details__badge">Hub operativo · Castellammare di Stabia</span>
                    <h2 id="contatti-title">Parliamo in modo operativo</h2>
                    <p class="contact-details__intro">Team operations attivo 7/7 per onboarding multiservizio, attivazioni digitali e supporto post vendita. Rispondiamo in media entro 43 minuti.</p>

                    <div class="contact-channel-grid">
                        <article class="contact-channel">
                            <span class="contact-channel__label">Linea diretta</span>
                            <a class="contact-channel__value" href="tel:+390810584542">+39 081 058 45 42</a>
                            <p class="contact-channel__meta">Lun-Ven · 08:30 — 19:15 (chiuso 13:15 — 16:00) · Sab 09:15 — 13:00</p>
                        </article>
                        <article class="contact-channel">
                            <span class="contact-channel__label">WhatsApp Business</span>
                            <a class="contact-channel__value" href="https://wa.me/393773798570" target="_blank" rel="noopener">+39 377 379 85 70</a>
                            <p class="contact-channel__meta">Ticket istantanei, file e vocali</p>
                        </article>
                        <article class="contact-channel">
                            <span class="contact-channel__label">Email operativa</span>
                            <a class="contact-channel__value" href="mailto:info@agenziaplinio.it">info@agenziaplinio.it</a>
                            <p class="contact-channel__meta">Monitoraggio continuo</p>
                        </article>
                        <article class="contact-channel">
                            <span class="contact-channel__label">PEC & Compliance</span>
                            <a class="contact-channel__value" href="mailto:agserviziviaplinio@sicurezzapostale.it">agserviziviaplinio@sicurezzapostale.it</a>
                            <p class="contact-channel__meta">Documenti ufficiali e procure</p>
                        </article>
                    </div>

                    <ul class="contact-sla list-unstyled">
                        <li>
                            <span class="contact-sla__value">24h</span>
                            <span class="contact-sla__label">presa in carico media</span>
                        </li>
                        <li>
                            <span class="contact-sla__value">12</span>
                            <span class="contact-sla__label">specialist dedicati</span>
                        </li>
                        <li>
                            <span class="contact-sla__value">3</span>
                            <span class="contact-sla__label">canali prioritari</span>
                        </li>
                    </ul>

                    <ul class="contact-meta-list list-unstyled">
                        <li>
                            <span class="contact-meta-list__label">Sede operativa</span>
                            <span class="contact-meta-list__value">Via Plinio Il Vecchio, 72 · 80053 Castellammare di Stabia (NA)</span>
                        </li>
                        <li>
                            <span class="contact-meta-list__label">Sportello clienti</span>
                            <span class="contact-meta-list__value">Lun-Ven 09:00 — 18:30 · Sab 09:00 — 13:00</span>
                        </li>
                    </ul>
                    <p class="contact-details__note">Su richiesta organizziamo call Google Meet o visite onsite per network e franchising.</p>
                </div>

            </div>
            <div class="col-lg-7" data-reveal="fade-left">
                <form class="contact-form glass-panel" method="post" data-contact-form novalidate>
                    <input type="hidden" name="form_scope" value="contact">
                    <input type="hidden" name="ap_token" value="<?php echo htmlspecialchars($formToken ?? '', ENT_QUOTES); ?>">
                    <div class="honeypot">
                        <label for="company_website">Lascia vuoto</label>
                        <input type="text" id="company_website" name="company_website" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="contact-form__header">
                        <span class="eyebrow">Richiesta guidata</span>
                        <h3>Pianifica una consulenza dedicata</h3>
                        <p>Inviaci il perimetro del progetto: ti ricontattiamo con un piano operativo, checklist degli oneri e tempi di go-live.</p>
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
                    <div class="contact-form__footer">
                        <p class="privacy-note">Inoltrando la richiesta accetti il trattamento dei dati ai sensi del Reg. UE 2016/679. Riceverai copia del ticket via email.</p>
                        <button class="ap-btn ap-btn--primary" type="submit" data-ripple="true">Invia richiesta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
