<?php
header('Content-Type: text/plain');
$default = "User-agent: *\nAllow: /\nSitemap: /sitemap.xml\n";
$content = $default;
try {
    require_once __DIR__ . '/api/db.php';
    $stmt = $pdo->prepare('SELECT value FROM settings WHERE name = ? LIMIT 1');
    $stmt->execute(['seo_robots_txt']);
    $val = $stmt->fetchColumn();
    if ($val !== false && trim($val) !== '') {
        $content = $val;
    }
} catch (Throwable $e) {
    // ignore errors
}
echo $content;
