<?php
$requireRole = 'admin';
require_once '../auth.php';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require_once __DIR__ . '/../../api/db.php';

$roles = ['viewer', 'editor', 'admin'];
$id = $_GET['id'] ?? null;
if (!$id) {
    exit('ID missing');
}
$stmt = $pdo->prepare('SELECT id, email, role, mfa_enabled FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) {
    exit('User not found');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Invalid CSRF token';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'viewer';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email';
        } elseif ($password !== '' && strlen($password) < 8) {
            $error = 'Password must be at least 8 characters';
        } elseif (!in_array($role, $roles, true)) {
            $role = 'viewer';
        } else {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ? AND id != ?');
            $stmt->execute([$email, $id]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'Email already exists';
            } else {
                if ($password !== '') {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare('UPDATE users SET email = ?, password_hash = ?, role = ? WHERE id = ?');
                    $stmt->execute([$email, $hash, $role, $id]);
                } else {
                    $stmt = $pdo->prepare('UPDATE users SET email = ?, role = ? WHERE id = ?');
                    $stmt->execute([$email, $role, $id]);
                }
                $message = 'User updated';
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                $stmt = $pdo->prepare('SELECT id, email, role, mfa_enabled FROM users WHERE id = ?');
                $stmt->execute([$id]);
                $user = $stmt->fetch();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Edit User</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Edit User</h1>
<?php if (!empty($message)): ?><p style="color:green;"><?php echo $message; ?></p><?php endif; ?>
<?php if (!empty($error)): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
<form method="post">
<input type="email" name="email" value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>" />
<input type="password" name="password" placeholder="New password" />
<select name="role">
    <option value="viewer"<?php if ($user['role'] === 'viewer') echo ' selected'; ?>>Viewer</option>
    <option value="editor"<?php if ($user['role'] === 'editor') echo ' selected'; ?>>Editor</option>
    <option value="admin"<?php if ($user['role'] === 'admin') echo ' selected'; ?>>Admin</option>
</select>
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit">Update</button>
</form>
<p>MFA: <?php echo $user['mfa_enabled'] ? 'Enabled' : 'Disabled'; ?><?php if ($user['mfa_enabled']): ?> (<a href="reset_mfa.php?id=<?php echo $user['id']; ?>">Reset MFA</a>)<?php endif; ?></p>
<p><a href="index.php">Back to users</a></p>
</body>
</html>
