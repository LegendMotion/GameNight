<?php
header('Content-Type: application/json');
$gamecode = $_GET['gamecode'] ?? '';
if ($gamecode === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing gamecode']);
    exit;
}
if (!preg_match('/^[A-Z0-9]{6}$/', $gamecode)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid gamecode format']);
    exit;
}
require_once __DIR__ . '/db.php';
$stmt = $pdo->prepare("SELECT data FROM collections WHERE gamecode = ? AND visibility = 'public' LIMIT 1");
$stmt->execute([$gamecode]);
$row = $stmt->fetch();
if (!$row) {
    http_response_code(404);
    echo json_encode(['error' => 'Collection not found']);
    exit;
}
echo $row['data'];
?>
