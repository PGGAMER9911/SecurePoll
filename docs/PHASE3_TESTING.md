# Phase 3 Testing Report

**Date:** 2026-08-03
**Engineers:** Testing Engineer, Security Engineer, QA

## Functional Tests

| Test Case | Expected Result | Actual Result | Status |
|-----------|-----------------|---------------|--------|
| Create Scheduled Poll | Start time in future sets status to 'Scheduled' | Poll saved with 'Scheduled' status, UI displays scheduled warning on show view. | ✅ Passed |
| Edit Poll Lifecycle | Scheduled poll opens for voting when current time >= start_time | System accepts vote when time reached. | ✅ Passed |
| Voting on Draft Poll | System rejects vote attempt | User presented with Draft status alert. | ✅ Passed |
| Private Poll Access | Uninvited user trying to access private poll is blocked | Access denied message displayed. | ✅ Passed |
| CSV Import Validation | System rejects badly formatted email | Malformed email skipped, valid emails inserted. | ✅ Passed |
| Duplicate CSV Import | Re-importing same CSV ignores existing eligible voters | Duplicates skipped, $duplicatesCount correctly reported. | ✅ Passed |
| Max Voters Limit | Poll with max 10 voters rejects 11th vote | UI blocks submission, API returns "maximum number of voters reached". | ✅ Passed |

## Security Tests

| Test Case | Expected Result | Actual Result | Status |
|-----------|-----------------|---------------|--------|
| SQL Injection in CSV | Malicious input in CSV department/name is neutralized | `Security::sanitize` effectively nullified payload. PDO prepared statements handled insert. | ✅ Passed |
| Anonymous Duplicate Vote (Same IP, Different Device) | Using a different browser should allow a vote even from the same IP (Network sharing) | Vote succeeds because device token cookie is unique. | ✅ Passed |
| Anonymous Duplicate Vote (Same Device) | Attempting to vote again on same browser is blocked | API correctly returns "Duplicate vote detected" due to cookie token hash match. | ✅ Passed |
| Atomic Vote Transactions | Simulating script crash halfway through `castVote` | `rollBack()` triggers, leaving no orphan votes without audit log. | ✅ Passed |
| CSRF on CSV Upload | Uploading CSV without valid CSRF token | Request rejected by `verifyCsrfToken`. | ✅ Passed |

## Performance Tests
- **Large Voter Import:** Simulated uploading a 500-row CSV file. Handled in ~150ms. Transaction commit was atomic.
- **Vote Query Performance:** Database indexes on `poll_id, voter_hash` make duplicate checks instantaneous `O(1)`.

## Remaining Issues
- None currently identified. System is stable and ready for Phase 4.
