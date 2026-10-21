-- Migration 01: Create Organizations Table
-- Creates organizations entity with owner reference and tenant metadata

CREATE TABLE IF NOT EXISTS organizations (
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
