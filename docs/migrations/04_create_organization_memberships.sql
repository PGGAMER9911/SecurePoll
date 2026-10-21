-- Migration 04: Create Organization Memberships and Group Member Junction
-- Review Point 4: Cascade on organization deletion, unlink on user deletion, UNIQUE on (organization_id, user_id) for idempotency (Review Point 5)

CREATE TABLE IF NOT EXISTS organization_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NOT NULL,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    department_id INT NULL,
    status ENUM('pending', 'active', 'suspended', 'expired', 'removed') NOT NULL DEFAULT 'active',
    membership_number VARCHAR(100) NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_org_member_org FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    CONSTRAINT fk_org_member_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_org_member_role FOREIGN KEY (role_id) REFERENCES organization_roles(id) ON DELETE RESTRICT,
    CONSTRAINT fk_org_member_dept FOREIGN KEY (department_id) REFERENCES organization_departments(id) ON DELETE SET NULL,
    UNIQUE KEY uq_org_member (organization_id, user_id),
    KEY idx_member_user (user_id),
    KEY idx_member_role (role_id),
    KEY idx_member_dept (department_id),
    KEY idx_member_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS organization_group_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    member_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ogm_group FOREIGN KEY (group_id) REFERENCES organization_groups(id) ON DELETE CASCADE,
    CONSTRAINT fk_ogm_member FOREIGN KEY (member_id) REFERENCES organization_members(id) ON DELETE CASCADE,
    UNIQUE KEY uq_group_member (group_id, member_id),
    KEY idx_ogm_group (group_id),
    KEY idx_ogm_member (member_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
