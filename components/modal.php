<?php
if (!function_exists('renderModal')) {
    function renderModal(string $id, array $content): string
    {
        $title = $content['title'] ?? '';
        $body = $content['body'] ?? '';
        return sprintf(
            '<div id="%1$s" class="ap-modal" aria-hidden="true">
                <div class="ap-modal__overlay" data-modal-close></div>
                <div class="ap-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="%1$s-title">
                    <button type="button" class="ap-modal__close" data-modal-close>&times;</button>
                    <div class="ap-modal__content">
                        <h4 id="%1$s-title">%2$s</h4>
                        <p>%3$s</p>
                    </div>
                </div>
            </div>',
            htmlspecialchars($id, ENT_QUOTES),
            htmlspecialchars($title, ENT_QUOTES),
            htmlspecialchars($body, ENT_QUOTES)
        );
    }
}
