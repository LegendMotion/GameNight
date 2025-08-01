<?php
$requireRole = 'admin';
require_once '../auth.php';
require_once __DIR__ . '/../../api/db.php';

$stmt = $pdo->query('SELECT id, email, role, mfa_secret FROM users ORDER BY email');
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Users</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Users</h1>
<p><a href="create.php">Create user</a></p>
<table>
<thead><tr><th>Email</th><th>Role</th><th>MFA</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($users as $user): ?>
<tr>
<td><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo $user['mfa_secret'] ? 'Enabled' : 'Disabled'; ?></td>
<td><a href="edit.php?id=<?php echo $user['id']; ?>">Edit</a><?php if ($user['mfa_secret']): ?> | <a href="reset_mfa.php?id=<?php echo $user['id']; ?>">Reset MFA</a><?php endif; ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</body>
</html>
