<?php
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/admin',
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();
$requireLogin = $requireLogin ?? true;
if ($requireLogin && empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
if (isset($requireRole)) {
    $roles = is_array($requireRole) ? $requireRole : [$requireRole];
    if (!in_array($_SESSION['role'] ?? '', $roles, true)) {
        http_response_code(403);
        exit('Access denied');
    }
}
?>
