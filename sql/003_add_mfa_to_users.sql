-- Migration to add MFA columns to users
ALTER TABLE users ADD COLUMN IF NOT EXISTS mfa_secret VARCHAR(255) DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS mfa_enabled TINYINT(1) NOT NULL DEFAULT 0;

INSERT INTO migrations (script_name)
SELECT '003_add_mfa_to_users.sql'
WHERE NOT EXISTS (
    SELECT 1 FROM migrations WHERE script_name = '003_add_mfa_to_users.sql'
);
