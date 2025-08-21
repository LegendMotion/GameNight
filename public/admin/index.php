<?php
$requireLogin = false;
require_once 'auth.php';
require_once 'i18n.php';
if (!empty($_SESSION['user_id'])) {
    header('Location: articles/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo get_locale(); ?>">
<head>
<meta charset="UTF-8" />
<title><?php echo t('admin_login'); ?></title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1><?php echo t('admin'); ?></h1>
<form id="login-form">
<input type="email" name="email" placeholder="<?php echo t('email'); ?>" />
<input type="password" name="password" placeholder="<?php echo t('password'); ?>" />
<button type="submit"><?php echo t('login'); ?></button>
</form>
<link rel="stylesheet" href="/assets/adminLogin.css" />
<script type="module" src="/admin/login.js"></script>
</body>
</html>
