<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$stmt = $pdo->prepare('SELECT name, value FROM settings WHERE name IN (?, ?, ?)');
$stmt->execute(['integrations_ga_measurement_id', 'integrations_ga_anonymize_ip', 'integrations_ga_respect_dnt']);
$settings = [];
foreach ($stmt as $row) {
    $settings[$row['name']] = $row['value'];
}

echo json_encode([
    'measurementId' => $settings['integrations_ga_measurement_id'] ?? '',
    'anonymizeIp' => ($settings['integrations_ga_anonymize_ip'] ?? '0') === '1',
    'respectDNT' => ($settings['integrations_ga_respect_dnt'] ?? '0') === '1',
]);
