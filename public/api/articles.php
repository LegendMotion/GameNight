<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';
$stmt = $pdo->query('SELECT id, slug, title, type, featured_image, created_at FROM posts ORDER BY created_at DESC');
$posts = $stmt->fetchAll();
echo json_encode($posts);
?>
