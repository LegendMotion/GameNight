<?php
header('Content-Type: application/json');
$gamecode = $_GET['gamecode'] ?? '';
if ($gamecode === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing gamecode']);
    exit;
}
require_once __DIR__ . '/db.php';
$stmt = $pdo->prepare('SELECT data FROM collections WHERE gamecode = ? LIMIT 1');
$stmt->execute([$gamecode]);
$row = $stmt->fetch();
if (!$row) {
    http_response_code(404);
    echo json_encode(['error' => 'Collection not found']);
    exit;
}
echo $row['data'];
?>
