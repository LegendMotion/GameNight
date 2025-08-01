<?php
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/admin',
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();
$hash = getenv('ADMIN_PASS_HASH') ?: '$2y$12$EF2ZteyOXTqCYpP46/MuwuaYwPo/X5YuwlP1r14/BzQMco544Dnn6';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Ugyldig CSRF-token';
    } elseif (!empty($_POST['password']) && password_verify($_POST['password'], $hash)) {
        session_regenerate_id(true);
        $_SESSION['logged_in'] = true;
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header('Location: new_post.php');
        exit;
    } else {
        $error = 'Feil passord';
    }
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit">Logg inn</button>
</form>
</body>
</html>
