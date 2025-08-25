-- SQL schema for GameNight blog and collections
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','editor','viewer') NOT NULL DEFAULT 'viewer',
  mfa_secret VARCHAR(255) DEFAULT NULL,
  mfa_enabled TINYINT(1) NOT NULL DEFAULT 0
);

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

CREATE TABLE IF NOT EXISTS collections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  gamecode VARCHAR(20) NOT NULL UNIQUE,
  visibility ENUM('public','private','hidden') NOT NULL DEFAULT 'public',
  data JSON NOT NULL,
  edit_token VARCHAR(64) DEFAULT NULL,
  token_expires_at DATETIME DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS settings (
  name VARCHAR(255) PRIMARY KEY,
  value TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS settings_audit (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  old_value TEXT,
  new_value TEXT NOT NULL,
  changed_by INT NOT NULL,
  changed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
