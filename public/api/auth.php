<?php
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/admin',
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/audit_log.php';
use Sonata\GoogleAuthenticator\GoogleAuthenticator;
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$stmt = $pdo->prepare('SELECT id, password_hash, role, mfa_secret, mfa_enabled FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();
if ($user && password_verify($password, $user['password_hash'])) {
    if (!empty($user['mfa_enabled'])) {
        $totp = $_POST['totp'] ?? '';
        $emailCode = $_POST['email_code'] ?? '';
        $verified = false;
        if ($totp !== '') {
            $g = new GoogleAuthenticator();
            $verified = $g->checkCode($user['mfa_secret'], $totp);
        } elseif ($emailCode !== '') {
            if (!empty($_SESSION['mfa_email_code']) && hash_equals($_SESSION['mfa_email_code'], $emailCode)) {
                $verified = true;
                unset($_SESSION['mfa_email_code']);
            }
        }
        if (!$verified) {
            http_response_code(401);
            echo json_encode(['error' => 'MFA required']);
            exit;
        }
    }
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    log_audit($pdo, (int)$user['id'], 'login', '', ['ip' => $_SERVER['REMOTE_ADDR'] ?? '']);
    echo json_encode(['success' => true, 'role' => $user['role']]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
}
