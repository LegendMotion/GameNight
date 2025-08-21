<?php
function log_audit(PDO $pdo, ?int $userId, string $action, string $target = '', array $metadata = []): void {
    $stmt = $pdo->prepare('INSERT INTO audit_logs (user_id, action, target, metadata) VALUES (?, ?, ?, ?)');
    $json = $metadata ? json_encode($metadata, JSON_UNESCAPED_UNICODE) : null;
    $stmt->execute([$userId, $action, $target, $json]);
}
?>
