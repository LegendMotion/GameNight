<?php
header('Content-Type: application/json');
session_start();

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
$stmt = $pdo->prepare('SELECT title, content, featured_image, visibility, edit_token, token_expires_at FROM games WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$game = $stmt->fetch();
if (!$game || $game['visibility'] === 'hidden') {
    http_response_code(404);
    echo json_encode(['error' => 'Game not found']);
    exit;
}

if ($game['visibility'] === 'private') {
    $token = $_GET['token'] ?? '';
    $tokenValid = $token && $token === $game['edit_token'] && (!empty($game['token_expires_at']) && strtotime($game['token_expires_at']) > time());
    if (empty($_SESSION['user_id']) && !$tokenValid) {
        http_response_code(403);
        echo json_encode(['error' => 'Access denied']);
        exit;
    }
}

$response = json_encode([
    'title' => $game['title'],
    'content' => $game['content'],
    'featured_image' => $game['featured_image']
]);
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
    if ($game['visibility'] === 'public') {
        $etag = '"' . md5($response) . '"';
        header('Cache-Control: public, max-age=3600');
        header("ETag: $etag");
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
            http_response_code(304);
            exit;
        }
    } else {
        header('Cache-Control: no-store');
    }
}
echo $response;
?>
