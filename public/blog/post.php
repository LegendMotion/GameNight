<?php
require_once __DIR__ . '/../api/db.php';
$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare('SELECT title, content, created_at FROM posts WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$post = $stmt->fetch();
if (!$post) {
    http_response_code(404);
    echo 'Artikkel ikke funnet';
    exit;
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo htmlspecialchars($post['title']); ?></title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<article>
<h1><?php echo htmlspecialchars($post['title']); ?></h1>
<div class="content">
<?php echo nl2br(htmlspecialchars($post['content'])); ?>
</div>
<p><small><?php echo htmlspecialchars($post['created_at']); ?></small></p>
</article>
</body>
</html>
