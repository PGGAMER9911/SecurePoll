

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) DEFAULT NULL,
    profile_image VARCHAR(255) DEFAULT NULL,
    college VARCHAR(150) DEFAULT NULL,
    department VARCHAR(100) DEFAULT NULL,
    course VARCHAR(100) DEFAULT NULL,
    semester INT DEFAULT NULL,
    enrollment_id VARCHAR(50) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    is_temporary BOOLEAN DEFAULT FALSE,
    must_set_password BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
);

CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE email_otps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    otp_code VARCHAR(255) NOT NULL,
    purpose VARCHAR(50) NOT NULL, -- e.g. registration, new_device, password_reset
    attempts INT DEFAULT 0,
    expires_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE device_fingerprints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    device_hash VARCHAR(255) NOT NULL,
    browser VARCHAR(100),
    os VARCHAR(100),
    user_agent TEXT,
    screen_resolution VARCHAR(50),
    language VARCHAR(20),
    timezone VARCHAR(50),
    ip_address VARCHAR(45),
    is_trusted BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE trusted_devices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    fingerprint_id INT NOT NULL,
    device_token_hash VARCHAR(64) NULL,
    expires_at TIMESTAMP NULL,
    trusted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (fingerprint_id) REFERENCES device_fingerprints(id) ON DELETE CASCADE,
    INDEX idx_user_token (user_id, device_token_hash)
);

CREATE TABLE login_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL UNIQUE,
    fingerprint_id INT,
    ip_address VARCHAR(45),
    is_active BOOLEAN DEFAULT TRUE,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (fingerprint_id) REFERENCES device_fingerprints(id) ON DELETE SET NULL
);

CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email VARCHAR(150) NOT NULL,
    token_hash VARCHAR(64) NOT NULL,
    expires_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    used_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token_hash (token_hash),
    INDEX idx_user_id (user_id)
);

-- ============================================================================
-- Organization Management System (OMS)
-- ============================================================================

CREATE TABLE organizations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    org_type ENUM('College / University', 'School', 'Company', 'Club / Student Community', 'Other') NOT NULL DEFAULT 'Other',
    owner_id INT NOT NULL,
    description TEXT NULL,
    logo_url VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_organizations_owner FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE RESTRICT,
    KEY idx_organizations_owner (owner_id),
    KEY idx_organizations_type (org_type),
    KEY idx_organizations_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE organization_roles (
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

CREATE TABLE organization_departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(50) NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_org_dept_org FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    UNIQUE KEY uq_dept_org_name (organization_id, name),
    KEY idx_dept_org (organization_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE organization_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NOT NULL,
    department_id INT NULL,
    name VARCHAR(150) NOT NULL,
    group_type ENUM('class', 'batch', 'club', 'team', 'committee', 'other') NOT NULL DEFAULT 'class',
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_org_group_org FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    CONSTRAINT fk_org_group_dept FOREIGN KEY (department_id) REFERENCES organization_departments(id) ON DELETE SET NULL,
    UNIQUE KEY uq_group_org_name (organization_id, name),
    KEY idx_group_org (organization_id),
    KEY idx_group_dept (department_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE organization_members (
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

CREATE TABLE organization_group_members (
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

CREATE TABLE polls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    creator_id INT NOT NULL,
    organization_id INT DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    banner_image VARCHAR(255),
    rules TEXT,
    language VARCHAR(20) DEFAULT 'en',
    visibility ENUM('Public', 'Private', 'Unlisted') DEFAULT 'Public',
    target_scope ENUM('organization_wide', 'departments', 'groups') DEFAULT 'organization_wide',
    voting_type ENUM('Single', 'Multiple', 'Yes/No', 'Rating') DEFAULT 'Single',
    is_anonymous BOOLEAN DEFAULT TRUE,
    result_visibility ENUM('Live', 'Hidden') DEFAULT 'Live',
    max_voters INT DEFAULT NULL,
    min_required_votes INT DEFAULT 0,
    start_time TIMESTAMP NULL,
    end_time TIMESTAMP NULL,
    auto_close BOOLEAN DEFAULT FALSE,
    status ENUM('Draft', 'Active', 'Closed') DEFAULT 'Draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_polls_org FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE SET NULL
);

CREATE TABLE poll_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poll_id INT NOT NULL,
    option_text VARCHAR(255) NOT NULL,
    option_image VARCHAR(255) DEFAULT NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE
);

CREATE TABLE eligible_voters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poll_id INT NOT NULL,
    email VARCHAR(150) NOT NULL,
    name VARCHAR(100) DEFAULT NULL,
    department VARCHAR(100) DEFAULT NULL,
    year_level VARCHAR(50) DEFAULT NULL,
    is_registered BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE,
    UNIQUE(poll_id, email)
);

CREATE TABLE poll_invitations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poll_id INT NOT NULL,
    email VARCHAR(150) NOT NULL,
    invitation_token VARCHAR(100) NOT NULL UNIQUE,
    status ENUM('Pending', 'Sent', 'Accepted') DEFAULT 'Pending',
    sent_at TIMESTAMP NULL,
    FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE
);

CREATE TABLE votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poll_id INT NOT NULL,
    option_id INT NOT NULL,
    user_id INT DEFAULT NULL, -- NULL if anonymous
    voter_hash VARCHAR(255) NOT NULL, -- Unique hash to prevent duplicates even if anonymous
    ip_address VARCHAR(45) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE,
    FOREIGN KEY (option_id) REFERENCES poll_options(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE(poll_id, voter_hash)
);

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100) NOT NULL,
    entity_id INT DEFAULT NULL,
    old_values JSON DEFAULT NULL,
    new_values JSON DEFAULT NULL,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE security_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    event_type VARCHAR(100) NOT NULL,
    severity ENUM('Low', 'Medium', 'High', 'Critical') DEFAULT 'Low',
    description TEXT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert initial roles
