-- Migration to add visibility to collections
ALTER TABLE collections
  ADD COLUMN visibility ENUM('public','private','hidden') NOT NULL DEFAULT 'public';
