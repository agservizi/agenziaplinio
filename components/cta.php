<?php
require_once __DIR__ . '/button.php';

if (!function_exists('renderCTAGroup')) {
    function renderCTAGroup(array $primary, array $secondary = []): string
    {
        $primaryButton = renderButton($primary['label'] ?? 'Scopri', [
            'variant' => $primary['variant'] ?? 'primary',
            'href' => $primary['href'] ?? '#',
            'class' => 'cta-primary',
            'attrs' => ['data-scroll' => 'true']
        ]);
        $secondaryButton = '';
        if (!empty($secondary)) {
            $secondaryButton = renderButton($secondary['label'] ?? 'Contattaci', [
                'variant' => $secondary['variant'] ?? 'ghost',
                'href' => $secondary['href'] ?? '#',
                'class' => 'cta-secondary',
                'attrs' => ['data-scroll' => 'true']
            ]);
        }
        return sprintf('<div class="cta-group">%s%s</div>', $primaryButton, $secondaryButton);
    }
}
