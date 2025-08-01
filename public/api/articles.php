<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$type = $_GET['type'] ?? '';
if ($type !== '') {
    $stmt = $pdo->prepare("SELECT id, slug, title, type, featured_image, created_at FROM posts WHERE visibility = 'public' AND type = ? ORDER BY created_at DESC");
    $stmt->execute([$type]);
} else {
    $stmt = $pdo->query("SELECT id, slug, title, type, featured_image, created_at FROM posts WHERE visibility = 'public' ORDER BY created_at DESC");
}

$posts = $stmt->fetchAll();
if (empty($posts)) {
    http_response_code(404);
}

$response = json_encode($posts);
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
