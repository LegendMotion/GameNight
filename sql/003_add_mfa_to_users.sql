-- Migration to add MFA columns to users
ALTER TABLE users ADD COLUMN mfa_secret VARCHAR(255) DEFAULT NULL;
ALTER TABLE users ADD COLUMN mfa_enabled TINYINT(1) NOT NULL DEFAULT 0;
