-- Migration 05: Update Users Table for Temporary Accounts & OTP Login
-- Review Point 3: Safe authentication with nullable password_hash for OTP-only temporary accounts
-- Review Point 6: Zero data loss; existing users retain full access and credentials

ALTER TABLE users
    MODIFY COLUMN password_hash VARCHAR(255) NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS is_temporary TINYINT(1) NOT NULL DEFAULT 0 AFTER is_verified,
    ADD COLUMN IF NOT EXISTS must_set_password TINYINT(1) NOT NULL DEFAULT 0 AFTER is_temporary;
