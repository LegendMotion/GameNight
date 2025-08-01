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

$stmt = $pdo->prepare('SELECT * FROM collections WHERE id = ?');
$stmt->execute([$id]);
$collection = $stmt->fetch();
if (!$collection) {
    exit('Samling ikke funnet');
}

$data = json_decode($collection['data'], true) ?: [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Ugyldig CSRF-token';
    } else {
        $name = trim($_POST['name'] ?? '');
        $visibility = $_POST['visibility'] ?? 'public';
        if (!in_array($visibility, ['hidden','private','public'], true)) {
            $visibility = 'public';
        }
        $challenges = array_values(array_filter($_POST['challenges'] ?? [], fn($c) => trim($c) !== ''));
        $image = $data['image'] ?? '';
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../uploads/collections/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = bin2hex(random_bytes(16));
            if ($ext) {
                $filename .= '.' . strtolower($ext);
            }
            $dest = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $image = '/uploads/collections/' . $filename;
            }
        }
        if ($name) {
            $data['name'] = $name;
            $data['visibility'] = $visibility;
            $data['image'] = $image;
            $data['challenges'] = $challenges;
            $stmt = $pdo->prepare('UPDATE collections SET data = ? WHERE id = ?');
            $stmt->execute([json_encode($data, JSON_UNESCAPED_UNICODE), $id]);
            $message = 'Oppdatert!';
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $collection['data'] = json_encode($data);
        } else {
            $error = 'Navn må fylles';
        }
    }
}

$data = json_decode($collection['data'], true) ?: [];
$name = $data['name'] ?? '';
$visibility = $data['visibility'] ?? 'public';
$image = $data['image'] ?? '';
$challenges = $data['challenges'] ?? [];
?>
<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8" />
<title>Rediger collection</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Rediger collection</h1>
<?php if (!empty($message)): ?><p style="color:green;"><?php echo $message; ?></p><?php endif; ?>
<?php if (!empty($error)): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data">
<input type="text" name="name" placeholder="Navn" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" />
<select name="visibility">
    <option value="public"<?php if ($visibility === 'public') echo ' selected'; ?>>Synlig</option>
    <option value="private"<?php if ($visibility === 'private') echo ' selected'; ?>>Privat</option>
    <option value="hidden"<?php if ($visibility === 'hidden') echo ' selected'; ?>>Skjult</option>
</select>
<input type="file" name="image" accept="image/*" />
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
