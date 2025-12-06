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
<section id="testimonials" class="ap-section ap-section--stories ap-section--widescreen" data-theme="night">
    <?php $heroStory = $testimonials[0]; ?>
    <div class="ap-stories__hero">
        <div class="ap-immersive-media" data-parallax-container>
            <video class="ap-immersive-media__video" autoplay muted loop playsinline poster="/assets/img/hero-poster.jpg">
                <source src="https://cdn.coverr.co/videos/coverr-futuristic-lights-6432/1080p.mp4" type="video/mp4">
            </video>
            <div class="ap-immersive-media__overlay"></div>
        </div>
        <div class="ap-stories__hero-content container">
            <div class="ap-stories__hero-copy" data-reveal="fade-up">
                <span class="ap-eyebrow">Customer stories</span>
                <h1 data-reveal-text>Le storie dei nostri clienti parlano per noi</h1>
                <p>Attiviamo pod dedicati che orchestrano onboarding, compliance e consegne in meno di 48h grazie a workflow trasparenti e KPI condivisi.</p>
                <div class="ap-stories__hero-cta">
                    <a class="ap-btn ap-btn--primary" href="?page=contatti">Apri una cabina di regia</a>
                    <button class="ap-btn ap-btn--ghost" type="button" data-modal-open="service-modal">Guarda tutte le reference</button>
                </div>
            </div>
            <aside class="ap-stories__hero-card" data-reveal="fade-up" data-reveal-delay="120">
                <div class="ap-chip ap-chip--glow">Case study espresso</div>
                <p class="ap-quote">“<?php echo htmlspecialchars($heroStory['content'], ENT_QUOTES); ?>”</p>
                <div class="ap-quote__author">
                    <div>
                        <strong><?php echo htmlspecialchars($heroStory['name'], ENT_QUOTES); ?></strong>
                        <small><?php echo htmlspecialchars($heroStory['role'], ENT_QUOTES); ?> · <?php echo htmlspecialchars($heroStory['company'], ENT_QUOTES); ?></small>
                    </div>
                    <span>5/5 rating</span>
                </div>
                <ul class="ap-quote__timeline">
                    <li>Kick-off</li>
                    <li>Workshop remoto</li>
                    <li>Go-live 48h</li>
                </ul>
            </aside>
        </div>
    </div>

    <div class="container">
        <div class="ap-stories__carousel" data-carousel>
            <button class="ap-carousel__nav" type="button" data-carousel-prev aria-label="Testimonianza precedente">
                <i class="fas fa-arrow-left"></i>
            </button>
            <div class="ap-stories__track" data-carousel-track>
                <?php foreach ($testimonials as $testimonial): ?>
                    <article class="ap-story-card" data-motion="slide">
                        <div class="ap-story-card__avatar">
                            <span><?php echo strtoupper(substr($testimonial['name'], 0, 1)); ?></span>
                        </div>
                        <blockquote>
                            <p><?php echo htmlspecialchars($testimonial['content'], ENT_QUOTES); ?></p>
                        </blockquote>
                        <footer>
                            <div>
                                <strong><?php echo htmlspecialchars($testimonial['name'], ENT_QUOTES); ?></strong>
                                <small><?php echo htmlspecialchars($testimonial['role'], ENT_QUOTES); ?> · <?php echo htmlspecialchars($testimonial['company'], ENT_QUOTES); ?></small>
                            </div>
                            <span class="ap-rating-pill">
                                <i class="fas fa-star"></i>
                                <?php echo number_format((float) $testimonial['rating'], 1); ?>
                            </span>
                        </footer>
                    </article>
                <?php endforeach; ?>
            </div>
            <button class="ap-carousel__nav" type="button" data-carousel-next aria-label="Testimonianza successiva">
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>

        <div class="ap-stories__metrics" data-reveal>
            <article>
                <strong>12k+</strong>
                <span>clienti attivi</span>
            </article>
            <article>
                <strong>4.9/5</strong>
                <span>customer satisfaction</span>
            </article>
            <article>
                <strong>99.5%</strong>
                <span>ticket risolti first touch</span>
            </article>
            <article>
                <strong>24h</strong>
                <span>presa in carico garantita</span>
            </article>
        </div>
    </div>
</section>