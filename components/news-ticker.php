<?php
if (empty($newsTickerItems)) {
    return;
}
?>
<div class="news-ticker" role="region" aria-label="Aggiornamenti Agenzia Plinio">
    <div class="news-ticker__inner">
        <?php foreach ([$newsTickerItems, $newsTickerItems] as $loop): ?>
            <?php foreach ($loop as $entry): ?>
                <span class="news-ticker__item">
                    <?php echo ap_format_announcement_message((string) ($entry['message'] ?? '')); ?>
                </span>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
</div>
