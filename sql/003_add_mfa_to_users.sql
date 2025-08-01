-- Migration to add MFA secret column to users
ALTER TABLE users ADD COLUMN mfa_secret VARCHAR(255) DEFAULT NULL;
