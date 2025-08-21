<?php
$requireRole = ['admin','editor'];
require_once '../layout.php';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require_once __DIR__ . '/../../api/db.php';
require_once __DIR__ . '/../../api/audit_log.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    exit('ID mangler');
}

$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
$stmt->execute([$id]);
$article = $stmt->fetch();
if (!$article) {
    exit('Artikkel ikke funnet');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Ugyldig CSRF-token';
    } else {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $type = $_POST['type'] ?? 'game';
        if (!in_array($type, ['drink','game'], true)) {
            $type = 'game';
        }
        $requirements = trim($_POST['requirements'] ?? '');
        $ingredients = trim($_POST['ingredients'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $featured_image = $article['featured_image'];
        if (!empty($_FILES['featured_image']['name']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../uploads/articles/';
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
                $featured_image = '/uploads/articles/' . $filename;
            }
        }
        if ($title && $slug && $content) {
            if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
                $error = 'Slug kan kun inneholde små bokstaver, tall og bindestrek';
            } else {
                $check = $pdo->prepare('SELECT COUNT(*) FROM posts WHERE slug = ? AND id != ?');
                $check->execute([$slug, $id]);
                if ($check->fetchColumn() > 0) {
                    $error = 'Slug finnes allerede';
                } else {
                    $sql = 'UPDATE posts SET slug = ?, title = ?, type = ?, content = ?, requirements = ?, ingredients = ?, featured_image = ? WHERE id = ?';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$slug, $title, $type, $content, $requirements, $ingredients, $featured_image, $id]);
                    $message = 'Oppdatert!';
                    log_audit($pdo, (int)$_SESSION['user_id'], 'article_update', $slug);
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
                    $stmt->execute([$id]);
                    $article = $stmt->fetch();
                }
            }
        } else {
            $error = 'Alle felt må fylles';
        }
    }
}

$title = 'Rediger artikkel';
$page = 'articles';
$breadcrumbs = [
    ['url' => '/admin/articles/', 'label' => 'Artikler'],
    ['label' => 'Rediger']
];
$help = 'Oppdater artikkeldetaljer.';
admin_header(compact('title','page','breadcrumbs','help'));
?>
<h1>Rediger artikkel</h1>
<?php if (!empty($message)): ?><p style="color:green;"><?php echo $message; ?></p><?php endif; ?>
<?php if (!empty($error)): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data">
<input type="text" name="title" placeholder="Tittel" value="<?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?>" />
<input type="text" name="slug" placeholder="slug" value="<?php echo htmlspecialchars($article['slug'], ENT_QUOTES, 'UTF-8'); ?>" />
<select name="type">
    <option value="game"<?php if ($article['type'] === 'game') echo ' selected'; ?>>Spill</option>
    <option value="drink"<?php if ($article['type'] === 'drink') echo ' selected'; ?>>Drink</option>
</select>
<textarea name="requirements" placeholder="Krav"><?php echo htmlspecialchars($article['requirements'], ENT_QUOTES, 'UTF-8'); ?></textarea>
<textarea name="ingredients" placeholder="Ingredienser"><?php echo htmlspecialchars($article['ingredients'], ENT_QUOTES, 'UTF-8'); ?></textarea>
<input type="file" name="featured_image" accept="image/*" />
<textarea name="content" placeholder="Innhold"><?php echo htmlspecialchars($article['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit">Oppdater</button>
</form>
<?php admin_footer(); ?>
