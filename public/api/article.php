<?php
header('Content-Type: application/json');
$slug = $_GET['slug'] ?? '';
if ($slug === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing slug']);
    exit;
}
require_once __DIR__ . '/db.php';
$stmt = $pdo->prepare('SELECT title, content, created_at FROM posts WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$post = $stmt->fetch();
if (!$post) {
    http_response_code(404);
    echo json_encode(['error' => 'Article not found']);
    exit;
}
echo json_encode($post);
?>
