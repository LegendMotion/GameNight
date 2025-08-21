<?php
$requireRole = 'admin';
require_once '../auth.php';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require_once __DIR__ . '/../../api/db.php';
require_once __DIR__ . '/../../api/audit_log.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    exit('ID mangler');
}

$stmt = $pdo->prepare('SELECT title FROM games WHERE id = ?');
$stmt->execute([$id]);
$game = $stmt->fetch();
if (!$game) {
    exit('Spill ikke funnet');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Ugyldig CSRF-token';
    } else {
        $stmt = $pdo->prepare('DELETE FROM games WHERE id = ?');
        $stmt->execute([$id]);
        log_audit($pdo, (int)$_SESSION['user_id'], 'game_delete', (string)$id);
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8" />
<title>Slett spill</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Slett spill</h1>
<p>Er du sikker på at du vil slette "<?php echo htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8'); ?>"?</p>
<?php if (!empty($error)): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit">Ja, slett</button>
<a href="index.php">Avbryt</a>
</form>
</body>
</html>
