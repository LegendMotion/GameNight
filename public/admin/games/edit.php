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
    } elseif (isset($_POST['generate_token'])) {
        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', time() + 86400);
        $stmt = $pdo->prepare('UPDATE games SET edit_token = ?, token_expires_at = ? WHERE id = ?');
        $stmt->execute([$token, $expires, $id]);
        $message = 'Token generert!';
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $game['edit_token'] = $token;
        $game['token_expires_at'] = $expires;
    } else {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $visibility = $_POST['visibility'] ?? 'public';
        if (!in_array($visibility, ['hidden','private','public'], true)) {
            $visibility = 'public';
        }
        $featured_image = $game['featured_image'];
        if (!empty($_FILES['featured_image']['name']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../uploads/games/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
            $filename = bin2hex(random_bytes(16));
            if ($ext) {
                $filename .= '.' . strtolower($ext);
            }
            $dest = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $dest)) {
                $featured_image = '/uploads/games/' . $filename;
            }
        }
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
<form method="post" enctype="multipart/form-data">
<input type="text" name="title" placeholder="Tittel" value="<?php echo htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8'); ?>" />
<input type="text" name="slug" placeholder="slug" value="<?php echo htmlspecialchars($game['slug'], ENT_QUOTES, 'UTF-8'); ?>" />
<select name="visibility">
    <option value="public"<?php if ($game['visibility'] === 'public') echo ' selected'; ?>>Synlig</option>
    <option value="private"<?php if ($game['visibility'] === 'private') echo ' selected'; ?>>Privat</option>
    <option value="hidden"<?php if ($game['visibility'] === 'hidden') echo ' selected'; ?>>Skjult</option>
</select>
<input type="file" name="featured_image" accept="image/*" />
<textarea name="content" placeholder="Innhold"><?php echo htmlspecialchars($game['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit">Oppdater</button>
</form>
<?php if (!empty($game['edit_token']) && strtotime($game['token_expires_at']) > time()): ?>
<p>Ekstern lenke: <a href="/admin/games/external_edit.php?id=<?php echo $game['id']; ?>&token=<?php echo htmlspecialchars($game['edit_token'], ENT_QUOTES, 'UTF-8'); ?>">rediger</a> (utløper <?php echo htmlspecialchars($game['token_expires_at'], ENT_QUOTES, 'UTF-8'); ?>)</p>
<?php endif; ?>
<form method="post" style="margin-top:1em;">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit" name="generate_token">Generer ekstern lenke</button>
</form>
</body>
</html>
