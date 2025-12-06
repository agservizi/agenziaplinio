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
<section id="testimonials" class="ap-section ap-section--stories">
    <div class="container">
        <?php $heroStory = $testimonials[0]; ?>
        <div class="ap-stories__hero" data-reveal="fade-up">
            <div class="ap-stories__copy">
                <span class="ap-eyebrow">Customer stories</span>
                <h2>Delivery sprint, KPI pubblici e team umano che risponde in meno di 1h</h2>
                <p>Ogni attivazione viene seguita da un pod dedicato che tiene allineati clienti e partner con dashboard condivise, video brief e recap vocali.</p>
                <ul class="ap-stories__highlights">
                    <li><strong>Playbook personalizzati</strong> con checklist operative e template pronti</li>
                    <li><strong>Accountability end-to-end</strong> su ticket, SLA e metriche di qualità</li>
                    <li><strong>Storytelling trasparente</strong> grazie a report vocali e note contestuali</li>
                </ul>
                <div class="ap-stories__cta">
                    <a class="ap-btn ap-btn--primary" href="?page=contatti">Prenota uno sprint</a>
                    <button class="ap-btn ap-btn--text" type="button" data-modal-open="service-modal">Guarda referenze</button>
                </div>
            </div>
            <aside class="ap-stories__media">
                <div class="ap-story-card">
                    <div class="ap-story-card__badge">Case study espresso</div>
                    <p class="ap-story-card__quote">“<?php echo htmlspecialchars($heroStory['content'], ENT_QUOTES); ?>”</p>
                    <div class="ap-story-card__author">
                        <div>
                            <strong><?php echo htmlspecialchars($heroStory['name'], ENT_QUOTES); ?></strong>
                            <small><?php echo htmlspecialchars($heroStory['role'], ENT_QUOTES); ?> · <?php echo htmlspecialchars($heroStory['company'], ENT_QUOTES); ?></small>
                        </div>
                        <span>Valutazione 5/5</span>
                    </div>
                    <div class="ap-story-card__timeline">
                        <span>Kick-off</span>
                        <span>Revisione contratti</span>
                        <span>Go-live 48h</span>
                    </div>
                </div>
            </aside>
        </div>

        <div class="ap-testimonials-grid" data-reveal="fade-up">
            <?php foreach ($testimonials as $index => $testimonial): ?>
                <article class="ap-testimonial <?php echo $index === 0 ? 'is-active' : ''; ?>">
                    <header>
                        <div class="ap-avatar">
                            <span><?php echo strtoupper(substr($testimonial['name'], 0, 1)); ?></span>
                        </div>
                        <div>
                            <h3><?php echo htmlspecialchars($testimonial['name'], ENT_QUOTES); ?></h3>
                            <p><?php echo htmlspecialchars($testimonial['role'], ENT_QUOTES); ?> · <?php echo htmlspecialchars($testimonial['company'], ENT_QUOTES); ?></p>
                        </div>
                        <span class="ap-rating-pill">
                            <i class="fas fa-star"></i>
                            <?php echo number_format((float) $testimonial['rating'], 1); ?>
                        </span>
                    </header>
                    <p class="ap-testimonial__body"><?php echo htmlspecialchars($testimonial['content'], ENT_QUOTES); ?></p>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="ap-stats" data-reveal="fade-up">
            <div>
                <span>12k+</span>
                <p>clienti gestiti con contratti attivi</p>
            </div>
            <div>
                <span>4.9/5</span>
                <p>valutazione media customer care</p>
            </div>
            <div>
                <span>99.5%</span>
                <p>ticket risolti al primo contatto</p>
            </div>
            <div>
                <span>24h</span>
                <p>presa in carico garantita</p>
            </div>
        </div>
    </div>
</section>