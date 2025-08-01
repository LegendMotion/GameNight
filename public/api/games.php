<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/db.php';

$stmt = $pdo->prepare("SELECT id, slug, title, featured_image FROM games WHERE visibility = 'public' ORDER BY id DESC");
$stmt->execute();
$games = $stmt->fetchAll();

echo json_encode($games);
?>
