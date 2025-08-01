<?php
session_start();
if (empty($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../api/db.php';
$id = $_GET['id'] ?? '';
if (!$id) {
    header('Location: dashboard.php');
    exit;
}
$stmt = $pdo->prepare('SELECT gamecode, data FROM collections WHERE id = ?');
$stmt->execute([$id]);
$col = $stmt->fetch();
if (!$col) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8" />
<title>Rediger samling</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Rediger samling</h1>
<form method="post" action="update_collection.php">
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<input type="text" name="gamecode" value="<?php echo htmlspecialchars($col['gamecode']); ?>" />
<textarea name="data"><?php echo htmlspecialchars($col['data']); ?></textarea>
<button type="submit">Oppdater</button>
</form>
</body>
</html>
