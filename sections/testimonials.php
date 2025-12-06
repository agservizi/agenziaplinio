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
<section id="testimonials" class="ap-section ap-section--tints">
    <div class="container">
        <div class="ap-section__heading" data-reveal="fade-up">
            <span class="ap-eyebrow">Customer stories</span>
            <h2>Miglioriamo processi e daily operations in tempi rapidi</h2>
            <p>Ogni giorno supportiamo PMI, professionisti e PA con onboarding assistito, task force dedicate e metriche trasparenti.</p>
        </div>

        <div class="ap-grid ap-grid--cards mt-5">
            <?php foreach ($testimonials as $testimonial): ?>
                <article class="ap-tile ap-tile--quote" data-reveal="fade-up">
                    <div class="ap-tile__rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?php echo $i <= $testimonial['rating'] ? 'is-active' : ''; ?>" aria-hidden="true"></i>
                        <?php endfor; ?>
                        <span class="visually-hidden">Valutazione <?php echo (int) $testimonial['rating']; ?> su 5</span>
                    </div>
                    <blockquote>
                        <p><?php echo htmlspecialchars($testimonial['content'], ENT_QUOTES); ?></p>
                    </blockquote>
                    <footer>
                        <div class="ap-avatar">
                            <span><?php echo strtoupper(substr($testimonial['name'], 0, 1)); ?></span>
                        </div>
                        <div>
                            <strong><?php echo htmlspecialchars($testimonial['name'], ENT_QUOTES); ?></strong>
                            <small><?php echo htmlspecialchars($testimonial['role'], ENT_QUOTES); ?> · <?php echo htmlspecialchars($testimonial['company'], ENT_QUOTES); ?></small>
                        </div>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="ap-metrics mt-5" data-reveal="fade-up">
            <div class="ap-metric">
                <span class="ap-metric__value">12k+</span>
                <span class="ap-metric__label">clienti gestiti</span>
            </div>
            <div class="ap-metric">
                <span class="ap-metric__value">4.9/5</span>
                <span class="ap-metric__label">customer score</span>
            </div>
            <div class="ap-metric">
                <span class="ap-metric__value">99.5%</span>
                <span class="ap-metric__label">ticket risolti</span>
            </div>
            <div class="ap-metric">
                <span class="ap-metric__value">24h</span>
                <span class="ap-metric__label">presa in carico</span>
            </div>
        </div>
    </div>
</section>