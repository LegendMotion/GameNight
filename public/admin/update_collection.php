<?php
session_start();
if (empty($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../api/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $gamecode = $_POST['gamecode'] ?? '';
    $data = $_POST['data'] ?? '';
    if ($id && $gamecode && $data) {
        $stmt = $pdo->prepare('UPDATE collections SET gamecode = ?, data = ? WHERE id = ?');
        $stmt->execute([$gamecode, $data, $id]);
    }
}
header('Location: dashboard.php');
exit;
