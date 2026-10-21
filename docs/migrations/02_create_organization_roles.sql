-- Migration 02: Create Organization-Scoped Roles and Permissions Table
-- Review Point 1: Solves MySQL/MariaDB NULL behavior in UNIQUE constraints via generated column org_id_non_null

CREATE TABLE IF NOT EXISTS organization_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(50) NOT NULL,
    description VARCHAR(255) NULL,
    permissions LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL CHECK (json_valid(permissions)),
    is_system_default TINYINT(1) NOT NULL DEFAULT 0,
    org_id_non_null INT GENERATED ALWAYS AS (IFNULL(organization_id, 0)) STORED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_org_roles_org FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    UNIQUE KEY uq_org_role_slug (org_id_non_null, slug),
    KEY idx_org_roles_org (organization_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default system role templates (organization_id IS NULL)
INSERT IGNORE INTO organization_roles (organization_id, name, slug, description, permissions, is_system_default) VALUES
(NULL, 'Owner', 'owner', 'Full organization ownership and management', '["*"]', 1),
(NULL, 'Administrator', 'admin', 'Manage members, departments, polls and settings', '["manage_members","manage_departments","manage_groups","manage_polls","view_reports","moderate_content"]', 1),
(NULL, 'Teacher / Professor', 'teacher', 'Create and manage department/group polls and view analytics', '["create_polls","manage_own_polls","view_poll_results","view_departments"]', 1),
(NULL, 'Student', 'student', 'Vote in targeted organization and class polls', '["vote_polls","view_own_history"]', 1),
(NULL, 'Member', 'member', 'General organization member', '["vote_polls","view_public_polls"]', 1);
