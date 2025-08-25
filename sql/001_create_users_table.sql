-- Migration to create users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','editor','viewer') NOT NULL DEFAULT 'viewer'
);

INSERT INTO migrations (script_name)
SELECT '001_create_users_table.sql'
WHERE NOT EXISTS (
    SELECT 1 FROM migrations WHERE script_name = '001_create_users_table.sql'
);
