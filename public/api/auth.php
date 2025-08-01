<?php
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/admin',
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}
require_once __DIR__ . '/db.php';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$stmt = $pdo->prepare('SELECT id, password_hash, role FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();
if ($user && password_verify($password, $user['password_hash'])) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    echo json_encode(['success' => true]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
}
