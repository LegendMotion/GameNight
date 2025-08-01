<?php
session_start();
if (!empty($_SESSION['logged_in'])) {
    header('Location: dashboard.php');
    exit;
}

$pass = getenv('ADMIN_PASS') ?: 'secret';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST['password'] ?? '';
    if ($input !== '' && hash_equals($pass, $input)) {
        session_regenerate_id(true);
        $_SESSION['logged_in'] = true;
        header('Location: dashboard.php');
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
