<?php
header('Content-Type: application/json');

$measurementId = getenv('ANALYTICS_MEASUREMENT_ID') ?: '';
$anonymizeIp = getenv('ANALYTICS_ANONYMIZE_IP') === '1';
$respectDNT = getenv('ANALYTICS_RESPECT_DNT') === '1';

$response = json_encode([
    'measurementId' => $measurementId,
    'anonymizeIp' => $anonymizeIp,
    'respectDNT' => $respectDNT,
]);

$etag = '"' . md5($response) . '"';
header('Cache-Control: public, max-age=300');
header("ETag: $etag");

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
    http_response_code(304);
    exit;
}

echo $response;

