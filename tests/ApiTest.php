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
        $pdo->exec('CREATE TABLE collections (gamecode TEXT, data TEXT, edit_token TEXT, token_expires_at TEXT)');
        $pdo->exec("INSERT INTO collections (gamecode, data) VALUES ('FEST12', '{\"name\":\"classic_party\"}')");
        $pdo->exec('CREATE TABLE posts (id INTEGER PRIMARY KEY AUTOINCREMENT, slug TEXT, title TEXT, type TEXT, content TEXT, requirements TEXT, ingredients TEXT, featured_image TEXT, visibility TEXT, created_at TEXT)');
        $pdo->exec("INSERT INTO posts (slug, title, type, content, requirements, ingredients, featured_image, visibility, created_at) VALUES ('hello', 'Hello', 'game', 'World', 'cards', NULL, 'image.png', 'public', '2023-01-01')");
        $pdo->exec("INSERT INTO posts (slug, title, type, content, requirements, ingredients, featured_image, visibility, created_at) VALUES ('drink', 'Drink', 'drink', 'Cheers', NULL, 'vodka', NULL, 'public', '2023-01-02')");
        $pdo->exec('CREATE TABLE games (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, slug TEXT, visibility TEXT, featured_image TEXT, content TEXT, edit_token TEXT, token_expires_at TEXT)');
        $pdo->exec("INSERT INTO games (title, slug, visibility, featured_image, content) VALUES ('Public Game', 'public-game', 'public', 'pub.png', 'hi')");
        $pdo->exec("INSERT INTO games (title, slug, visibility, featured_image, content) VALUES ('Hidden Game', 'hidden-game', 'hidden', NULL, 'secret')");
        $pdo->exec("INSERT INTO games (title, slug, visibility, featured_image, content, edit_token, token_expires_at) VALUES ('Private Game', 'private-game', 'private', NULL, 'priv', 'secrettoken', '2030-01-01 00:00:00')");
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
        $this->assertCount(2, $data);
        $types = array_column($data, 'type');
        $this->assertContains('game', $types);
        $this->assertContains('drink', $types);
    }

    public function testArticlesEndpointCanFilterByType(): void
    {
        $code = 'parse_str("type=drink", $_GET); include "' . __DIR__ . '/../public/api/articles.php";';
        $output = shell_exec('php -r ' . escapeshellarg($code));
        $data = json_decode($output, true);
        $this->assertCount(1, $data);
        $this->assertEquals('drink', $data[0]['type']);
    }

    public function testArticlesEndpointReturns404WhenNoPublicMatches(): void
    {
        $code = 'parse_str("type=unknown", $_GET); include "' . __DIR__ . '/../public/api/articles.php"; echo http_response_code();';
        $output = shell_exec('php -r ' . escapeshellarg($code));
        $output = trim($output);
        $status = (int) substr($output, -3);
        $data = json_decode(substr($output, 0, -3), true);
        $this->assertSame([], $data);
        $this->assertSame(404, $status);
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

    public function testGamesEndpointFiltersVisibility(): void
    {
        $code = 'include "' . __DIR__ . '/../public/api/games.php";';
        $output = shell_exec('php -r ' . escapeshellarg($code));
        $data = json_decode($output, true);
        $this->assertCount(1, $data);
        $this->assertEquals('public-game', $data[0]['slug']);
    }

    public function testGameEndpointRespectsVisibility(): void
    {
        $code = 'parse_str("slug=hidden-game", $_GET); include "' . __DIR__ . '/../public/api/game.php";';
        $output = shell_exec('php -r ' . escapeshellarg($code));
        $data = json_decode($output, true);
        $this->assertEquals('Game not found', $data['error']);

        $code = 'parse_str("slug=private-game", $_GET); include "' . __DIR__ . '/../public/api/game.php";';
        $output = shell_exec('php -r ' . escapeshellarg($code));
        $data = json_decode($output, true);
        $this->assertEquals('Access denied', $data['error']);

        $code = 'parse_str("slug=private-game&token=secrettoken", $_GET); include "' . __DIR__ . '/../public/api/game.php";';
        $output = shell_exec('php -r ' . escapeshellarg($code));
        $data = json_decode($output, true);
        $this->assertEquals('Private Game', $data['title']);
    }
}
