# SecurePoll Database Schema & Architecture Documentation

## Overview

SecurePoll uses a MySQL/MariaDB database designed for privacy, auditable voting, and multi-tenant organization management. The schema supports individual voters and creators alongside hierarchical organizations (Universities, Colleges, Schools, Companies, and Communities).

- **Active Database**: `securepoll_db`
- **Total Tables**: 28
- **Engine**: InnoDB (UTF-8 `utf8mb4_unicode_ci`)
- **Migration Runner**: `docs/migrations/run_migrations.php`
- **Rollback Script**: `docs/migrations/rollback_all.sql`
- **Backup Location**: `storage/backups/securepoll_db_backup_20261012.sql`

---

## Architectural Review Points & Design Decisions

### 1. MySQL NULL Behavior in Unique Constraints (Default Organization Roles)
- **Challenge**: Standard SQL and MySQL/MariaDB treat `NULL` values as distinct in `UNIQUE` indexes. If `organization_id IS NULL` represents system default roles (templates), a unique index on `(organization_id, slug)` would allow duplicate default roles with the same slug.
- **Solution**: Implemented a virtual stored generated column:
  ```sql
  org_id_non_null INT GENERATED ALWAYS AS (IFNULL(organization_id, 0)) STORED,
  UNIQUE KEY uq_org_role_slug (org_id_non_null, slug)
  ```
  This projects `NULL` organization IDs to `0`, strictly guaranteeing that system-wide default role slugs (`owner`, `admin`, `teacher`, `student`, `member`) are unique.

### 2. Referential Integrity Between Polls, Departments, and Groups
- **Challenge**: Polymorphic targeting (`target_type`, `target_id`) loses foreign key integrity, cascade guarantees, and query optimization.
- **Solution**: Replaced polymorphic targeting with strongly-typed junction tables:
  - `poll_target_departments` (`poll_id`, `department_id`)
  - `poll_target_groups` (`poll_id`, `group_id`)
  Both tables utilize composite primary keys and `ON DELETE CASCADE` foreign keys to enforce strict referential integrity.

### 3. Safe Authentication Behavior when `password_hash` is NULL
- **Challenge**: Temporary organization accounts created via bulk import or email OTP do not have a pre-existing password (`password_hash IS NULL`).
- **Solution**:
  - `users.password_hash` is nullable (`VARCHAR(255) NULL DEFAULT NULL`).
  - Added flags: `is_temporary TINYINT(1) DEFAULT 0` and `must_set_password TINYINT(1) DEFAULT 0`.
  - Audited `app/Services/AuthService.php` login check:
    ```php
    if (!$user || empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Invalid email or password.'];
    }
    ```
    This securely rejects password logins when `password_hash` is `NULL` or empty, preventing any authentication bypass or PHP 8.1+ deprecation warnings.
  - In `app/Controllers/SettingsController.php`, users with `must_set_password = 1` can set an initial password without providing a current password.

### 4. Foreign-Key Deletion Behavior & Ownership Transfer
- **Organizations & Ownership**:
  - `organizations.owner_id`: `FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE RESTRICT`. Prevents accidental deletion of organization owners while the organization exists, mandating explicit ownership transfer before account deletion.
- **Polls & Votes**:
  - `polls.organization_id`: `FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE SET NULL`. If an organization is removed, historical polls and individual vote records are preserved intact.
- **Memberships & Groups**:
  - `organization_members.organization_id`: `ON DELETE CASCADE`.
  - `organization_members.user_id`: `ON DELETE CASCADE`.
  - `organization_members.role_id`: `ON DELETE RESTRICT`.
  - `organization_departments.organization_id`: `ON DELETE CASCADE`.
  - `organization_groups.organization_id`: `ON DELETE CASCADE`.
  - `organization_groups.department_id`: `ON DELETE SET NULL`.

### 5. Import Idempotency, Duplicate Emails & Existing Account Linking
- **Idempotency**:
  - `organization_imports` stores a SHA-256 `file_hash` per organization to detect duplicate identical imports before processing.
  - `organization_members` enforces `UNIQUE KEY uq_org_member (organization_id, user_id)`. Re-running an import will not duplicate memberships.
- **Existing User Linking**:
  - When importing a CSV containing an email already registered in `users`, the import process associates the existing `user_id` with `organization_members` without creating a duplicate user row.
- **Error Tracking**:
  - `organization_import_errors` captures granular row-by-row errors (`row_number`, `raw_data`, `error_message`) with `ON DELETE CASCADE` to the parent import record.

### 6. Backward Compatibility with Existing Data
- **Zero Data Loss**: Migrations altered existing tables (`users`, `polls`) strictly through non-destructive `ADD COLUMN IF NOT EXISTS` and `MODIFY COLUMN` statements.
- **Baseline Integrity**:
  - `users`: 12 rows retained
  - `polls`: 8 rows retained
  - `votes`: 9 rows retained
  - `poll_options`: 14 rows retained
  - `poll_invitations`: 2 rows retained
  - `notifications`: 1 row retained
  - `audit_logs`: 108 rows retained
  - `security_logs`: 198 rows retained

---

## Table Inventory (28 Tables)

### Core Authentication & Users
1. `roles` — Global platform roles (`Super Admin`, `Poll Creator`, `Voter`).
2. `users` — User accounts with support for regular and temporary OTP-based profiles.
3. `login_sessions` — Active user sessions with device association and expiration.
4. `device_fingerprints` — Hardware and browser fingerprint telemetry.
5. `trusted_devices` — Verified trusted devices per user.
6. `email_otps` — Time-limited one-time passwords for verification, login, and resets.

### Polling System
7. `polls` — Poll records supporting global, private, and organization-scoped targeting.
8. `poll_options` — Choice options per poll.
9. `eligible_voters` — Whitelist entries for private poll invitations.
10. `poll_invitations` — Tokenized poll participation invites.
11. `votes` — Anonymized or authenticated ballots with SHA-256 voter hash uniqueness.

### Organization Management System (OMS)
12. `organizations` — Tenant organizations (Universities, Schools, Companies, Communities).
13. `organization_roles` — Organization-level roles and permission sets (`owner`, `admin`, `teacher`, `student`, `member`).
14. `organization_departments` — Departments/Faculties within an organization.
15. `organization_groups` — Classes, batches, clubs, or committees.
16. `organization_members` — User memberships within organizations with status, expiry, and department mapping.
17. `organization_group_members` — Junction table associating members with groups.
18. `poll_target_departments` — Junction table restricting poll visibility to specific departments.
19. `poll_target_groups` — Junction table restricting poll visibility to specific groups.
20. `organization_imports` — Tracking batches for CSV/Excel bulk member imports.
21. `organization_import_errors` — Detailed row-level errors for import audits.

### Moderation & Auditing
22. `moderation_reports` — User-submitted reports for inappropriate polls, comments, or accounts.
23. `audit_logs` — Auditable event log tracking entity changes and administrative actions.
24. `security_logs` — Security event log (failed logins, new devices, suspicious activity).

### Notifications & System
25. `notifications` — In-app alerts and notifications.
26. `notification_preferences` — Per-user delivery channels and alert categories.
27. `email_queue` — Asynchronous email delivery queue.
28. `settings` — System-wide configuration key-value store.

---

## Migration Procedures

### Running Migrations
Run the master migration script using PHP CLI:
```powershell
d:\xampp\php\php.exe docs/migrations/run_migrations.php
```

### Performing a Full Rollback
If a complete rollback is ever required:
```powershell
d:\xampp\mysql\bin\mysql.exe -u root securepoll_db < docs/migrations/rollback_all.sql
```
