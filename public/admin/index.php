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
<p id="error" style="color:red;"></p>
<form id="login-form">
<input type="email" name="email" placeholder="E-post" />
<input type="password" name="password" placeholder="Passord" />
<button type="submit">Logg inn</button>
</form>
<script>
document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const resp = await fetch('/api/auth.php', {method: 'POST', body: formData});
    if (resp.ok) {
        window.location.href = 'articles/index.php';
    } else {
        document.getElementById('error').textContent = 'Feil e-post eller passord';
    }
});
</script>
</body>
</html>
