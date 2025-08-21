<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/logging.php';

// Database connection helper
// Adjust credentials via environment variables for security
$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'gamenight';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$charset = 'utf8mb4';

$dsn = getenv('DB_DSN') ?: "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_PERSISTENT         => true,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    log_message('critical', 'Database connection failed', ['exception' => $e]);
    send_alert('Database connection failure');
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}
?>
