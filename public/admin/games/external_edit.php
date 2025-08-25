<?php
$requireRole = ['admin','editor'];
require_once '../auth.php';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require_once __DIR__ . '/../../api/db.php';
require_once __DIR__ . '/../../api/audit_log.php';

$id = $_GET['id'] ?? null;
$token = $_GET['token'] ?? null;
if (!$id || !$token) {
    exit('Token eller ID mangler');
}

$stmt = $pdo->prepare('SELECT * FROM games WHERE id = ? AND edit_token = ? AND token_expires_at > NOW()');
$stmt->execute([$id, $token]);
$game = $stmt->fetch();
if (!$game) {
    exit('Ugyldig eller utløpt token');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Ugyldig CSRF-token';
    } else {
        $title = trim($_POST['title'] ?? '');
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
        if ($title && $content) {
            $sql = 'UPDATE games SET title = ?, featured_image = ?, content = ? WHERE id = ?';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $featured_image, $content, $id]);
            $message = 'Oppdatert!';
            log_audit($pdo, null, 'game_update_external', (string)$id);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $stmt = $pdo->prepare('SELECT * FROM games WHERE id = ?');
            $stmt->execute([$id]);
            $game = $stmt->fetch();
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
<title>Ekstern redigering</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Ekstern redigering</h1>
<?php if (!empty($message)): ?><p style="color:green;"><?php echo $message; ?></p><?php endif; ?>
<?php if (!empty($error)): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data">
<input type="text" name="title" placeholder="Tittel" value="<?php echo htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8'); ?>" />
<input type="file" name="featured_image" accept="image/*" />
<textarea name="content" placeholder="Innhold"><?php echo htmlspecialchars($game['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit">Oppdater</button>
</form>
</body>
</html>
