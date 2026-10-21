-- Migration 06: Update Polls Table and Create Typed Targeting Junction Tables
-- Review Point 2: Replaces untyped polymorphic targeting with typed junction tables and foreign keys
-- Review Point 4: ON DELETE SET NULL on polls.organization_id preserves poll and vote history if organization is removed

ALTER TABLE polls
    ADD COLUMN IF NOT EXISTS organization_id INT NULL DEFAULT NULL AFTER creator_id,
    ADD COLUMN IF NOT EXISTS target_scope ENUM('organization_wide', 'departments', 'groups') NOT NULL DEFAULT 'organization_wide' AFTER visibility;

-- Add foreign key constraint if not already present
SET @fk_exists = (
    SELECT COUNT(*) 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
      AND CONSTRAINT_NAME = 'fk_polls_org' 
      AND TABLE_NAME = 'polls'
);

SET @sql_fk = IF(@fk_exists = 0, 
    'ALTER TABLE polls ADD CONSTRAINT fk_polls_org FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE SET NULL', 
    'SELECT 1'
);
PREPARE stmt FROM @sql_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS poll_target_departments (
    poll_id INT NOT NULL,
    department_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (poll_id, department_id),
    CONSTRAINT fk_ptd_poll FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE,
    CONSTRAINT fk_ptd_dept FOREIGN KEY (department_id) REFERENCES organization_departments(id) ON DELETE CASCADE,
    KEY idx_ptd_poll (poll_id),
    KEY idx_ptd_dept (department_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS poll_target_groups (
    poll_id INT NOT NULL,
    group_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (poll_id, group_id),
    CONSTRAINT fk_ptg_poll FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE,
    CONSTRAINT fk_ptg_group FOREIGN KEY (group_id) REFERENCES organization_groups(id) ON DELETE CASCADE,
    KEY idx_ptg_poll (poll_id),
    KEY idx_ptg_group (group_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
