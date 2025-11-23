<?php
if (!function_exists('renderButton')) {
    function renderButton(string $label, array $options = []): string
    {
        $variant = $options['variant'] ?? 'primary';
        $size = $options['size'] ?? 'md';
        $href = $options['href'] ?? null;
        $attrs = $options['attrs'] ?? [];
        $asLink = !empty($href);
        $class = ['ap-btn', "ap-btn--{$variant}", "ap-btn--{$size}"];
        if (!empty($options['class'])) {
            $class[] = $options['class'];
        }
        $attrString = '';
        $attrs['data-ripple'] = 'true';
        $attrs['class'] = trim(implode(' ', $class));
        if ($asLink) {
            $attrs['href'] = $href;
        }
        foreach ($attrs as $key => $value) {
            $attrString .= sprintf('%s="%s" ', $key, htmlspecialchars((string)$value, ENT_QUOTES));
        }
        $tag = $asLink ? 'a' : 'button';
        $typeAttr = $asLink ? '' : 'type="button" ';
        return sprintf('<%1$s %2$s%3$s>%4$s</%1$s>', $tag, $typeAttr, $attrString, htmlspecialchars($label, ENT_QUOTES));
    }
}
