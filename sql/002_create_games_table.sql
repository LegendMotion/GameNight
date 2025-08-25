-- Migration to create games table
CREATE TABLE IF NOT EXISTS games (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  visibility ENUM('hidden','private','public') NOT NULL DEFAULT 'public',
  featured_image VARCHAR(255) NULL,
  content TEXT NOT NULL,
  edit_token VARCHAR(64) DEFAULT NULL,
  token_expires_at DATETIME DEFAULT NULL
);

INSERT INTO migrations (script_name)
SELECT '002_create_games_table.sql'
WHERE NOT EXISTS (
    SELECT 1 FROM migrations WHERE script_name = '002_create_games_table.sql'
);
