<?php
if (!function_exists('renderServiceCard')) {
    function renderServiceCard(array $service): string
    {
        $title = $service['title'] ?? '';
        $description = $service['description'] ?? '';
        $icon = $service['icon'] ?? '';
        $key = $service['key'] ?? '';
        $attributes = $key ? sprintf(' data-service-key="%s"', htmlspecialchars($key, ENT_QUOTES)) : '';

        return sprintf(
            '<article class="service-card" data-reveal %4$s>
                <div class="icon-circle">
                    <span class="icon">%1$s</span>
                </div>
                <div class="service-content">
                    <h5>%2$s</h5>
                    <p>%3$s</p>
                    %5$s
                </div>
            </article>',
            htmlspecialchars($icon, ENT_QUOTES),
            htmlspecialchars($title, ENT_QUOTES),
            htmlspecialchars($description, ENT_QUOTES),
            $attributes,
            $key ? '<button class="service-more" type="button" data-ripple="true">Dettagli</button>' : ''
        );
    }
}
