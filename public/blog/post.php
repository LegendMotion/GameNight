<?php
require_once __DIR__ . '/../api/db.php';
$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare('SELECT title, type, content, requirements, ingredients, featured_image, created_at FROM posts WHERE slug = ? LIMIT 1');
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
<?php include __DIR__ . '/../seo_verification.php'; ?>
</head>
<body>
<article>
<h1><?php echo htmlspecialchars($post['title']); ?></h1>
<?php if (!empty($post['featured_image'])): ?>
  <img loading="lazy" src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="" />
<?php endif; ?>
<?php if ($post['type'] === 'game' && !empty($post['requirements'])): ?>
  <h2>What you need</h2>
  <ul>
  <?php foreach (explode("\n", $post['requirements']) as $item): ?>
    <?php if (trim($item) !== ''): ?><li><?php echo htmlspecialchars(trim($item)); ?></li><?php endif; ?>
  <?php endforeach; ?>
  </ul>
<?php endif; ?>
<?php if ($post['type'] === 'drink' && !empty($post['ingredients'])): ?>
  <h2>Recipe</h2>
  <ul>
  <?php foreach (explode("\n", $post['ingredients']) as $item): ?>
    <?php if (trim($item) !== ''): ?><li><?php echo htmlspecialchars(trim($item)); ?></li><?php endif; ?>
  <?php endforeach; ?>
  </ul>
<?php endif; ?>
<div class="content">
<?php echo nl2br(htmlspecialchars($post['content'])); ?>
</div>
<p><small><?php echo htmlspecialchars($post['created_at']); ?></small></p>
</article>
</body>
</html>
