<?php
session_start();
if (empty($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../api/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gamecode = $_POST['gamecode'] ?? '';
    $data = $_POST['data'] ?? '';
    if ($gamecode && $data) {
        $stmt = $pdo->prepare('INSERT INTO collections (gamecode, data) VALUES (?, ?)');
        $stmt->execute([$gamecode, $data]);
        $message = 'Lagret!';
    } else {
        $error = 'Alle felt må fylles';
    }
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8" />
<title>Ny samling</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Ny samling</h1>
<?php if (!empty($message)): ?><p style="color:green;"><?php echo $message; ?></p><?php endif; ?>
<?php if (!empty($error)): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
<form method="post">
<input type="text" name="gamecode" placeholder="gamecode" />
<textarea name="data" placeholder='{"games":[]}'></textarea>
<button type="submit">Lagre</button>
</form>
</body>
</html>
