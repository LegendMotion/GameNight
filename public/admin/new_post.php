<?php
session_start();
if (empty($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../api/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $content = $_POST['content'] ?? '';
    if ($title && $slug && $content) {
        $stmt = $pdo->prepare('INSERT INTO posts (slug, title, content, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([$slug, $title, $content]);
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
<title>Ny artikkel</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Ny artikkel</h1>
<?php if (!empty($message)): ?><p style="color:green;"><?php echo $message; ?></p><?php endif; ?>
<?php if (!empty($error)): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
<form method="post">
<input type="text" name="title" placeholder="Tittel" />
<input type="text" name="slug" placeholder="slug" />
<textarea name="content" placeholder="Innhold"></textarea>
<button type="submit">Lagre</button>
</form>
</body>
</html>
