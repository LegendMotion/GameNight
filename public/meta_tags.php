<?php
require_once __DIR__ . '/api/db.php';

function get_setting(PDO $pdo, string $name, string $default = ''): string {
    $stmt = $pdo->prepare('SELECT value FROM settings WHERE name = ?');
    $stmt->execute([$name]);
    $value = $stmt->fetchColumn();
    return $value !== false ? $value : $default;
}

$siteName = get_setting($pdo, 'general_site_name', 'GameNight');
$description = get_setting($pdo, 'seo_meta_description');
$keywords = get_setting($pdo, 'seo_meta_keywords');
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$url = $scheme . $host . $uri;

if ($description !== '') {
    echo '<meta name="description" content="' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '">' . PHP_EOL;
}
if ($keywords !== '') {
    echo '<meta name="keywords" content="' . htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8') . '">' . PHP_EOL;
}
$escapedTitle = htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8');
$escapedDescription = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
$escapedUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

echo '<meta property="og:title" content="' . $escapedTitle . '">' . PHP_EOL;
if ($description !== '') {
    echo '<meta property="og:description" content="' . $escapedDescription . '">' . PHP_EOL;
}
echo '<meta property="og:url" content="' . $escapedUrl . '">' . PHP_EOL;
echo '<meta property="og:type" content="website">' . PHP_EOL;

echo '<meta name="twitter:card" content="summary">' . PHP_EOL;
echo '<meta name="twitter:title" content="' . $escapedTitle . '">' . PHP_EOL;
if ($description !== '') {
    echo '<meta name="twitter:description" content="' . $escapedDescription . '">' . PHP_EOL;
}
?>
