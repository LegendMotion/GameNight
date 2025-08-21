<?php
$requireRole = 'admin';
require_once '../auth.php';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require_once __DIR__ . '/../../api/db.php';

$roles = ['viewer', 'editor', 'admin'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Invalid CSRF token';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'viewer';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters';
        } elseif (!in_array($role, $roles, true)) {
            $role = 'viewer';
        } else {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'Email already exists';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, role) VALUES (?, ?, ?)');
                $stmt->execute([$email, $hash, $role]);
                $message = 'User created';
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Create User</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Create User</h1>
<?php if (!empty($message)): ?><p style="color:green;"><?php echo $message; ?></p><?php endif; ?>
<?php if (!empty($error)): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
<form method="post">
<input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
<input type="password" name="password" placeholder="Password" />
<select name="role">
    <option value="viewer"<?php if (($_POST['role'] ?? '') === 'viewer') echo ' selected'; ?>>Viewer</option>
    <option value="editor"<?php if (($_POST['role'] ?? '') === 'editor') echo ' selected'; ?>>Editor</option>
    <option value="admin"<?php if (($_POST['role'] ?? '') === 'admin') echo ' selected'; ?>>Admin</option>
</select>
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit">Create</button>
</form>
<p><a href="index.php">Back to users</a></p>
</body>
</html>
