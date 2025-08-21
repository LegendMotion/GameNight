<?php
function get_locale(): string {
    if (!empty($_COOKIE['locale'])) {
        return preg_replace('/[^a-z]/', '', $_COOKIE['locale']);
    }
    $header = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    if (stripos($header, 'no') === 0) {
        return 'no';
    }
    return 'en';
}

function load_translations(): array {
    static $translations = null;
    if ($translations === null) {
        $locale = get_locale();
        $file = __DIR__ . '/../locales/' . $locale . '.json';
        if (!file_exists($file)) {
            $file = __DIR__ . '/../locales/en.json';
        }
        $json = file_get_contents($file);
        $translations = json_decode($json, true) ?? [];
    }
    return $translations;
}

function t(string $key): string {
    $translations = load_translations();
    return $translations[$key] ?? $key;
}
?>
