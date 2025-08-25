<?php
// GameNight update script

// Security: require secret token or IP allowlist
$clientIp = $_SERVER['REMOTE_ADDR'] ?? '';
$allowedIps = array_filter(array_map('trim', explode(',', getenv('SETUP_ALLOWED_IPS') ?: '')));
$secretToken = getenv('SETUP_SECRET_TOKEN') ?: '';
$providedToken = $_SERVER['HTTP_X_SETUP_TOKEN'] ?? ($_GET['token'] ?? '');

$authorized = false;
if ($secretToken && hash_equals($secretToken, $providedToken)) {
    $authorized = true;
} elseif ($allowedIps && in_array($clientIp, $allowedIps, true)) {
    $authorized = true;
}
if (!$authorized) {
    http_response_code(403);
    exit('Forbidden');
}

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

    try {
        $extractedRoot = glob($tmpDir.'/*')[0] ?? $tmpDir;
        recurseCopy(
            $extractedRoot,
            __DIR__.'/..',
            ['.env','installed.lock','backup','install.php','update.php']
        );
        if (file_exists($backupDir.'/.env')) {
            copy($backupDir.'/.env', __DIR__.'/../.env');
        }

        $output = [];
        $returnVar = 0;
        exec('cd .. && composer install --no-dev 2>&1', $output, $returnVar);
        if ($returnVar !== 0) {
            throw new RuntimeException("Composer install failed:\n".implode("\n", $output));
        }

        $env = parseEnv(__DIR__.'/../.env');
        try {
            $dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset=utf8mb4";
            $pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASS'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            foreach (glob(__DIR__.'/../sql/*.sql') as $file) {
                $pdo->exec(file_get_contents($file));
            }
        } catch (Throwable $e) {
            throw new RuntimeException('Database update failed: '.$e->getMessage(), 0, $e);
        }

        if ($latestVersion) {
            file_put_contents($versionFile, $latestVersion);
        }

        // Log and delete this script for security
        $logDir = __DIR__.'/../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        file_put_contents($logDir.'/update.log', date('c')." update from $clientIp\n", FILE_APPEND);
        $deleted = @unlink(__FILE__);
        if ($deleted && !file_exists(__FILE__)) {
            echo 'Update complete. update.php removed.';
        } else {
            echo 'Update complete. Please remove update.php for security.';
        }
    } catch (Throwable $e) {
        recurseCopy($backupDir, __DIR__.'/..');
        echo 'Update failed: '.htmlspecialchars($e->getMessage());
    }
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
