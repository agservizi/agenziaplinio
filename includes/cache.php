<?php
declare(strict_types=1);

function ap_cache_dir(): string
{
    static $dir = null;
    if ($dir !== null) {
        return $dir;
    }
    $dir = __DIR__ . '/../storage/cache';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    return $dir;
}

function ap_cache_normalize_key(string $key): string
{
    $normalized = preg_replace('/[^a-z0-9\-_.:]/i', '_', $key) ?? '';
    $normalized = trim($normalized, '_');
    if ($normalized === '') {
        $normalized = 'cache_' . md5($key);
    }
    return $normalized;
}

function ap_cache_path(string $key): string
{
    return rtrim(ap_cache_dir(), '/\\') . '/' . ap_cache_normalize_key($key) . '.cache.php';
}

function ap_cache_get(string $key, int $ttl = 300)
{
    if ($ttl <= 0) {
        return null;
    }
    $path = ap_cache_path($key);
    if (!is_file($path)) {
        return null;
    }
    $payload = @file_get_contents($path);
    if ($payload === false) {
        return null;
    }
    $data = @unserialize($payload, ['allowed_classes' => false]);
    if (!is_array($data) || !isset($data['expires'], $data['value'])) {
        @unlink($path);
        return null;
    }
    if ($data['expires'] < time()) {
        @unlink($path);
        return null;
    }
    return $data['value'];
}

function ap_cache_set(string $key, $value, int $ttl = 300): void
{
    if ($ttl <= 0) {
        ap_cache_forget($key);
        return;
    }
    $payload = serialize([
        'expires' => time() + $ttl,
        'value' => $value,
    ]);
    @file_put_contents(ap_cache_path($key), $payload, LOCK_EX);
}

function ap_cache_forget(string $key): void
{
    $path = ap_cache_path($key);
    if (is_file($path)) {
        @unlink($path);
    }
}

function ap_cache_forget_prefix(string $prefix): void
{
    $dir = ap_cache_dir();
    $normalized = ap_cache_normalize_key($prefix);
    foreach (glob($dir . '/' . $normalized . '*.cache.php') ?: [] as $file) {
        @unlink($file);
    }
}
