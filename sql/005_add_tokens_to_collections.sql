-- Migration to add edit_token and token_expires_at to collections
ALTER TABLE collections
  ADD COLUMN edit_token VARCHAR(64) DEFAULT NULL,
  ADD COLUMN token_expires_at DATETIME DEFAULT NULL;
