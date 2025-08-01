<?php
require_once __DIR__ . '/../api/db.php';
$stmt = $pdo->query('SELECT slug, title, created_at FROM posts ORDER BY created_at DESC');
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>GameNight Blogg</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Blogg</h1>
<ul>
<?php foreach ($posts as $post): ?>
<li><a href="/blog/post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>"><?php echo htmlspecialchars($post['title']); ?></a> <small><?php echo htmlspecialchars($post['created_at']); ?></small></li>
<?php endforeach; ?>
</ul>
</body>
</html>
