<?php
use PHPUnit\Framework\TestCase;

class SitemapTest extends TestCase
{
    private string $dbFile;

    protected function setUp(): void
    {
        $this->dbFile = tempnam(sys_get_temp_dir(), 'db');
        $dsn = 'sqlite:' . $this->dbFile;
        putenv('DB_DSN=' . $dsn);
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE posts (id INTEGER PRIMARY KEY AUTOINCREMENT, slug TEXT, title TEXT, type TEXT, content TEXT, requirements TEXT, ingredients TEXT, featured_image TEXT, visibility TEXT, created_at TEXT)');
        $pdo->exec("INSERT INTO posts (slug, title, type, content, requirements, ingredients, featured_image, visibility, created_at) VALUES ('hello', 'Hello', 'game', 'World', 'cards', NULL, 'image.png', 'public', '2023-01-01')");
        $pdo->exec('CREATE TABLE settings (name TEXT, value TEXT)');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->dbFile)) {
            unlink($this->dbFile);
        }
    }

    public function testSitemapIncludesPostsAndCollections(): void
    {
        $code = 'include "' . __DIR__ . '/../public/sitemap.php";';
        $output = shell_exec('php -r ' . escapeshellarg($code));
        $this->assertStringContainsString('<loc>https://example.com/blog/post.php?slug=hello</loc>', $output);
        $this->assertStringContainsString('/data/collections/FEST123.json', $output);
    }

    public function testRobotsTxtDefaultAndOverride(): void
    {
        $code = 'include "' . __DIR__ . '/../public/robots.php";';
        $output = shell_exec('php -r ' . escapeshellarg($code));
        $this->assertStringContainsString('Sitemap: /sitemap.xml', $output);

        $pdo = new PDO('sqlite:' . $this->dbFile);
        $pdo->exec("INSERT INTO settings (name, value) VALUES ('seo_robots_txt', 'User-agent: *\nDisallow: /secret')");
        $output = shell_exec('php -r ' . escapeshellarg($code));
        $this->assertStringContainsString('Disallow: /secret', $output);
    }
}
