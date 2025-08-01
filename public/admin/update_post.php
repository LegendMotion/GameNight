<?php
session_start();
if (empty($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../api/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $title = $_POST['title'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $content = $_POST['content'] ?? '';
    if ($id && $title && $slug && $content) {
        $stmt = $pdo->prepare('UPDATE posts SET title = ?, slug = ?, content = ? WHERE id = ?');
        $stmt->execute([$title, $slug, $content, $id]);
    }
}
header('Location: dashboard.php');
exit;
