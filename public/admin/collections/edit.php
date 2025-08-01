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
    } elseif (isset($_POST['generate_token'])) {
        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', time() + 86400);
        $stmt = $pdo->prepare('UPDATE collections SET edit_token = ?, token_expires_at = ? WHERE id = ?');
        $stmt->execute([$token, $expires, $id]);
        $message = 'Token generert!';
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $collection['edit_token'] = $token;
        $collection['token_expires_at'] = $expires;
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
$collectionSchemaJson = file_get_contents(__DIR__ . '/../../../docs/collection-schema.json');
$gameSchemaJson = file_get_contents(__DIR__ . '/../../../docs/game-schema.json');
?>
<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8" />
<title>Rediger collection</title>
<link rel="stylesheet" href="/styles/main.css" />
<style>.invalid{border:2px solid red;}</style>
</head>
<body>
<h1>Rediger collection</h1>
<?php if (!empty($message)): ?><p style="color:green;"><?php echo $message; ?></p><?php endif; ?>
<?php if (!empty($error)): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data">
<input type="text" name="name" placeholder="Navn" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" />
<input type="text" name="gamecode" placeholder="Gamecode" value="<?php echo htmlspecialchars($collection['gamecode'], ENT_QUOTES, 'UTF-8'); ?>" readonly />
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
<div id="previewContainer" style="margin-top:1em;">
  <h2>Forhåndsvisning</h2>
  <div id="previewApp"></div>
</div>
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit">Oppdater</button>
</form>
<?php if (!empty($collection['edit_token']) && strtotime($collection['token_expires_at']) > time()): ?>
<p>Ekstern lenke: <a href="/admin/collections/external_edit.php?id=<?php echo $collection['id']; ?>&token=<?php echo htmlspecialchars($collection['edit_token'], ENT_QUOTES, 'UTF-8'); ?>">rediger</a> (utløper <?php echo htmlspecialchars($collection['token_expires_at'], ENT_QUOTES, 'UTF-8'); ?>)</p>
<?php endif; ?>
<form method="post" style="margin-top:1em;">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit" name="generate_token">Generer ekstern lenke</button>
</form>
<script type="module">
import Ajv from 'https://cdn.jsdelivr.net/npm/ajv@8/dist/ajv.esm.js';
import { showChallenge } from '/components/ChallengeCard.js';

const collectionSchema = <?php echo $collectionSchemaJson; ?>;
const gameSchema = <?php echo $gameSchemaJson; ?>;

const ajv = new Ajv({allErrors: true});
ajv.addSchema(gameSchema, 'game-schema.json');
const validate = ajv.compile(collectionSchema);

const form = document.querySelector('form');
const container = document.getElementById('challenges');
const addBtn = document.getElementById('addChallenge');
const submitBtn = form.querySelector('button[type="submit"]');
const previewEl = document.getElementById('previewApp');
const errorBox = document.createElement('div');
errorBox.style.color = 'red';
form.insertBefore(errorBox, form.firstChild);

function addRow(value) {
  const div = document.createElement('div');
  div.className = 'challenge-row';
  div.innerHTML = '<input type="text" name="challenges[]" value="' + value.replace(/"/g, '&quot;') + '"> <button type="button" class="remove-challenge">Fjern</button>';
  div.querySelector('.remove-challenge').addEventListener('click', () => { div.remove(); update(); });
  div.querySelector('input').addEventListener('input', update);
  container.appendChild(div);
}

addBtn.addEventListener('click', () => { addRow(''); update(); });

container.querySelectorAll('.challenge-row input').forEach(inp => inp.addEventListener('input', update));
container.querySelectorAll('.remove-challenge').forEach(btn => btn.addEventListener('click', () => { btn.parentElement.remove(); update(); }));

form.addEventListener('input', update);
form.addEventListener('submit', e => { if (!update()) e.preventDefault(); });

function buildData() {
  const name = form.querySelector('input[name="name"]').value.trim();
  const gamecode = form.querySelector('input[name="gamecode"]').value.trim();
  const visibility = form.querySelector('select[name="visibility"]').value;
  const challenges = [...form.querySelectorAll('input[name="challenges[]"]')].map((input, idx) => ({
    id: String(idx + 1),
    type: 'challenge',
    title: input.value.trim()
  })).filter(c => c.title !== '');
  return { name, gamecode, public: visibility === 'public', challenges };
}

function update() {
  const data = buildData();
  const valid = validate(data);
  errorBox.innerHTML = '';
  form.querySelectorAll('.invalid').forEach(el => el.classList.remove('invalid'));
  if (!valid) {
    validate.errors.forEach(err => {
      const path = err.instancePath.split('/');
      if (path[1] === 'name') form.querySelector('input[name="name"]').classList.add('invalid');
      if (path[1] === 'gamecode') form.querySelector('input[name="gamecode"]').classList.add('invalid');
      if (path[1] === 'challenges' && path[2]) {
        const index = parseInt(path[2], 10);
        const inputs = form.querySelectorAll('input[name="challenges[]"]');
        if (inputs[index]) inputs[index].classList.add('invalid');
      }
      errorBox.innerHTML += '<div>' + (err.instancePath || 'root') + ' ' + err.message + '</div>';
    });
  }
  submitBtn.disabled = !valid;
  previewEl.innerHTML = '';
  if (data.challenges.length) {
    showChallenge(data, { containerId: 'previewApp', applyBackground: false, nextBtnId: 'previewNext' });
  }
  return valid;
}

update();
</script>
</body>
</html>
