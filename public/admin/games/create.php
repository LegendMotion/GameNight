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
        $visibility = $_POST['visibility'] ?? 'public';
        if (!in_array($visibility, ['hidden','private','public'], true)) {
            $visibility = 'public';
        }
        $featured_image = null;
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
                $base = pathinfo($filename, PATHINFO_FILENAME);
                $destWebp = $uploadDir . $base . '.webp';
                $optimized = false;
                if (extension_loaded('gd') && function_exists('imagewebp')) {
                    $img = @imagecreatefromstring(file_get_contents($dest));
                    if ($img !== false) {
                        $w = imagesx($img);
                        $h = imagesy($img);
                        $max = 1200;
                        if ($w > $max || $h > $max) {
                            $ratio = min($max / $w, $max / $h);
                            $newW = (int)($w * $ratio);
                            $newH = (int)($h * $ratio);
                            $tmp = imagecreatetruecolor($newW, $newH);
                            imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);
                            imagedestroy($img);
                            $img = $tmp;
                        }
                        if (imagewebp($img, $destWebp, 80)) {
                            $optimized = true;
                        }
                        imagedestroy($img);
                    }
                } elseif (class_exists('Imagick')) {
                    try {
                        $img = new Imagick($dest);
                        $img->setImageFormat('webp');
                        $img->resizeImage(1200, 1200, Imagick::FILTER_LANCZOS, 1, true);
                        $img->setImageCompressionQuality(80);
                        $img->writeImage($destWebp);
                        $img->clear();
                        $img->destroy();
                        $optimized = true;
                    } catch (Exception $e) {
                        error_log('Image optimization failed: ' . $e->getMessage());
                    }
                } else {
                    error_log('Image optimization skipped: no GD or Imagick extension');
                }
                if ($optimized) {
                    unlink($dest);
                    $filename = $base . '.webp';
                    $dest = $destWebp;
                }
                $featured_image = '/uploads/games/' . $filename;
            }
        }
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
<form method="post" enctype="multipart/form-data">
<input type="text" name="title" placeholder="Tittel" />
<input type="text" name="slug" placeholder="slug" />
<select name="visibility">
    <option value="public">Synlig</option>
    <option value="private">Privat</option>
    <option value="hidden">Skjult</option>
</select>
<input type="file" name="featured_image" accept="image/*" />
<textarea name="content" placeholder="Innhold"></textarea>
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit">Lagre</button>
</form>
</body>
</html>
