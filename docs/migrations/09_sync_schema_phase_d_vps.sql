-- ============================================================================
-- Migration 09: Sync Schema Phase D (Preferences, Email Queue, Notifications, Polls)
-- Idempotent script for production database synchronization
-- ============================================================================

-- 1. Notification Preferences Table
CREATE TABLE IF NOT EXISTS `notification_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `notification_type` varchar(50) NOT NULL,
  `in_app` tinyint(1) DEFAULT 1,
  `email` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_pref` (`user_id`,`notification_type`),
  CONSTRAINT `notification_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Email Queue Table
CREATE TABLE IF NOT EXISTS `email_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to_email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body_html` text NOT NULL,
  `body_text` text DEFAULT NULL,
  `status` enum('pending','processing','sent','failed') DEFAULT 'pending',
  `attempts` int(11) DEFAULT 0,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Users Table Columns for Appearance and Presets
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `theme` enum('dark','light','system') DEFAULT 'system';
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `notification_preset` varchar(50) DEFAULT 'basic';

-- 4. Notifications Table Metadata Columns
ALTER TABLE `notifications` ADD COLUMN IF NOT EXISTS `category` varchar(50) NULL AFTER `user_id`;
ALTER TABLE `notifications` ADD COLUMN IF NOT EXISTS `icon` varchar(50) NULL AFTER `category`;
ALTER TABLE `notifications` ADD COLUMN IF NOT EXISTS `entity_type` varchar(50) NULL AFTER `is_read`;
ALTER TABLE `notifications` ADD COLUMN IF NOT EXISTS `entity_id` int(11) NULL AFTER `entity_type`;
ALTER TABLE `notifications` ADD COLUMN IF NOT EXISTS `action_type` varchar(50) NULL AFTER `entity_id`;
ALTER TABLE `notifications` ADD COLUMN IF NOT EXISTS `group_key` varchar(100) NULL AFTER `action_type`;
ALTER TABLE `notifications` ADD COLUMN IF NOT EXISTS `group_count` int(11) DEFAULT 1 AFTER `group_key`;

-- 5. Polls Status Enum Alignment
ALTER TABLE `polls` MODIFY COLUMN `status` enum('Draft','Scheduled','Active','Paused','Ended','Archived') DEFAULT 'Draft';

-- 6. Poll Invitations Status and Security Columns
ALTER TABLE `poll_invitations` MODIFY COLUMN `status` enum('Pending','Sent','Accepted','Declined','Revoked') DEFAULT 'Pending';
ALTER TABLE `poll_invitations` ADD COLUMN IF NOT EXISTS `expires_at` timestamp NULL AFTER `sent_at`;
ALTER TABLE `poll_invitations` ADD COLUMN IF NOT EXISTS `used_at` timestamp NULL AFTER `expires_at`;
ALTER TABLE `poll_invitations` ADD COLUMN IF NOT EXISTS `bound_device_token` varchar(255) NULL AFTER `used_at`;
