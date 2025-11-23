<?php
$hasFaqs = !empty($chatbotFaqs);
$categories = [];
if ($hasFaqs) {
    foreach ($chatbotFaqs as $faq) {
        $categories[$faq['category_slug']] = $faq['category'];
    }
}
$featuredFaqs = $hasFaqs ? array_slice($chatbotFaqs, 0, 4) : [];
?>
<div class="ap-chatbot" data-chatbot>
    <button class="ap-chatbot__toggle" type="button" data-chatbot-toggle aria-expanded="false" aria-controls="ap-chatbot-panel">
        <span class="ap-chatbot__toggle-icon" aria-hidden="true">?</span>
        <span class="ap-chatbot__toggle-copy">
            <strong>Serve aiuto?</strong>
            <small>Chatta con Plinio</small>
        </span>
    </button>
    <section id="ap-chatbot-panel" class="ap-chatbot__panel" aria-hidden="true" aria-live="polite">
        <header class="ap-chatbot__header">
            <div>
                <p class="ap-chatbot__eyebrow">Assistente virtuale</p>
                <h5>FAQ chatbot</h5>
            </div>
            <button type="button" class="ap-chatbot__close" data-chatbot-close aria-label="Chiudi chatbot">×</button>
        </header>
        <div class="ap-chatbot__body">
            <?php if ($hasFaqs): ?>
                <div class="ap-chatbot__filters" role="group" aria-label="Filtra per categoria">
                    <button type="button" class="is-active" data-chatbot-filter="all">Tutte</button>
                    <?php foreach ($categories as $slug => $label): ?>
                        <button type="button" data-chatbot-filter="<?php echo htmlspecialchars($slug, ENT_QUOTES); ?>">
                            <?php echo htmlspecialchars($label); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <div class="ap-chatbot__faq-list" data-chatbot-faq-list>
                    <?php foreach ($chatbotFaqs as $faq): ?>
                        <button
                            type="button"
                            class="ap-chatbot__faq-pill"
                            data-chatbot-faq
                            data-category="<?php echo htmlspecialchars($faq['category_slug'], ENT_QUOTES); ?>"
                            data-question="<?php echo htmlspecialchars($faq['question'], ENT_QUOTES); ?>"
                        >
                            <span><?php echo htmlspecialchars($faq['question']); ?></span>
                            <small><?php echo htmlspecialchars($faq['category']); ?></small>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="ap-chatbot__messages" data-chatbot-messages role="log" aria-live="polite">
                <div class="ap-chatbot__message ap-chatbot__message--bot">
                    <p>Ciao sono Plinio! Il chatbot di AG SERVIZI VIA PLINIO 72. Come posso aiutarti oggi?</p>
                </div>
            </div>
            <form class="ap-chatbot__form" data-chatbot-form autocomplete="off">
                <label class="visually-hidden" for="ap-chatbot-input">Scrivi la tua domanda</label>
                <input
                    type="text"
                    id="ap-chatbot-input"
                    name="chatbot_question"
                    placeholder="Scrivi una domanda..."
                    data-chatbot-input
                    <?php echo $hasFaqs ? '' : 'disabled'; ?>
                >
                <button type="submit" <?php echo $hasFaqs ? '' : 'disabled'; ?>>Invia</button>
            </form>
        </div>
    </section>
</div>
