<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/db.php';

$stmt = $pdo->prepare("SELECT id, slug, title, featured_image FROM games WHERE visibility = 'public' ORDER BY id DESC");
$stmt->execute();
$games = $stmt->fetchAll();

$response = json_encode($games);
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
    $etag = '"' . md5($response) . '"';
    header('Cache-Control: public, max-age=3600');
    header("ETag: $etag");
    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
        http_response_code(304);
        exit;
    }
}
echo $response;
?>
