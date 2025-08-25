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

$stmt = $pdo->prepare('SELECT * FROM collections WHERE id = ? AND edit_token = ? AND token_expires_at > NOW()');
$stmt->execute([$id, $token]);
$collection = $stmt->fetch();
if (!$collection) {
    exit('Ugyldig eller utløpt token');
}

$data = json_decode($collection['data'], true) ?: [];
$challenges = $data['challenges'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Ugyldig CSRF-token';
    } else {
        $challenges = array_values(array_filter($_POST['challenges'] ?? [], fn($c) => trim($c) !== ''));
        $data['challenges'] = $challenges;
        $stmt = $pdo->prepare('UPDATE collections SET data = ? WHERE id = ?');
        $stmt->execute([json_encode($data, JSON_UNESCAPED_UNICODE), $id]);
        $message = 'Oppdatert!';
        log_audit($pdo, null, 'collection_update_external', $collection['gamecode']);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $collection['data'] = json_encode($data);
    }
}

$data = json_decode($collection['data'], true) ?: [];
$challenges = $data['challenges'] ?? [];
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
<form method="post">
<div id="challenges">
<?php foreach ($challenges as $c): ?>
    <div class="challenge-row">
        <input type="text" name="challenges[]" value="<?php echo htmlspecialchars($c, ENT_QUOTES, 'UTF-8'); ?>" />
        <button type="button" class="remove-challenge">Fjern</button>
    </div>
<?php endforeach; ?>
</div>
<button type="button" id="addChallenge">Legg til utfordring</button>
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit">Oppdater</button>
</form>
<script>
(function() {
  const container = document.getElementById('challenges');
  document.getElementById('addChallenge').addEventListener('click', () => addRow(''));
  function addRow(value) {
    const div = document.createElement('div');
    div.className = 'challenge-row';
    div.innerHTML = '<input type="text" name="challenges[]" value="' + value.replace(/"/g, '&quot;') + '" /> <button type="button" class="remove-challenge">Fjern</button>';
    div.querySelector('.remove-challenge').addEventListener('click', () => div.remove());
    container.appendChild(div);
  }
  container.querySelectorAll('.remove-challenge').forEach(btn => {
    btn.addEventListener('click', () => btn.parentElement.remove());
  });
})();
</script>
</body>
</html>
