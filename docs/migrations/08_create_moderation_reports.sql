-- Migration 08: Create Moderation Reports Table
-- Allows reporting inappropriate polls, options, comments, or behavior within organizations or globally

CREATE TABLE IF NOT EXISTS moderation_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NULL,
    reporter_id INT NOT NULL,
    reported_entity_type ENUM('poll', 'poll_option', 'user', 'comment', 'organization') NOT NULL,
    reported_entity_id INT NOT NULL,
    reason VARCHAR(100) NOT NULL,
    details TEXT NULL,
    status ENUM('pending', 'in_review', 'resolved', 'dismissed') NOT NULL DEFAULT 'pending',
    resolution_notes TEXT NULL,
    reviewed_by INT NULL,
    reviewed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_mod_rep_org FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE SET NULL,
    CONSTRAINT fk_mod_rep_reporter FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_mod_rep_reviewer FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    KEY idx_mod_org (organization_id),
    KEY idx_mod_reporter (reporter_id),
    KEY idx_mod_status (status),
    KEY idx_mod_entity (reported_entity_type, reported_entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
