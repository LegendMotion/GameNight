<?php
header('Content-Type: application/json');
$slug = $_GET['slug'] ?? '';
if ($slug === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing slug']);
    exit;
}
if (!preg_match('/^[a-z0-9-]{1,64}$/', $slug)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid slug format']);
    exit;
}
require_once __DIR__ . '/db.php';
$stmt = $pdo->prepare('SELECT title, type, content, requirements, ingredients, featured_image, created_at FROM posts WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$post = $stmt->fetch();
if (!$post) {
    http_response_code(404);
    echo json_encode(['error' => 'Article not found']);
    exit;
}

$response = json_encode($post);
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
