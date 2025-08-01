<?php
$requireRole = 'admin';
require_once '../auth.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../../api/db.php';

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

$keys = ['mail_host', 'mail_port', 'mail_user', 'mail_pass'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Invalid CSRF token';
    } else {
        foreach ($keys as $key) {
            $value = trim($_POST[$key] ?? '');
            save_setting($pdo, $key, $value, (int)$_SESSION['user_id']);
        }
        $message = 'Mail settings updated';
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

$values = [];
foreach ($keys as $key) {
    $stmt = $pdo->prepare('SELECT value FROM settings WHERE name = ?');
    $stmt->execute([$key]);
    $values[$key] = $stmt->fetchColumn() ?? '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Mail Settings</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Mail Settings</h1>
<?php if (!empty($message)): ?><p style="color:green;"><?php echo $message; ?></p><?php endif; ?>
<?php if (!empty($error)): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
<form method="post">
<p>Host <input type="text" name="mail_host" value="<?php echo htmlspecialchars($values['mail_host'], ENT_QUOTES, 'UTF-8'); ?>" /></p>
<p>Port <input type="text" name="mail_port" value="<?php echo htmlspecialchars($values['mail_port'], ENT_QUOTES, 'UTF-8'); ?>" /></p>
<p>User <input type="text" name="mail_user" value="<?php echo htmlspecialchars($values['mail_user'], ENT_QUOTES, 'UTF-8'); ?>" /></p>
<p>Password <input type="text" name="mail_pass" value="<?php echo htmlspecialchars($values['mail_pass'], ENT_QUOTES, 'UTF-8'); ?>" /></p>
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit">Save</button>
</form>
<p><a href="index.php">Back to settings</a></p>
</body>
</html>
