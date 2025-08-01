<?php
session_start();
if (empty($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../api/db.php';
// Fetch posts
$postsStmt = $pdo->query('SELECT id, title, slug FROM posts ORDER BY created_at DESC');
$posts = $postsStmt->fetchAll();
// Fetch collections
$collectionsStmt = $pdo->query('SELECT id, gamecode FROM collections ORDER BY id DESC');
$collections = $collectionsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8" />
<title>Admin Dashboard</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Dashboard</h1>
<nav>
    <a href="new_post.php">Ny artikkel</a> |
    <a href="new_collection.php">Ny samling</a>
</nav>
<h2>Artikler</h2>
<ul>
<?php foreach ($posts as $post): ?>
<li>
    <?php echo htmlspecialchars($post['title']); ?>
    [<a href="edit_post.php?id=<?php echo $post['id']; ?>">rediger</a>]
    [<a href="delete_post.php?id=<?php echo $post['id']; ?>" onclick="return confirm('Slette?');">slett</a>]
</li>
<?php endforeach; ?>
</ul>
<h2>Samlinger</h2>
<ul>
<?php foreach ($collections as $col): ?>
<li>
    <?php echo htmlspecialchars($col['gamecode']); ?>
    [<a href="edit_collection.php?id=<?php echo $col['id']; ?>">rediger</a>]
    [<a href="delete_collection.php?id=<?php echo $col['id']; ?>" onclick="return confirm('Slette?');">slett</a>]
</li>
<?php endforeach; ?>
</ul>
</body>
</html>
