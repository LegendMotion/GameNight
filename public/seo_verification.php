<?php
require_once __DIR__ . '/api/db.php';

$tokens = [];
$stmt = $pdo->prepare('SELECT name, value FROM settings WHERE name IN (?, ?)');
$stmt->execute(['seo_google_verification', 'seo_bing_verification']);
foreach ($stmt as $row) {
    $tokens[$row['name']] = $row['value'];
}

$google = trim($tokens['seo_google_verification'] ?? '');
$bing = trim($tokens['seo_bing_verification'] ?? '');

if ($google !== '') {
    echo '<meta name="google-site-verification" content="' . htmlspecialchars($google, ENT_QUOTES) . '">' . PHP_EOL;
}
if ($bing !== '') {
    echo '<meta name="msvalidate.01" content="' . htmlspecialchars($bing, ENT_QUOTES) . '">' . PHP_EOL;
}
?>
