<?php
session_start();
if (empty($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../api/db.php';
$id = $_GET['id'] ?? '';
if ($id) {
    $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->execute([$id]);
}
header('Location: dashboard.php');
exit;
