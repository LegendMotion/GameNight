<?php

use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    private string $dbFile;

    protected function setUp(): void
    {
        $this->dbFile = tempnam(sys_get_temp_dir(), 'db');
        $dsn = 'sqlite:' . $this->dbFile;
        putenv('DB_DSN=' . $dsn);

        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE collections (gamecode TEXT, data TEXT)');
        $pdo->exec("INSERT INTO collections (gamecode, data) VALUES ('FEST12', '{\"name\":\"classic_party\"}')");
        $pdo->exec('CREATE TABLE posts (id INTEGER PRIMARY KEY AUTOINCREMENT, slug TEXT, title TEXT, type TEXT, content TEXT, requirements TEXT, ingredients TEXT, featured_image TEXT, created_at TEXT)');
        $pdo->exec("INSERT INTO posts (slug, title, type, content, requirements, ingredients, featured_image, created_at) VALUES ('hello', 'Hello', 'game', 'World', 'cards', NULL, 'image.png', '2023-01-01')");
    }

    protected function tearDown(): void
    {
        if (file_exists($this->dbFile)) {
            unlink($this->dbFile);
        }
    }

    public function testCollectionEndpointReturnsData(): void
    {
        $code = 'parse_str("gamecode=FEST12", $_GET); include "' . __DIR__ . '/../public/api/collection.php";';
        $output = shell_exec('php -r ' . escapeshellarg($code));
        $this->assertSame('{"name":"classic_party"}', trim($output));
    }

    public function testArticlesEndpointReturnsList(): void
    {
        $code = 'include "' . __DIR__ . '/../public/api/articles.php";';
        $output = shell_exec('php -r ' . escapeshellarg($code));
        $data = json_decode($output, true);
        $this->assertEquals('game', $data[0]['type']);
    }

    public function testArticleEndpointReturnsArticle(): void
    {
        $code = 'parse_str("slug=hello", $_GET); include "' . __DIR__ . '/../public/api/article.php";';
        $output = shell_exec('php -r ' . escapeshellarg($code));
        $data = json_decode($output, true);
        $this->assertEquals('Hello', $data['title']);
        $this->assertEquals('game', $data['type']);
        $this->assertEquals('cards', $data['requirements']);
        $this->assertEquals('image.png', $data['featured_image']);
    }
}
