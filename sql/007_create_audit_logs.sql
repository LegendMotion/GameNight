-- Migration to create audit_logs table
CREATE TABLE IF NOT EXISTS audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  action VARCHAR(100) NOT NULL,
  target VARCHAR(255),
  metadata JSON,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (user_id),
  INDEX (action),
  INDEX (created_at)
);

INSERT INTO migrations (script_name)
SELECT '007_create_audit_logs.sql'
WHERE NOT EXISTS (
    SELECT 1 FROM migrations WHERE script_name = '007_create_audit_logs.sql'
);
