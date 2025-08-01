<?php
$requireRole = 'admin';
require_once '../auth.php';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require_once __DIR__ . '/../../api/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    exit('ID missing');
}
$stmt = $pdo->prepare('SELECT id, email FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) {
    exit('User not found');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Invalid CSRF token';
    } else {
        $stmt = $pdo->prepare('UPDATE users SET mfa_secret = NULL, mfa_enabled = 0 WHERE id = ?');
        $stmt->execute([$id]);
        $message = 'MFA reset';
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Reset MFA</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Reset MFA</h1>
<p>User: <?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></p>
<?php if (!empty($message)): ?><p style="color:green;"><?php echo $message; ?></p><?php endif; ?>
<?php if (!empty($error)): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
<?php if (empty($message)): ?>
<form method="post">
<p>Are you sure you want to reset MFA for this user?</p>
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit">Reset MFA</button>
</form>
<?php endif; ?>
<p><a href="index.php">Back to users</a></p>
</body>
</html>
