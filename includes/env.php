<?php
declare(strict_types=1);

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}

if (!function_exists('ap_load_env')) {
    function ap_load_env(string $path): void
    {
        static $loaded = false;
        if ($loaded || !is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || strpos($trimmed, '#') === 0) {
                continue;
            }
            if (!str_contains($line, '=')) {
                continue;
            }
            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if ($value !== '' && ($value[0] === '"' && substr($value, -1) === '"')) {
                $value = substr($value, 1, -1);
            }
            putenv("{$name}={$value}");
            $_ENV[$name] = $value;
        }

        $loaded = true;
    }
}

if (!function_exists('ap_env')) {
    function ap_env(string $key, ?string $default = null): ?string
    {
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        return $value;
    }
}
