-- Migration to create migrations tracking table
CREATE TABLE IF NOT EXISTS migrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  script_name VARCHAR(255) NOT NULL UNIQUE,
  applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO migrations (script_name)
SELECT '000_create_migrations_table.sql'
WHERE NOT EXISTS (
    SELECT 1 FROM migrations WHERE script_name = '000_create_migrations_table.sql'
);
