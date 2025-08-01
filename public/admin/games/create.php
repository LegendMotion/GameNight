<?php
require_once '../auth.php';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require_once __DIR__ . '/../../api/db.php';

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
                $check = $pdo->prepare('SELECT COUNT(*) FROM games WHERE slug = ?');
                $check->execute([$slug]);
                if ($check->fetchColumn() > 0) {
                    $error = 'Slug finnes allerede';
                } else {
                    $sql = 'INSERT INTO games (title, slug, visibility, featured_image, content) VALUES (?, ?, ?, ?, ?)';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$title, $slug, $visibility, $featured_image, $content]);
                    $message = 'Lagret!';
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
<title>Nytt spill</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Nytt spill</h1>
<?php if (!empty($message)): ?><p style="color:green;"><?php echo $message; ?></p><?php endif; ?>
<?php if (!empty($error)): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
<form method="post">
<input type="text" name="title" placeholder="Tittel" />
<input type="text" name="slug" placeholder="slug" />
<select name="visibility">
    <option value="1">Synlig</option>
    <option value="0">Skjult</option>
</select>
<input type="text" name="featured_image" placeholder="Bilde-URL" />
<textarea name="content" placeholder="Innhold"></textarea>
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit">Lagre</button>
</form>
</body>
</html>
