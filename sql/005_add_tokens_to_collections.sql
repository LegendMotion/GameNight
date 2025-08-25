-- Migration to add edit_token and token_expires_at to collections
ALTER TABLE collections
  ADD COLUMN IF NOT EXISTS edit_token VARCHAR(64) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS token_expires_at DATETIME DEFAULT NULL;

INSERT INTO migrations (script_name)
SELECT '005_add_tokens_to_collections.sql'
WHERE NOT EXISTS (
    SELECT 1 FROM migrations WHERE script_name = '005_add_tokens_to_collections.sql'
);
