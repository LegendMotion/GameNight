<?php
// GameNight update script
if (!file_exists(__DIR__.'/../installed.lock')) {
    exit('Application is not installed.');
}

$versionFile = __DIR__.'/../version.txt';
$currentVersion = is_file($versionFile) ? trim(file_get_contents($versionFile)) : 'unknown';

function parseEnv($path) {
    $vars = [];
    if (!is_file($path)) return $vars;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        [$k, $v] = array_map('trim', explode('=', $line, 2));
        $vars[$k] = $v;
    }
    return $vars;
}

function recurseCopy($src, $dst, $ignore = []) {
    $dir = opendir($src);
    @mkdir($dst, 0777, true);
    while (($file = readdir($dir)) !== false) {
        if ($file === '.' || $file === '..' || in_array($file, $ignore)) continue;
        $srcPath = "$src/$file";
        $dstPath = "$dst/$file";
        if (is_dir($srcPath)) {
            recurseCopy($srcPath, $dstPath, $ignore);
        } else {
            copy($srcPath, $dstPath);
        }
    }
    closedir($dir);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $repo = 'YourGitHubUser/GameNight'; // change to your repository
    $ctx = stream_context_create(['http' => ['user_agent' => 'PHP']]);
    $api = "https://api.github.com/repos/$repo/releases/latest";
    $json = json_decode(file_get_contents($api, false, $ctx), true);
    $zipUrl = $json['zipball_url'] ?? null;
    $latestVersion = $json['tag_name'] ?? '';
    if (!$zipUrl) {
        exit('Could not fetch release information.');
    }

    $tmpZip = tempnam(sys_get_temp_dir(), 'gn').'.zip';
    file_put_contents($tmpZip, file_get_contents($zipUrl, false, $ctx));
    $tmpDir = sys_get_temp_dir().'/gn_update_'.uniqid();
    mkdir($tmpDir);
    $zip = new ZipArchive();
    if ($zip->open($tmpZip) === true) {
        $zip->extractTo($tmpDir);
        $zip->close();
    } else {
        exit('Could not extract update.');
    }

    $backupDir = __DIR__.'/../backup_'.date('Ymd_His');
    recurseCopy(__DIR__.'/..', $backupDir, ['vendor','node_modules','backup']);

    $extractedRoot = glob($tmpDir.'/*')[0] ?? $tmpDir;
    recurseCopy($extractedRoot, __DIR__.'/..', ['.env','installed.lock','backup']);
    if (file_exists($backupDir.'/.env')) {
        copy($backupDir.'/.env', __DIR__.'/../.env');
    }

    shell_exec('cd .. && composer install --no-dev 2>&1');

    $env = parseEnv(__DIR__.'/../.env');
    try {
        $dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset=utf8mb4";
        $pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASS'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        foreach (glob(__DIR__.'/../sql/*.sql') as $file) {
            $pdo->exec(file_get_contents($file));
        }
    } catch (Throwable $e) {
        echo 'Database update failed: '.htmlspecialchars($e->getMessage());
    }

    if ($latestVersion) {
        file_put_contents($versionFile, $latestVersion);
    }

    echo 'Update complete. Remove update.php for security.';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>GameNight Update</title>
</head>
<body>
<h1>GameNight Update</h1>
<p>Current version: <?=htmlspecialchars($currentVersion)?>.</p>
<form method="post">
    <button type="submit">Update to latest version</button>
</form>
</body>
</html>
