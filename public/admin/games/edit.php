<?php
require_once '../auth.php';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require_once __DIR__ . '/../../api/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    exit('ID mangler');
}

$stmt = $pdo->prepare('SELECT * FROM games WHERE id = ?');
$stmt->execute([$id]);
$game = $stmt->fetch();
if (!$game) {
    exit('Spill ikke funnet');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Ugyldig CSRF-token';
    } else {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $visibility = isset($_POST['visibility']) ? (int)$_POST['visibility'] : 0;
        $featured_image = trim($_POST['featured_image'] ?? '');
        $content = trim($_POST['content'] ?? '');
        if ($title && $slug && $content) {
            if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
                $error = 'Slug kan kun inneholde små bokstaver, tall og bindestrek';
            } else {
                $check = $pdo->prepare('SELECT COUNT(*) FROM games WHERE slug = ? AND id != ?');
                $check->execute([$slug, $id]);
                if ($check->fetchColumn() > 0) {
                    $error = 'Slug finnes allerede';
                } else {
                    $sql = 'UPDATE games SET title = ?, slug = ?, visibility = ?, featured_image = ?, content = ? WHERE id = ?';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$title, $slug, $visibility, $featured_image, $content, $id]);
                    $message = 'Oppdatert!';
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    $stmt = $pdo->prepare('SELECT * FROM games WHERE id = ?');
                    $stmt->execute([$id]);
                    $game = $stmt->fetch();
                }
            }
        } else {
            $error = 'Alle felt må fylles';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8" />
<title>Rediger spill</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Rediger spill</h1>
<?php if (!empty($message)): ?><p style="color:green;"><?php echo $message; ?></p><?php endif; ?>
<?php if (!empty($error)): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
<form method="post">
<input type="text" name="title" placeholder="Tittel" value="<?php echo htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8'); ?>" />
<input type="text" name="slug" placeholder="slug" value="<?php echo htmlspecialchars($game['slug'], ENT_QUOTES, 'UTF-8'); ?>" />
<select name="visibility">
    <option value="1"<?php if ($game['visibility']) echo ' selected'; ?>>Synlig</option>
    <option value="0"<?php if (!$game['visibility']) echo ' selected'; ?>>Skjult</option>
</select>
<input type="text" name="featured_image" placeholder="Bilde-URL" value="<?php echo htmlspecialchars($game['featured_image'], ENT_QUOTES, 'UTF-8'); ?>" />
<textarea name="content" placeholder="Innhold"><?php echo htmlspecialchars($game['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit">Oppdater</button>
</form>
</body>
</html>
