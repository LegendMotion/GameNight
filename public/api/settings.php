<?php
header('Content-Type: application/json');
session_start();

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

require_once __DIR__ . '/db.php';

function save_setting(PDO $pdo, string $name, string $value, int $userId): void {
    $stmt = $pdo->prepare('SELECT value FROM settings WHERE name = ?');
    $stmt->execute([$name]);
    $oldValue = $stmt->fetchColumn();
    if ($oldValue === false) {
        $stmt = $pdo->prepare('INSERT INTO settings (name, value) VALUES (?, ?)');
        $stmt->execute([$name, $value]);
    } else {
        $stmt = $pdo->prepare('UPDATE settings SET value = ? WHERE name = ?');
        $stmt->execute([$value, $name]);
    }
    if ($oldValue !== $value) {
        $audit = $pdo->prepare('INSERT INTO settings_audit (name, old_value, new_value, changed_by) VALUES (?, ?, ?, ?)');
        $audit->execute([$name, $oldValue, $value, $userId]);
    }
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $prefix = $_GET['prefix'] ?? '';
    if ($prefix !== '') {
        $stmt = $pdo->prepare('SELECT name, value FROM settings WHERE name LIKE ?');
        $stmt->execute([$prefix . '%']);
    } else {
        $stmt = $pdo->query('SELECT name, value FROM settings');
    }
    $settings = [];
    foreach ($stmt as $row) {
        $settings[$row['name']] = $row['value'];
    }
    echo json_encode(['settings' => $settings]);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data['settings'] ?? null)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid payload']);
        exit;
    }
    foreach ($data['settings'] as $name => $value) {
        $name = trim((string)$name);
        $value = trim((string)$value);
        if ($name !== '') {
            save_setting($pdo, $name, $value, (int)$_SESSION['user_id']);
        }
    }
    echo json_encode(['success' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
