<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$type = $_GET['type'] ?? '';
$search = trim($_GET['q'] ?? '');
$params = [];
$where = "visibility = 'public'";
if ($type !== '') {
    $where .= " AND type = ?";
    $params[] = $type;
}
if ($search !== '') {
    $where .= " AND (title LIKE ? OR slug LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$sql = "SELECT id, slug, title, type, featured_image, created_at FROM posts WHERE $where ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

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
