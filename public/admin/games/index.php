<?php
require_once '../auth.php';
require_once __DIR__ . '/../../api/db.php';

$stmt = $pdo->prepare('SELECT id, title, slug FROM games ORDER BY id DESC');
$stmt->execute();
$games = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8" />
<title>Games</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Games</h1>
<p><a href="create.php">Nytt spill</a></p>
<table>
<thead><tr><th>Tittel</th><th>Slug</th><th>Handlinger</th></tr></thead>
<tbody>
<?php foreach ($games as $game): ?>
<tr>
<td><?php echo htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($game['slug'], ENT_QUOTES, 'UTF-8'); ?></td>
<td>
    <a href="edit.php?id=<?php echo $game['id']; ?>">Rediger</a>
    <a href="delete.php?id=<?php echo $game['id']; ?>">Slett</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</body>
</html>
