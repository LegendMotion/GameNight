<?php
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/admin',
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();
$requireLogin = $requireLogin ?? true;
if ($requireLogin) {
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
    if (!empty($requireRole) && ($_SESSION['role'] ?? '') !== $requireRole) {
        http_response_code(403);
        exit('Access denied');
    }
}
?>
