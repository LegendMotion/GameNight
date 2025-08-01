<?php
$requireLogin = false;
require_once 'auth.php';
if (!empty($_SESSION['user_id'])) {
    header('Location: articles/index.php');
    exit;
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
<form id="login-form">
<input type="email" name="email" placeholder="E-post" />
<input type="password" name="password" placeholder="Passord" />
<button type="submit">Logg inn</button>
</form>
<link rel="stylesheet" href="/assets/adminLogin.css" />
<script type="module" src="/admin/login.js"></script>
</body>
</html>
