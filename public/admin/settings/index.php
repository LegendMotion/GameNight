<?php
$requireRole = 'admin';
require_once '../layout.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Invalid CSRF token';
    } else {
        if (!empty($_POST['settings']) && is_array($_POST['settings'])) {
            foreach ($_POST['settings'] as $name => $value) {
                $name = trim($name);
                $value = trim($value);
                if ($name !== '') {
                    save_setting($pdo, $name, $value, (int)$_SESSION['user_id']);
                }
            }
        }
        $newName = trim($_POST['new_name'] ?? '');
        $newValue = trim($_POST['new_value'] ?? '');
        if ($newName !== '') {
            save_setting($pdo, $newName, $newValue, (int)$_SESSION['user_id']);
        }
        $message = 'Settings updated';
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

$stmt = $pdo->query('SELECT name, value FROM settings ORDER BY name');
$settings = $stmt->fetchAll();

$title = 'Settings';
$page = 'settings';
$breadcrumbs = [['label' => 'Innstillinger']];
$help = 'Administrer applikasjonsinnstillinger.';
admin_header(compact('title','page','breadcrumbs','help'));
?>
<h1>Settings</h1>
<p><a href="mail.php">Mail Server</a> | <a href="mfa.php">MFA Options</a></p>
<?php if (!empty($message)): ?><p style="color:green;"><?php echo $message; ?></p><?php endif; ?>
<?php if (!empty($error)): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
<form method="post">
<table>
<thead><tr><th>Name</th><th>Value</th></tr></thead>
<tbody>
<?php foreach ($settings as $setting): ?>
<tr>
    <td><?php echo htmlspecialchars($setting['name'], ENT_QUOTES, 'UTF-8'); ?></td>
    <td><input type="text" name="settings[<?php echo htmlspecialchars($setting['name'], ENT_QUOTES, 'UTF-8'); ?>]" value="<?php echo htmlspecialchars($setting['value'], ENT_QUOTES, 'UTF-8'); ?>" /></td>
</tr>
<?php endforeach; ?>
<tr>
    <td><input type="text" name="new_name" placeholder="New setting name" /></td>
    <td><input type="text" name="new_value" placeholder="New setting value" /></td>
</tr>
</tbody>
</table>
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit">Save</button>
</form>
<?php admin_footer(); ?>
