<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$cleared = 0;
$stmt = $pdo->prepare('UPDATE games SET edit_token = NULL, token_expires_at = NULL WHERE token_expires_at IS NOT NULL AND token_expires_at < NOW()');
$stmt->execute();
$cleared += $stmt->rowCount();
$stmt = $pdo->prepare('UPDATE collections SET edit_token = NULL, token_expires_at = NULL WHERE token_expires_at IS NOT NULL AND token_expires_at < NOW()');
$stmt->execute();
$cleared += $stmt->rowCount();

echo json_encode(['cleared' => $cleared]);
?>
