<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$stmt = $pdo->prepare('SELECT name, value FROM settings WHERE name IN (?, ?)');
$stmt->execute(['integrations_adsense_publisher_id', 'integrations_adsense_layout']);
$settings = [];
foreach ($stmt as $row) {
    $settings[$row['name']] = $row['value'];
}

echo json_encode([
    'publisherId' => $settings['integrations_adsense_publisher_id'] ?? '',
    'layout' => $settings['integrations_adsense_layout'] ?? '',
]);
