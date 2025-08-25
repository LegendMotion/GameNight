<?php
use PHPUnit\Framework\TestCase;

class ImportHandlerTest extends TestCase
{
    private string $dbFile;

    protected function setUp(): void
    {
        $this->dbFile = tempnam(sys_get_temp_dir(), 'db');
        $dsn = 'sqlite:' . $this->dbFile;
        putenv('DB_DSN=' . $dsn);
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE collections (id INTEGER PRIMARY KEY AUTOINCREMENT, gamecode TEXT, data TEXT, visibility TEXT DEFAULT "public")');
        $pdo->exec('CREATE TABLE audit_logs (user_id INT, action TEXT, target TEXT, metadata TEXT)');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->dbFile)) {
            unlink($this->dbFile);
        }
    }

    public function testAssignsGamecodeWhenMissing(): void
    {
        $json = json_encode([
            'name' => 'auto_code_collection',
            'public' => true,
            'challenges' => []
        ], JSON_UNESCAPED_UNICODE);

        $code = 'define("UNIT_TEST", true); $_SERVER["REQUEST_METHOD"]="POST"; $_POST["json_text"]=' . var_export($json, true) .
            '; include "' . __DIR__ . '/../public/admin/collections/import.php";';

        $gamecode = trim(shell_exec('php -r ' . escapeshellarg($code)));

        $pdo = new PDO(getenv('DB_DSN'));
        $stored = $pdo->query("SELECT gamecode FROM collections WHERE data LIKE '%auto_code_collection%'")->fetchColumn();

        $this->assertNotEmpty($stored);
        $this->assertMatchesRegularExpression('/^[A-F0-9]{6}$/', $stored);
        $this->assertSame($stored, $gamecode);
    }

    public function testRejectsDuplicateGamecode(): void
    {
        $pdo = new PDO(getenv('DB_DSN'));
        $pdo->exec("INSERT INTO collections (gamecode, data, visibility) VALUES ('DUP123', '{\"name\":\"existing\",\"public\":true,\"challenges\":[]}', 'public')");

        $json = json_encode([
            'name' => 'another',
            'gamecode' => 'DUP123',
            'public' => true,
            'challenges' => []
        ], JSON_UNESCAPED_UNICODE);

        $code = 'define("UNIT_TEST", true); $_SERVER["REQUEST_METHOD"]="POST"; $_POST["json_text"]=' . var_export($json, true) .
            '; include "' . __DIR__ . '/../public/admin/collections/import.php";';

        $output = shell_exec('php -r ' . escapeshellarg($code));

        $this->assertStringContainsString('Gamecode er allerede i bruk', $output);
        $count = $pdo->query("SELECT COUNT(*) FROM collections WHERE gamecode = 'DUP123'")->fetchColumn();
        $this->assertSame(1, (int)$count);
    }
}
