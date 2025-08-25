<?php
$requireRole = ['admin','editor'];
require_once '../layout.php';
require_once __DIR__ . '/../../api/db.php';

$perPage = 10;
$pageNum = max(1, (int)($_GET['page'] ?? 1));
$search = trim($_GET['q'] ?? '');
$type = $_GET['type'] ?? '';

$params = [];
$where = 'WHERE 1';
if ($search !== '') {
    $where .= ' AND (title LIKE ? OR slug LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($type !== '') {
    $where .= ' AND type = ?';
    $params[] = $type;
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM posts $where");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));
$offset = ($pageNum - 1) * $perPage;

$paramsWithLimit = $params;
$paramsWithLimit[] = $perPage;
$paramsWithLimit[] = $offset;
$stmt = $pdo->prepare("SELECT id, title, slug, type FROM posts $where ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->execute($paramsWithLimit);
$articles = $stmt->fetchAll();

$title = 'Artikler';
$page = 'articles';
$breadcrumbs = [['label' => 'Artikler']];
$help = 'Administrer artikler. Søk, rediger eller slett poster.';
admin_header(compact('title','page','breadcrumbs','help'));
?>
<h1>Artikler</h1>
<?php if (user_can(['admin','editor'])): ?>
<p><a href="create.php">Ny artikkel</a></p>
<?php endif; ?>
<form method="get" style="margin-bottom:1em;">
<input type="text" name="q" id="filter" placeholder="Søk" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" />
<select name="type">
    <option value="">Alle typer</option>
    <option value="drink"<?php if ($type === 'drink') echo ' selected'; ?>>Drink</option>
    <option value="game"<?php if ($type === 'game') echo ' selected'; ?>>Spill</option>
</select>
<button type="submit">Søk</button>
</form>
<table>
<thead><tr><th>Tittel</th><th>Type</th><th>Slug</th><th>Handlinger</th></tr></thead>
<tbody>
<?php foreach ($articles as $article): ?>
<tr>
<td><?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($article['type'], ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($article['slug'], ENT_QUOTES, 'UTF-8'); ?></td>
<td>
    <a href="edit.php?id=<?php echo $article['id']; ?>">Rediger</a>
    <?php if (user_can(['admin'])): ?>
    <a href="delete.php?id=<?php echo $article['id']; ?>">Slett</a>
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
<p>Side <?php echo $pageNum; ?> av <?php echo $totalPages; ?></p>
<div>
<?php if ($pageNum > 1): ?>
<a href="?<?php echo http_build_query(['page' => $pageNum - 1, 'q' => $search, 'type' => $type]); ?>">Forrige</a>
<?php endif; ?>
<?php if ($pageNum < $totalPages): ?>
<a href="?<?php echo http_build_query(['page' => $pageNum + 1, 'q' => $search, 'type' => $type]); ?>">Neste</a>
<?php endif; ?>
</div>
<?php admin_footer(); ?>
