<?php
session_start();
$pass = getenv('ADMIN_PASS') ?: 'secret';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../api/validate.php';
    $password = sanitize_field($_POST['password'] ?? '', 255);
    if ($password !== false && hash_equals($pass, $password)) {
        $_SESSION['logged_in'] = true;
        header('Location: new_post.php');
        exit;
    }
    $error = 'Feil passord';
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8" />
<title>Admin-login</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Admin</h1>
<?php if (!empty($error)): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
<form method="post">
<input type="password" name="password" placeholder="Passord" />
<button type="submit">Logg inn</button>
</form>
</body>
</html>
