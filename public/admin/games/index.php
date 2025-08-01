<?php
require_once '../layout.php';
require_once __DIR__ . '/../../api/db.php';

$stmt = $pdo->prepare('SELECT id, title, slug FROM games ORDER BY id DESC');
$stmt->execute();
$games = $stmt->fetchAll();

$title = 'Games';
$page = 'games';
$breadcrumbs = [['label' => 'Spill']];
$help = 'Administrer spill.';
admin_header(compact('title','page','breadcrumbs','help'));
?>
<h1>Games</h1>
<?php if (user_can(['admin','editor'])): ?>
<p><a href="create.php">Nytt spill</a></p>
<?php endif; ?>
<table>
<thead><tr><th>Tittel</th><th>Slug</th><th>Handlinger</th></tr></thead>
<tbody>
<?php foreach ($games as $game): ?>
<tr>
<td><?php echo htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($game['slug'], ENT_QUOTES, 'UTF-8'); ?></td>
<td>
    <a href="edit.php?id=<?php echo $game['id']; ?>">Rediger</a>
    <?php if (user_can(['admin'])): ?>
    <a href="delete.php?id=<?php echo $game['id']; ?>">Slett</a>
    <?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php admin_footer(); ?>