INSERT INTO roles (name) VALUES ('Super Admin'), ('Poll Creator'), ('Voter');

-- Typed poll targeting junction tables
CREATE TABLE poll_target_departments (
    poll_id INT NOT NULL,
    department_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (poll_id, department_id),
    CONSTRAINT fk_ptd_poll FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE,
    CONSTRAINT fk_ptd_dept FOREIGN KEY (department_id) REFERENCES organization_departments(id) ON DELETE CASCADE,
    KEY idx_ptd_poll (poll_id),
    KEY idx_ptd_dept (department_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE poll_target_groups (
    poll_id INT NOT NULL,
    group_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (poll_id, group_id),
    CONSTRAINT fk_ptg_poll FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE,
    CONSTRAINT fk_ptg_group FOREIGN KEY (group_id) REFERENCES organization_groups(id) ON DELETE CASCADE,
    KEY idx_ptg_poll (poll_id),
    KEY idx_ptg_group (group_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bulk Imports & Errors
CREATE TABLE organization_imports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NOT NULL,
    imported_by INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_hash VARCHAR(64) NOT NULL,
    total_rows INT NOT NULL DEFAULT 0,
    imported_count INT NOT NULL DEFAULT 0,
    updated_count INT NOT NULL DEFAULT 0,
    skipped_count INT NOT NULL DEFAULT 0,
    error_count INT NOT NULL DEFAULT 0,
    status ENUM('pending', 'processing', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_org_import_org FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    CONSTRAINT fk_org_import_user FOREIGN KEY (imported_by) REFERENCES users(id) ON DELETE CASCADE,
    KEY idx_org_import_org (organization_id),
    KEY idx_org_import_hash (organization_id, file_hash),
    KEY idx_org_import_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE organization_import_errors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    import_id INT NOT NULL,
    row_number INT NOT NULL,
    raw_data TEXT NULL,
    error_message VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_import_errors_import FOREIGN KEY (import_id) REFERENCES organization_imports(id) ON DELETE CASCADE,
    KEY idx_import_errors_import (import_id),
    KEY idx_import_errors_row (row_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Moderation Reports
CREATE TABLE moderation_reports (
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

-- Default System Role Templates
INSERT IGNORE INTO organization_roles (organization_id, name, slug, description, permissions, is_system_default) VALUES
(NULL, 'Owner', 'owner', 'Full organization ownership and management', '["*"]', 1),
(NULL, 'Administrator', 'admin', 'Manage members, departments, polls and settings', '["manage_members","manage_departments","manage_groups","manage_polls","view_reports","moderate_content"]', 1),
(NULL, 'Teacher / Professor', 'teacher', 'Create and manage department/group polls and view analytics', '["create_polls","manage_own_polls","view_poll_results","view_departments"]', 1),
(NULL, 'Student', 'student', 'Vote in targeted organization and class polls', '["vote_polls","view_own_history"]', 1),
(NULL, 'Member', 'member', 'General organization member', '["vote_polls","view_public_polls"]', 1);

