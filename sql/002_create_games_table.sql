-- Migration to create games table
CREATE TABLE IF NOT EXISTS games (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  visibility TINYINT(1) NOT NULL DEFAULT 1,
  featured_image VARCHAR(255) NULL,
  content TEXT NOT NULL
);
