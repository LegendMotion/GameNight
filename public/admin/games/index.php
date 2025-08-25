<?php
$requireRole = ['admin','editor'];
require_once '../layout.php';
require_once __DIR__ . '/../../api/db.php';

$search = trim($_GET['q'] ?? '');
$params = [];
$sql = 'SELECT id, title, slug FROM games';
if ($search !== '') {
    $sql .= ' WHERE title LIKE ? OR slug LIKE ?';
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$sql .= ' ORDER BY id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
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
<form method="get" style="margin-bottom:1em;">
<input type="text" name="q" id="filter" placeholder="Search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit">Search</button>
</form>
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
<script>
document.getElementById('filter').addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('tbody tr').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
});
</script>
<?php admin_footer(); ?>
