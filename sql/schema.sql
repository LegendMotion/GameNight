-- SQL schema for GameNight blog and collections
CREATE TABLE IF NOT EXISTS posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(100) NOT NULL UNIQUE,
  title VARCHAR(255) NOT NULL,
  type VARCHAR(20) NOT NULL,
  content TEXT NOT NULL,
  requirements TEXT NULL,
  ingredients TEXT NULL,
  featured_image VARCHAR(255) NULL,
  created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS collections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  gamecode VARCHAR(20) NOT NULL UNIQUE,
  data JSON NOT NULL
);
