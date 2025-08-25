-- Migration to create settings and settings_audit tables
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

INSERT INTO migrations (script_name)
SELECT '004_create_settings_tables.sql'
WHERE NOT EXISTS (
    SELECT 1 FROM migrations WHERE script_name = '004_create_settings_tables.sql'
);
