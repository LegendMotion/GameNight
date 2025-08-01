<?php
header('Content-Type: application/xml');
$scheme = $_SERVER['REQUEST_SCHEME'] ?? 'https';
$host = $_SERVER['HTTP_HOST'] ?? 'example.com';
$base = $scheme . '://' . $host;

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Blog posts from database
try {
    require_once __DIR__ . '/api/db.php';
    $stmt = $pdo->query("SELECT slug, created_at FROM posts WHERE visibility = 'public'");
    foreach ($stmt as $row) {
        $loc = htmlspecialchars($base . '/blog/post.php?slug=' . urlencode($row['slug']), ENT_XML1);
        $lastmod = htmlspecialchars(date('c', strtotime($row['created_at'])), ENT_XML1);
        echo "<url><loc>{$loc}</loc><lastmod>{$lastmod}</lastmod></url>";
    }
} catch (Throwable $e) {
    // ignore database errors
}

// Collections from JSON files
foreach (glob(__DIR__ . '/data/collections/*.json') as $file) {
    $json = json_decode(file_get_contents($file), true);
    if (!is_array($json) || !($json['public'] ?? false)) continue;
    $name = basename($file);
    $loc = htmlspecialchars($base . '/data/collections/' . rawurlencode($name), ENT_XML1);
    $lastmod = htmlspecialchars(date('c', filemtime($file)), ENT_XML1);
    echo "<url><loc>{$loc}</loc><lastmod>{$lastmod}</lastmod></url>";
}

echo '</urlset>';
