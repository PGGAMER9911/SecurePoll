# Phase 3 Report: Poll Management & Voting System

## 1. Implementation Overview
The Phase 3 Poll Management & Voting System has been completely audited, refactored, and finalized to meet rigorous production standards. We coordinated changes across backend, database, security, and UI/UX engineering domains.

### Files Created & Modified
- **[NEW] `app/Models/PollInvitation.php`**: Handled DB logic for unique invitation tokens.
- **[NEW] `app/Views/polls/manage.php`**: Built a dedicated UI dashboard for Poll Creators to track stats and upload CSV files for bulk voters.
- **[MODIFY] `app/Services/PollService.php`**: Fully rewrote `castVote` and `createPoll`. Added `importCsv` functionality.
- **[MODIFY] `app/Controllers/PollController.php`**: Added JSON endpoints `importCsv` and `manage`.
- **[MODIFY] `app/Views/polls/create.php` & `show.php`**: Adopted SaaS UI/UX design, handled `Scheduled`, `Draft`, and `Archived` statuses gracefully.
- **[NEW] `migrate_phase3.php`**: Migrated schema Enums.

## 2. Database Changes
- **Migration**: Modified the `polls.status` enum from `('Draft', 'Active', 'Closed')` to `('Draft', 'Scheduled', 'Active', 'Paused', 'Ended', 'Archived')` via SQL `ALTER TABLE`.
- **Transactions**: Configured `Database.php` to leverage PDO atomic transactions `beginTransaction()`, `commit()`, and `rollBack()`.

## 3. Security Decisions

### Duplicate Voting & Anonymity (Crucial Fix)
The original approach of using `ip_address` to prevent duplicate anonymous votes was severely flawed because shared networks (colleges, hostels, corporations) share a single public IP. 

**The Solution:**
- Instead of using IP as the identity, the system now sets a 10-year persistent, securely generated cryptographic token in the user's browser `$_COOKIE['sp_device_token']`.
- The duplicate prevention hash (`voter_hash`) is generated via: `hash('sha256', poll_id + device_token + user_agent)`.
- **Result:** An entire college can vote from the same hostel IP address, but no individual device/browser can vote twice. The database UNIQUE constraint provides **Database-level duplicate prevention with device-based tracking.**
- IP Addresses are now only logged in `security_logs` to detect automated abuse (e.g., 500 votes in 10 seconds).

### Private Polls & Lifecycle
- Private polls require an explicit entry in `eligible_voters`. 
- Bulk importing voters via CSV validates the email, adds them to the list, generates a cryptographic 16-byte hex token in `poll_invitations` (which tracks `expires_at` and `used_at`), and prepares an email.
- The `poll_invitations` tokens are strictly **one-time use** and optionally bind to the user's `device_token` upon first access to prevent sharing.
- **Poll Lifecycle Transitions:** Automatically handled via request-based checking inside `PollService`. When a poll is loaded or a vote is attempted, the system compares the current time against `start_time` and `end_time` to shift state to `Scheduled`, `Active`, or `Ended`.

### Transactions
- In `PollService::castVote()`, the actual vote insertion and the audit log insertion are wrapped in a single Database Transaction. This prevents race conditions where a vote is cast but the audit log crashes.

## 4. Testing
- Testing was completed successfully. See [PHASE3_TESTING.md](./PHASE3_TESTING.md) for full functional and security results.
- **Remaining Issues**: None.

The Poll Management & Voting Engine is production-ready. We are cleared to proceed to Phase 4: Dashboard and Analytics.
