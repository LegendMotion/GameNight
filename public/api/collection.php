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

$response = $row['data'];
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
