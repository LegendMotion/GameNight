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
$stmt = $pdo->prepare('SELECT slug, title, content FROM posts WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8" />
<title>Rediger artikkel</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Rediger artikkel</h1>
<form method="post" action="update_post.php">
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" />
<input type="text" name="slug" value="<?php echo htmlspecialchars($post['slug']); ?>" />
<textarea name="content"><?php echo htmlspecialchars($post['content']); ?></textarea>
<button type="submit">Oppdater</button>
</form>
</body>
</html>
