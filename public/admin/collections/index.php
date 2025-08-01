<?php
require_once '../auth.php';
require_once __DIR__ . '/../../api/db.php';

$stmt = $pdo->query('SELECT id, gamecode, visibility, data FROM collections ORDER BY id DESC');
$collections = [];
while ($row = $stmt->fetch()) {
    $data = json_decode($row['data'], true);
    $collections[] = [
        'id' => $row['id'],
        'gamecode' => $row['gamecode'],
        'visibility' => $row['visibility'],
        'name' => $data['name'] ?? ''
    ];
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8" />
<title>Collections</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Collections</h1>
<table>
<thead><tr><th>Navn</th><th>Gamecode</th><th>Synlighet</th><th>Handlinger</th></tr></thead>
<tbody>
<?php foreach ($collections as $c): ?>
<tr>
<td><?php echo htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($c['gamecode'], ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($c['visibility'], ENT_QUOTES, 'UTF-8'); ?></td>
<td><a href="edit.php?id=<?php echo $c['id']; ?>">Rediger</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</body>
</html>
