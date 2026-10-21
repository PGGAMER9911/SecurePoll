-- Rollback Plan for Organization Management System Migrations
-- WARNING: This script drops newly created organization entities and reverts schema additions.
-- It should ONLY be executed if a full rollback is explicitly requested.

-- 1. Drop Moderation Reports
DROP TABLE IF EXISTS moderation_reports;

-- 2. Drop Organization Imports and Errors
DROP TABLE IF EXISTS organization_import_errors;
DROP TABLE IF EXISTS organization_imports;

-- 3. Drop Poll Targeting Junctions and Remove Poll Columns
DROP TABLE IF EXISTS poll_target_groups;
DROP TABLE IF EXISTS poll_target_departments;

SET @fk_exists = (
    SELECT COUNT(*) 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
      AND CONSTRAINT_NAME = 'fk_polls_org' 
      AND TABLE_NAME = 'polls'
);
SET @sql_fk = IF(@fk_exists > 0, 'ALTER TABLE polls DROP FOREIGN KEY fk_polls_org', 'SELECT 1');
PREPARE stmt FROM @sql_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

ALTER TABLE polls 
    DROP COLUMN IF EXISTS organization_id,
    DROP COLUMN IF EXISTS target_scope;

-- 4. Revert Users Columns
ALTER TABLE users 
    DROP COLUMN IF EXISTS must_set_password,
    DROP COLUMN IF EXISTS is_temporary;

-- 5. Drop Membership Tables
DROP TABLE IF EXISTS organization_group_members;
DROP TABLE IF EXISTS organization_members;

-- 6. Drop Departments and Groups
DROP TABLE IF EXISTS organization_groups;
DROP TABLE IF EXISTS organization_departments;

-- 7. Drop Organization Roles
DROP TABLE IF EXISTS organization_roles;

-- 8. Drop Organizations
DROP TABLE IF EXISTS organizations;
