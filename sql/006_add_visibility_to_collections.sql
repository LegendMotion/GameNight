-- Migration to add visibility to collections
ALTER TABLE collections
  ADD COLUMN IF NOT EXISTS visibility ENUM('public','private','hidden') NOT NULL DEFAULT 'public';

INSERT INTO migrations (script_name)
SELECT '006_add_visibility_to_collections.sql'
WHERE NOT EXISTS (
    SELECT 1 FROM migrations WHERE script_name = '006_add_visibility_to_collections.sql'
);
