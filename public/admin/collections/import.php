<?php
$requireRole = ['admin','editor'];
require_once '../layout.php';
require_once __DIR__ . '/../../api/db.php';
require_once __DIR__ . '/../../api/audit_log.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = '';
    if (!empty($_FILES['json_file']['tmp_name'])) {
        $json = file_get_contents($_FILES['json_file']['tmp_name']);
    } else {
        $json = trim($_POST['json_text'] ?? '');
    }

    if ($json === '') {
        $error = 'Ingen JSON oppgitt';
    } elseif (!function_exists('json_validate') || !json_validate($json)) {
        $error = 'Ugyldig JSON';
    } else {
        $data = json_decode($json, true);
        if (!is_array($data)) {
            $error = 'Kunne ikke lese JSON';
        } else {
            // Ensure unique gamecode, generate if missing
            if (empty($data['gamecode'])) {
                do {
                    $data['gamecode'] = strtoupper(bin2hex(random_bytes(3)));
                    $stmt = $pdo->prepare('SELECT COUNT(*) FROM collections WHERE gamecode = ?');
                    $stmt->execute([$data['gamecode']]);
                } while ($stmt->fetchColumn() > 0);
            } else {
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM collections WHERE gamecode = ?');
                $stmt->execute([$data['gamecode']]);
                if ($stmt->fetchColumn() > 0) {
                    $error = 'Gamecode er allerede i bruk';
                }
            }

            // Ensure slug uniqueness if provided
            if (!$error && !empty($data['slug'])) {
                $slug = $data['slug'];
                $stmt = $pdo->query('SELECT data FROM collections');
                while ($row = $stmt->fetchColumn()) {
                    $existing = json_decode($row, true);
                    if (($existing['slug'] ?? '') === $slug) {
                        $error = 'Slug er allerede i bruk';
                        break;
                    }
                }
            }

            if (!$error) {
                $encoded = json_encode($data, JSON_UNESCAPED_UNICODE);
                // Validate against JSON Schema using AJV via node
                $schemaPath = realpath(__DIR__ . '/../../../docs/collection-schema.json');
                $gameSchemaPath = realpath(__DIR__ . '/../../../docs/game-schema.json');
                $tmpData = tempnam(sys_get_temp_dir(), 'col');
                file_put_contents($tmpData, $encoded);
                $cmd = 'node -e ' . escapeshellarg(
                    "const Ajv=require('ajv');const fs=require('fs');const ajv=new Ajv({allErrors:true});" .
                    "ajv.addSchema(JSON.parse(fs.readFileSync('$gameSchemaPath','utf8')),'game-schema.json');" .
                    "const schema=JSON.parse(fs.readFileSync('$schemaPath','utf8'));" .
                    "const data=JSON.parse(fs.readFileSync('$tmpData','utf8'));" .
                    "const validate=ajv.compile(schema);" .
                    "if(!validate(data)){console.error(JSON.stringify(validate.errors));process.exit(1);}"
                ) . ' 2>&1';
                exec($cmd, $out, $ret);
                unlink($tmpData);
                if ($ret !== 0) {
                    $error = 'Schema-validering feilet: ' . implode("\n", $out);
                } else {
                    $visibility = !empty($data['public']) ? 'public' : 'private';
                    $stmt = $pdo->prepare('INSERT INTO collections (gamecode, visibility, data) VALUES (?, ?, ?)');
                    $stmt->execute([$data['gamecode'], $visibility, $encoded]);
                    $newId = $pdo->lastInsertId();
                    log_audit($pdo, (int)($_SESSION['user_id'] ?? 0), 'collection_create', $data['gamecode']);
                    header('Location: edit.php?id=' . $newId);
                    exit;
                }
            }
        }
    }
}

$title = 'Importer Collection';
$page = 'collections';
$breadcrumbs = [
    ['label' => 'Samlinger', 'url' => '/admin/collections/'],
    ['label' => 'Importer']
];
$help = 'Last opp eller lim inn JSON for å importere en samling.';
admin_header(compact('title','page','breadcrumbs','help'));
?>
<h1>Importer Collection</h1>
<?php if ($error): ?><p style="color:red;white-space:pre;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <p><textarea name="json_text" rows="20" cols="80" placeholder="Lim inn JSON her"></textarea></p>
    <p>eller</p>
    <p><input type="file" name="json_file" accept="application/json" /></p>
    <p><button type="submit">Import</button></p>
</form>
<?php admin_footer(); ?>
