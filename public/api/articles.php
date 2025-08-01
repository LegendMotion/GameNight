<?php
header('Content-Type: application/json');
require_once __DIR__ . '/validate.php';
require_once __DIR__ . '/db.php';
$query = 'SELECT id, slug, title, created_at FROM posts ORDER BY created_at DESC';
$params = [];
if (isset($_GET['limit'])) {
    $limit = validate_int($_GET['limit'], 1, 100);
    if ($limit === false) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid limit']);
        exit;
    }
    $query .= ' LIMIT ?';
    $params[] = $limit;
}
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$posts = $stmt->fetchAll();
echo json_encode($posts);
?>
