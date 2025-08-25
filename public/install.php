<?php
// Simple web installer for GameNight
if (file_exists(__DIR__.'/../installed.lock')) {
    exit('GameNight is already installed. Remove install.php.');
}

$errors = [];
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    $errors[] = 'PHP 8.1 or higher is required.';
}
$extensions = ['pdo', 'pdo_mysql', 'zip'];
foreach ($extensions as $ext) {
    if (!extension_loaded($ext)) {
        $errors[] = "PHP extension '$ext' is required.";
    }
}

function html($str) { return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
    $host = trim($_POST['db_host'] ?? '');
    $name = trim($_POST['db_name'] ?? '');
    $user = trim($_POST['db_user'] ?? '');
    $pass = trim($_POST['db_pass'] ?? '');

    try {
        $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        foreach (glob(__DIR__.'/../sql/*.sql') as $file) {
            $pdo->exec(file_get_contents($file));
        }

        $env = "DB_HOST=$host\nDB_NAME=$name\nDB_USER=$user\nDB_PASS=$pass\n";
        file_put_contents(__DIR__.'/../.env', $env);

        if (file_exists(__DIR__.'/../composer.json') && !is_dir(__DIR__.'/../vendor')) {
            $output = shell_exec('cd .. && composer install --no-dev 2>&1; echo $?');
            if ($output === null) {
                throw new RuntimeException('Composer failed to run.');
            }
            $lines = explode("\n", trim($output));
            $status = array_pop($lines);
            if ($status !== '0') {
                throw new RuntimeException("Composer install failed:\n" . implode("\n", $lines));
            }
        }

        file_put_contents(__DIR__.'/../installed.lock', date('c'));
        echo 'Installation complete. Remove install.php for security.';
        unlink(__FILE__);
        exit;
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>GameNight Installer</title>
</head>
<body>
<h1>GameNight Installation</h1>
<?php foreach ($errors as $e): ?>
    <p style="color:red;"><?=html($e)?></p>
<?php endforeach; ?>
<form method="post">
    <label>Database Host: <input name="db_host" required></label><br>
    <label>Database Name: <input name="db_name" required></label><br>
    <label>Database User: <input name="db_user" required></label><br>
    <label>Database Password: <input type="password" name="db_pass"></label><br>
    <button type="submit">Install</button>
</form>
</body>
</html>
