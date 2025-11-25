<?php
/**
 * Sezione Testimonials
 */
$testimonials = [
    [
        'name' => 'Maria Rossi',
        'role' => 'Imprenditrice',
        'company' => 'Rossi S.r.l.',
        'content' => 'Servizio eccellente per la costituzione della mia società. Tutto gestito in meno di 24 ore con assistenza passo-passo. Altamente raccomandato!',
        'rating' => 5
    ],
    [
        'name' => 'Giuseppe Verdi',
        'role' => 'Commerciante',
        'company' => 'Verdi Commerciale',
        'content' => 'Pagamenti certificati rapidi e sicuri. Ho risolto tutti i miei bollettini in un giorno solo. Supporto clienti sempre disponibile.',
        'rating' => 5
    ],
    [
        'name' => 'Anna Bianchi',
        'role' => 'Professionista',
        'company' => 'Studio Bianchi',
        'content' => 'SPID e firma digitale attivati in meno di 20 minuti. Processo guidato perfetto per chi non è esperto di tecnologia.',
        'rating' => 5
    ]
];
?>
<section id="testimonials" class="testimonials-section section-padding bg-light">
    <div class="container">
        <div class="section-heading text-center" data-reveal="fade-up">
            <p class="eyebrow">Cosa dicono i nostri clienti</p>
            <h2>Esperienze reali, fiducia dimostrata</h2>
            <p class="text-muted">Migliaia di clienti soddisfatti scelgono Agenzia Plinio per i loro servizi quotidiani.</p>
        </div>
        <div class="row g-4 mt-4">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="testimonial-card h-100" data-reveal="fade-up">
                        <div class="testimonial-card__rating mb-3">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= $testimonial['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <blockquote class="testimonial-card__quote mb-4">
                            "<?php echo htmlspecialchars($testimonial['content'], ENT_QUOTES); ?>"
                        </blockquote>
                        <div class="testimonial-card__author d-flex align-items-center">
                            <div class="testimonial-card__avatar me-3">
                                <?php echo strtoupper(substr($testimonial['name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h6 class="mb-0"><?php echo htmlspecialchars($testimonial['name'], ENT_QUOTES); ?></h6>
                                <small class="text-muted"><?php echo htmlspecialchars($testimonial['role'], ENT_QUOTES); ?> - <?php echo htmlspecialchars($testimonial['company'], ENT_QUOTES); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5" data-reveal="fade-up">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">12k+</div>
                    <div class="stat-label">Clienti soddisfatti</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">4.9/5</div>
                    <div class="stat-label">Valutazione media</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">99.5%</div>
                    <div class="stat-label">Tasso di risoluzione</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24h</div>
                    <div class="stat-label">Risposta garantita</div>
                </div>
            </div>
        </div>
    </div>
</section>