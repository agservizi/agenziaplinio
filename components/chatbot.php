<?php
$hasFaqs = !empty($chatbotFaqs);
$categories = [];
if ($hasFaqs) {
    foreach ($chatbotFaqs as $faq) {
        $categories[$faq['category_slug']] = $faq['category'];
    }
}
$featuredFaqs = $hasFaqs ? array_slice($chatbotFaqs, 0, 4) : [];
$isLoggedIn = !empty(ap_auth_current_user());
?>
<div class="ap-chatbot" data-chatbot data-logged-in="<?php echo $isLoggedIn ? 'true' : 'false'; ?>">
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
            <nav class="ap-chatbot__tabs" role="tablist">
                <button type="button" class="ap-chatbot__tab is-active" data-chatbot-tab="chat" aria-selected="true">Chat</button>
                <?php if ($hasFaqs): ?>
                <button type="button" class="ap-chatbot__tab" data-chatbot-tab="faq" aria-selected="false">FAQ</button>
                <?php endif; ?>
            </nav>
            <button type="button" class="ap-chatbot__close" data-chatbot-close aria-label="Chiudi chatbot">×</button>
        </header>
        <div class="ap-chatbot__body">
            <div class="ap-chatbot__tab-content" data-chatbot-tab-content="chat">
                <div class="ap-chatbot__messages" data-chatbot-messages role="log" aria-live="polite">
                    <div class="ap-chatbot__message ap-chatbot__message--bot">
                        <p>Ciao sono Plinio! Il chatbot di AG SERVIZI VIA PLINIO 72. Come posso aiutarti con pagamenti, ricariche, SPID, PEC o altri servizi?</p>
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
            <div class="ap-chatbot__tab-content" data-chatbot-tab-content="faq" hidden>
                <?php if ($hasFaqs): ?>
                    <div class="ap-chatbot__intro">
                        <p>Sfoglia le domande frequenti per trovare rapidamente le risposte che cerchi.</p>
                    </div>
                    <div class="ap-chatbot__shortcuts" role="group" aria-label="Domande popolari">
                        <?php foreach ($featuredFaqs as $faq): ?>
                            <button type="button" class="ap-chatbot__shortcut" data-chatbot-question="<?php echo htmlspecialchars($faq['question'], ENT_QUOTES); ?>">
                                <?php echo htmlspecialchars($faq['question']); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
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
            </div>
        </div>
    </section>
</div>
