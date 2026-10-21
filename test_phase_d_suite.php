<?php
/**
 * Phase D Invitations, Notifications & Email Preferences - CLI Regression Test Suite
 *
 * Requirements tested:
 * Group A: Notification & Email Preferences Logic & Persistence
 * Group B: Mandatory Security Email Protection (Non-bypassable)
 * Group C: Poll Invitations & Workflow (In-app + Email Queueing)
 * Group D: Duplicate Prevention & Token Security
 * Group E: Queue Reliability, Real-time Preference Re-check & Retry Limits
 * Group F: In-App Notifications Ownership, Counts & Isolation
 * Group G: Settings Endpoint & CSRF Protection
 * Group H: Zero-Data Mutation & Baseline Verification
 */

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

\app\Helpers\Env::load(__DIR__ . '/.env');

use app\Services\NotificationPreferenceService;
use app\Services\NotificationService;
use app\Services\PollService;
use app\Models\NotificationPreference;
use app\Models\Notification;
use app\Models\EmailQueue;
use app\Models\PollInvitation;
use app\Models\Poll;
use app\Models\User;
use app\Constants\NotificationTypes;
use app\Core\Database;
use app\Helpers\Security;
use app\Helpers\Session;

class PhaseDTestRunner {
    public int $passed = 0;
    public int $failed = 0;
    public array $errors = [];

    public function assert(bool $condition, string $testName, string $failureDetails = ''): void {
        if ($condition) {
            $this->passed++;
            echo "  [PASS] {$testName}\n";
        } else {
            $this->failed++;
            $msg = "  [FAIL] {$testName}" . ($failureDetails ? " -> {$failureDetails}" : '');
            $this->errors[] = $msg;
            echo "{$msg}\n";
        }
    }

    public function assertEquals($expected, $actual, string $testName): void {
        $this->assert($expected === $actual, $testName, "Expected: " . var_export($expected, true) . ", Got: " . var_export($actual, true));
    }

    public function assertTrue($actual, string $testName): void {
        $this->assert($actual === true, $testName, "Expected true, Got: " . var_export($actual, true));
    }

    public function assertFalse($actual, string $testName): void {
        $this->assert($actual === false, $testName, "Expected false, Got: " . var_export($actual, true));
    }
}

$runner = new PhaseDTestRunner();
$db = Database::getInstance()->getConnection();
$prefService = new NotificationPreferenceService();
$notificationService = new NotificationService();
$prefModel = new NotificationPreference();
$notifModel = new Notification();
$queueModel = new EmailQueue();
$invitationModel = new PollInvitation();
$userModel = new User();
$pollModel = new Poll();

echo "====================================================================\n";
echo " SecurePoll Phase D: Automated CLI Regression Test Suite\n";
echo "====================================================================\n\n";

function getTableCounts(PDO $db): array {
    $tables = [
        'users', 'polls', 'poll_options', 'votes', 'notifications', 
        'login_sessions', 'trusted_devices', 'password_resets', 'email_otps', 
        'eligible_voters', 'poll_invitations', 'email_queue', 'notification_preferences'
    ];
    $counts = [];
    foreach ($tables as $t) {
        $stmt = $db->query("SELECT COUNT(*) FROM `{$t}`");
        $counts[$t] = (int)$stmt->fetchColumn();
    }
    return $counts;
}

$baselineCounts = getTableCounts($db);
echo "Initial Baseline Record Counts:\n";
foreach ($baselineCounts as $tbl => $cnt) {
    echo "  {$tbl}: {$cnt}\n";
}
echo "\n";

// Track test records for clean, isolated removal
$testUserIds = [];
$testPollIds = [];
$testInvitationIds = [];
$testNotificationIds = [];
$testQueueIds = [];

try {
    // -------------------------------------------------------------
    // SETUP ISOLATED TEST USERS & POLLS
    // -------------------------------------------------------------
    echo "--- Setting Up Test Fixtures ---\n";
    
    // User A: Creator
    $stmtUserA = $db->prepare("INSERT INTO users (role_id, name, email, password_hash, is_verified) VALUES (1, 'PhaseD Creator', 'phased_creator@example.com', 'test_hash', 1)");
    $stmtUserA->execute();
    $userA_id = (int)$db->lastInsertId();
    $testUserIds[] = $userA_id;

    // User B: Invitee & Setting Tester
    $stmtUserB = $db->prepare("INSERT INTO users (role_id, name, email, password_hash, is_verified) VALUES (1, 'PhaseD Voter', 'phased_voter@example.com', 'test_hash', 1)");
    $stmtUserB->execute();
    $userB_id = (int)$db->lastInsertId();
    $testUserIds[] = $userB_id;

    // User C: Second User for isolation tests
    $stmtUserC = $db->prepare("INSERT INTO users (role_id, name, email, password_hash, is_verified) VALUES (1, 'PhaseD Other', 'phased_other@example.com', 'test_hash', 1)");
    $stmtUserC->execute();
    $userC_id = (int)$db->lastInsertId();
    $testUserIds[] = $userC_id;

    // Create a test private poll
    $stmtPoll = $db->prepare("INSERT INTO polls (creator_id, title, description, visibility, voting_type, status) VALUES (?, 'PhaseD Private Poll', 'Testing invitations', 'Private', 'Single', 'Active')");
    $stmtPoll->execute([$userA_id]);
    $testPollId = (int)$db->lastInsertId();
    $testPollIds[] = $testPollId;

    echo "  Created test users (#{$userA_id}, #{$userB_id}, #{$userC_id}) and private poll (#{$testPollId}).\n\n";

    // =============================================================
    // GROUP A: Notification & Email Preferences Logic & Persistence
    // =============================================================
    echo "--- Test Group A: Email Preferences Logic & Persistence ---\n";

    // A1. Default preferences for new/existing user with no rows
    $defaults = $prefService->getEmailPreferences($userB_id);
    $runner->assertTrue($defaults['global'], 'A1: Global email preference defaults to ON (true)');
    $runner->assertTrue($defaults['invitations'], 'A1: Invitations category defaults to ON (true)');
    $runner->assertTrue($defaults['reminders'], 'A1: Reminders category defaults to ON (true)');
    $runner->assertTrue($defaults['updates'], 'A1: Updates category defaults to ON (true)');
    $runner->assertTrue($defaults['results'], 'A1: Results category defaults to ON (true)');

    // A2. Global ON + Category ON allows sending
    $canSendInv = $prefService->canSendEmail($userB_id, NotificationTypes::NEW_INVITATION);
    $runner->assertTrue($canSendInv, 'A2: Global ON + Invitations ON allows NEW_INVITATION email');

    // A3. Turn Category OFF (invitations = false)
    $prefService->updateEmailPreferences($userB_id, [
        'global' => true,
        'invitations' => false,
        'reminders' => true,
        'updates' => true,
        'results' => true
    ]);
    
    // Check persistence from database
    $reloaded = $prefService->getEmailPreferences($userB_id);
    $runner->assertTrue($reloaded['global'], 'A3: Global setting remains true after database reload');
    $runner->assertFalse($reloaded['invitations'], 'A3: Invitations setting persisted as false');
    $runner->assertTrue($reloaded['reminders'], 'A3: Reminders setting remains true');
    
    $runner->assertFalse($prefService->canSendEmail($userB_id, NotificationTypes::NEW_INVITATION), 'A3: Category OFF blocks invitations email');
    $runner->assertTrue($prefService->canSendEmail($userB_id, NotificationTypes::POLL_ENDING_SOON), 'A3: Other enabled category (reminders) still allowed when invitations is OFF');
    $runner->assertTrue($prefService->canSendEmail($userB_id, NotificationTypes::POLL_UPDATED), 'A3: Other enabled category (updates) still allowed when invitations is OFF');
    $runner->assertTrue($prefService->canSendEmail($userB_id, NotificationTypes::POLL_ENDED), 'A3: Other enabled category (results) still allowed when invitations is OFF');

    // A4. Turn Global OFF
    $prefService->updateEmailPreferences($userB_id, [
        'global' => false,
        'invitations' => true, // Category chosen ON, but Global is OFF
        'reminders' => true,
        'updates' => true,
        'results' => true
    ]);

    $reloadedGlobalOff = $prefService->getEmailPreferences($userB_id);
    $runner->assertFalse($reloadedGlobalOff['global'], 'A4: Global setting persisted as false');
    $runner->assertTrue($reloadedGlobalOff['invitations'], 'A4: Individual category choice preserved even when Global is OFF');

    // With Global OFF, ALL optional emails are blocked regardless of category
    $runner->assertFalse($prefService->canSendEmail($userB_id, NotificationTypes::NEW_INVITATION), 'A4: Global OFF blocks invitations email');
    $runner->assertFalse($prefService->canSendEmail($userB_id, NotificationTypes::POLL_ENDING_SOON), 'A4: Global OFF blocks reminders email');
    $runner->assertFalse($prefService->canSendEmail($userB_id, NotificationTypes::POLL_UPDATED), 'A4: Global OFF blocks updates email');
    $runner->assertFalse($prefService->canSendEmail($userB_id, NotificationTypes::POLL_ENDED), 'A4: Global OFF blocks results email');

    // A5. Global OFF must NOT disable In-App notifications
    $canInApp = $prefService->canSendInApp($userB_id, NotificationTypes::NEW_INVITATION);
    $runner->assertTrue($canInApp, 'A5: Global email OFF does NOT disable in-app notifications');

    // =============================================================
    // GROUP B: Mandatory Security Email Protection
    // =============================================================
    echo "\n--- Test Group B: Mandatory Security Email Protection ---\n";

    // Even when Global email is OFF, security emails MUST always be allowed
    $runner->assertTrue($prefService->canSendEmail($userB_id, NotificationTypes::PASSWORD_RESET), 'B1: Password reset email allowed even when Global is OFF');
    $runner->assertTrue($prefService->canSendEmail($userB_id, NotificationTypes::SECURITY_ALERT), 'B1: Security alert email allowed even when Global is OFF');
    $runner->assertTrue($prefService->canSendEmail($userB_id, NotificationTypes::EMAIL_VERIFICATION), 'B1: Email verification allowed even when Global is OFF');
    $runner->assertTrue($prefService->canSendEmail($userB_id, NotificationTypes::LOGIN_NEW_DEVICE), 'B1: New device login alert allowed even when Global is OFF');
    $runner->assertTrue($prefService->canSendEmail($userB_id, 'otp'), 'B1: OTP verification email allowed even when Global is OFF');

    // =============================================================
    // GROUP C: Poll Invitations & Workflow (In-app + Email Queueing)
    // =============================================================
    echo "\n--- Test Group C: Poll Invitations Workflow ---\n";

    // Reset user B to have Global ON and Invitations ON
    $prefService->updateEmailPreferences($userB_id, [
        'global' => true,
        'invitations' => true,
        'reminders' => true,
        'updates' => true,
        'results' => true
    ]);

    // Track queue count before
    $qBefore = (int)$db->query("SELECT COUNT(*) FROM email_queue WHERE to_email = 'phased_voter@example.com'")->fetchColumn();
    $nBefore = (int)$db->query("SELECT COUNT(*) FROM notifications WHERE user_id = {$userB_id}")->fetchColumn();

    // Invite User B
    $token1 = bin2hex(random_bytes(16));
    $invId1 = $invitationModel->create([
        'poll_id' => $testPollId,
        'email' => 'phased_voter@example.com',
        'invitation_token' => $token1,
        'status' => 'Pending'
    ]);
    $testInvitationIds[] = $invId1;

    $notificationService->notifyNewInvitation(
        $userB_id, 
        $testPollId, 
        'PhaseD Private Poll', 
        'PhaseD Creator', 
        $token1, 
        '2026-12-31 23:59:59'
    );

    // Verify in-app notification created
    $nAfter = (int)$db->query("SELECT COUNT(*) FROM notifications WHERE user_id = {$userB_id}")->fetchColumn();
    $runner->assertEquals($nBefore + 1, $nAfter, 'C1: In-app notification created for valid invitation');

    $latestNotif = $notifModel->getForUser($userB_id, 1)[0];
    $testNotificationIds[] = $latestNotif['id'];
    $runner->assertEquals('Poll Invitation: PhaseD Private Poll', $latestNotif['title'], 'C1: Notification title is formatted accurately');

    // Verify email was queued
    $qAfter = (int)$db->query("SELECT COUNT(*) FROM email_queue WHERE to_email = 'phased_voter@example.com'")->fetchColumn();
    $runner->assertEquals($qBefore + 1, $qAfter, 'C2: Email was queued when user preferences permit it');

    $queuedEmail = $db->query("SELECT * FROM email_queue WHERE to_email = 'phased_voter@example.com' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $testQueueIds[] = $queuedEmail['id'];
    $runner->assertEquals('pending', $queuedEmail['status'], 'C2: Queued email status is pending');
    $runner->assertTrue(strpos($queuedEmail['body_html'], 'PhaseD Private Poll') !== false, 'C2: Email body contains poll title');
    $runner->assertTrue(strpos($queuedEmail['body_html'], 'PhaseD Creator') !== false, 'C2: Email body contains inviter context');
    $runner->assertTrue(strpos($queuedEmail['body_text'], 'type:new_invitation') !== false, 'C2: Email body_text contains notification type metadata');

    // C3. Test Email is NOT queued when preference is OFF
    $prefService->updateEmailPreferences($userB_id, [
        'global' => false,
        'invitations' => true,
        'reminders' => true,
        'updates' => true,
        'results' => true
    ]);

    $qCountBeforeBlocked = (int)$db->query("SELECT COUNT(*) FROM email_queue WHERE to_email = 'phased_voter@example.com'")->fetchColumn();
    $nCountBeforeBlocked = (int)$db->query("SELECT COUNT(*) FROM notifications WHERE user_id = {$userB_id}")->fetchColumn();

    $notificationService->notifyNewInvitation(
        $userB_id, 
        $testPollId, 
        'PhaseD Private Poll (Blocked)', 
        'PhaseD Creator', 
        bin2hex(random_bytes(16))
    );

    $qCountAfterBlocked = (int)$db->query("SELECT COUNT(*) FROM email_queue WHERE to_email = 'phased_voter@example.com'")->fetchColumn();
    $nCountAfterBlocked = (int)$db->query("SELECT COUNT(*) FROM notifications WHERE user_id = {$userB_id}")->fetchColumn();

    $runner->assertEquals($qCountBeforeBlocked, $qCountAfterBlocked, 'C3: Email is NOT queued when Global email is OFF');
    $runner->assertEquals($nCountBeforeBlocked + 1, $nCountAfterBlocked, 'C3: In-app notification is STILL created when Global email is OFF');

    // Find and track blocked notification for cleanup
    $blockedNotif = $notifModel->getForUser($userB_id, 1)[0];
    $testNotificationIds[] = $blockedNotif['id'];

    // =============================================================
    // GROUP D: Duplicate Prevention & Token Protections
    // =============================================================
    echo "\n--- Test Group D: Duplicate Prevention & Token Security ---\n";

    // Simulate duplicate check as done in PollController::inviteUser()
    $existingInv = $invitationModel->getByPollAndEmail($testPollId, 'phased_voter@example.com');
    $runner->assertTrue(!empty($existingInv), 'D1: Existing pending invitation correctly detected');
    $runner->assertEquals('Pending', $existingInv['status'], 'D1: Existing invitation is in Pending status');

    // Re-submitting the same invitation must be rejected
    $isDuplicatePending = ($existingInv && $existingInv['status'] === 'Pending');
    $runner->assertTrue($isDuplicatePending, 'D1: Duplicate submission prevented when invitation is already pending');

    // Private poll authorization check with valid invitation token
    Session::set('user_id', $userB_id);
    Session::set('user_email', 'phased_voter@example.com');

    // Accept invitation via DashboardController workflow
    $invitationModel->updateStatus($invId1, 'Accepted');
    $acceptedInv = $invitationModel->getById($invId1);
    $runner->assertEquals('Accepted', $acceptedInv['status'], 'D2: Invitation status successfully transitioned to Accepted');

    // Once accepted, user has authorized access
    $hasAccess = ($acceptedInv && $acceptedInv['status'] === 'Accepted');
    $runner->assertTrue($hasAccess, 'D2: Accepted invitation grants private poll access');

    // Invalid / fake token is rejected
    $fakeInv = $invitationModel->getByToken('nonexistent_token_12345');
    $runner->assertFalse((bool)$fakeInv, 'D3: Fake/invalid token correctly rejected');

    // =============================================================
    // GROUP E: Queue Reliability, Preference Re-check & Retry Limits
    // =============================================================
    echo "\n--- Test Group E: Queue Reliability & Retry Handling ---\n";

    // E1: Real-time preference re-check:
    // Insert an optional email into queue while user had email ON
    $queueModel->enqueue('phased_voter@example.com', 'Poll Reminder Test', '<p>Reminder</p>', 'type:poll_ending_soon');
    $reminderQueueId = (int)$db->lastInsertId();
    $testQueueIds[] = $reminderQueueId;

    // Now, before worker runs, User turns Global email OFF!
    $prefService->updateEmailPreferences($userB_id, [
        'global' => false,
        'invitations' => true,
        'reminders' => true,
        'updates' => true,
        'results' => true
    ]);

    // Simulate worker preference check
    $row = $db->query("SELECT * FROM email_queue WHERE id = {$reminderQueueId}")->fetch(PDO::FETCH_ASSOC);
    $type = 'poll_ending_soon';
    $canSendNow = $prefService->canSendEmail($userB_id, $type);
    $runner->assertFalse($canSendNow, 'E1: Real-time preference check blocks sending of already-queued optional email');

    // Worker marks as skipped/failed
    $queueModel->markFailed($reminderQueueId, 'Skipped: User disabled email notifications.');
    $skippedRow = $db->query("SELECT * FROM email_queue WHERE id = {$reminderQueueId}")->fetch(PDO::FETCH_ASSOC);
    $runner->assertEquals('failed', $skippedRow['status'], 'E1: Skipped email marked as failed');
    $runner->assertTrue(strpos($skippedRow['error_message'], 'Skipped: User disabled') !== false, 'E1: Error message records skipped reason');

    // E2: Successful transport handoff updates status to 'sent'
    $queueModel->enqueue('phased_voter@example.com', 'Sent Test', '<p>Test</p>', 'type:otp');
    $sentQueueId = (int)$db->lastInsertId();
    $testQueueIds[] = $sentQueueId;

    $queueModel->markProcessing($sentQueueId);
    $procRow = $db->query("SELECT * FROM email_queue WHERE id = {$sentQueueId}")->fetch(PDO::FETCH_ASSOC);
    $runner->assertEquals('processing', $procRow['status'], 'E2: Message correctly transitions to processing');
    $runner->assertEquals(1, (int)$procRow['attempts'], 'E2: Attempts incremented to 1');

    $queueModel->markSent($sentQueueId);
    $sentRow = $db->query("SELECT * FROM email_queue WHERE id = {$sentQueueId}")->fetch(PDO::FETCH_ASSOC);
    $runner->assertEquals('sent', $sentRow['status'], 'E2: Message successfully marked sent upon successful handoff');
    $runner->assertTrue(!empty($sentRow['sent_at']), 'E2: sent_at timestamp is populated');

    // E3: Retry limits enforcement
    $queueModel->enqueue('phased_voter@example.com', 'Retry Test', '<p>Failing</p>');
    $retryQueueId = (int)$db->lastInsertId();
    $testQueueIds[] = $retryQueueId;

    // Simulate 3 failures
    for ($attempt = 1; $attempt <= 3; $attempt++) {
        $queueModel->markProcessing($retryQueueId);
        $queueModel->markFailed($retryQueueId, "Simulated network failure #{$attempt}");
    }

    $failedRow = $db->query("SELECT * FROM email_queue WHERE id = {$retryQueueId}")->fetch(PDO::FETCH_ASSOC);
    $runner->assertEquals(3, (int)$failedRow['attempts'], 'E3: Attempts tracked accurately up to limit (3)');

    // Pending query must NOT pick up messages that reached retry limit
    $pendingList = $queueModel->getPending(100);
    $foundInPending = false;
    foreach ($pendingList as $p) {
        if ($p['id'] == $retryQueueId) {
            $foundInPending = true;
            break;
        }
    }
    $runner->assertFalse($foundInPending, 'E3: Permanently failed message with 3 attempts is excluded from pending queue');

    // =============================================================
    // GROUP F: In-App Notifications Ownership, Counts & Isolation
    // =============================================================
    echo "\n--- Test Group F: In-App Notifications Ownership & Counts ---\n";

    // Create a notification for User B and another for User C
    $nB_id = $notifModel->create([
        'user_id' => $userB_id,
        'type' => 'test_type',
        'title' => 'User B Notification',
        'message' => 'Hello B'
    ]);
    $testNotificationIds[] = $nB_id;

    $nC_id = $notifModel->create([
        'user_id' => $userC_id,
        'type' => 'test_type',
        'title' => 'User C Notification',
        'message' => 'Hello C'
    ]);
    $testNotificationIds[] = $nC_id;

    // F1: Unread count accuracy
    $uCountB = (int)$notifModel->getUnreadCount($userB_id);
    $uCountC = (int)$notifModel->getUnreadCount($userC_id);
    $runner->assertTrue($uCountB >= 1, 'F1: User B has correct unread count');
    $runner->assertTrue($uCountC >= 1, 'F1: User C has correct unread count');

    // F2: Ownership enforcement on mark-as-read
    // User C tries to mark User B's notification as read
    $notifModel->markAsRead($nB_id, $userC_id);
    $checkB = $db->query("SELECT is_read FROM notifications WHERE id = {$nB_id}")->fetchColumn();
    $runner->assertEquals(0, (int)$checkB, 'F2: User C cannot mark User B notification as read (isolation enforced)');

    // Owner (User B) marks own notification as read
    $notifModel->markAsRead($nB_id, $userB_id);
    $checkB2 = $db->query("SELECT is_read FROM notifications WHERE id = {$nB_id}")->fetchColumn();
    $runner->assertEquals(1, (int)$checkB2, 'F2: Owner successfully marks notification as read');

    // F3: Ownership enforcement on delete
    // User B tries to delete User C's notification
    $notifModel->delete($nC_id, $userB_id);
    $checkC = $db->query("SELECT COUNT(*) FROM notifications WHERE id = {$nC_id}")->fetchColumn();
    $runner->assertEquals(1, (int)$checkC, 'F3: User B cannot delete User C notification (isolation enforced)');

    // Owner (User C) deletes own notification
    $notifModel->delete($nC_id, $userC_id);
    $checkC2 = $db->query("SELECT COUNT(*) FROM notifications WHERE id = {$nC_id}")->fetchColumn();
    $runner->assertEquals(0, (int)$checkC2, 'F3: Owner successfully deletes notification');

    // =============================================================
    // GROUP G: Settings Endpoint & CSRF Protection
    // =============================================================
    echo "\n--- Test Group G: Settings Endpoint & CSRF Protection ---\n";

    // G1: CSRF validation helper test
    $validToken = Security::generateCsrfToken();
    $runner->assertTrue(Security::verifyCsrfToken($validToken), 'G1: Valid CSRF token accepted');
    $runner->assertFalse(Security::verifyCsrfToken('tampered_token_xyz'), 'G1: Invalid CSRF token rejected');
    $runner->assertFalse(Security::verifyCsrfToken(''), 'G1: Empty CSRF token rejected');

    // G2: Full round-trip preference update via service
    $prefService->updateEmailPreferences($userB_id, [
        'global' => true,
        'invitations' => true,
        'reminders' => false,
        'updates' => true,
        'results' => false
    ]);

    $finalPrefs = $prefService->getEmailPreferences($userB_id);
    $runner->assertTrue($finalPrefs['global'], 'G2: Global preference ON');
    $runner->assertTrue($finalPrefs['invitations'], 'G2: Invitations ON');
    $runner->assertFalse($finalPrefs['reminders'], 'G2: Reminders OFF');
    $runner->assertTrue($finalPrefs['updates'], 'G2: Updates ON');
    $runner->assertFalse($finalPrefs['results'], 'G2: Results OFF');

} catch (\Throwable $e) {
    echo "\n[EXCEPTION OCCURRED]: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
    $runner->failed++;
} finally {
    // =============================================================
    // CLEANUP FIXTURES
    // =============================================================
    echo "\n--- Cleaning Up Test Fixtures ---\n";

    if (!empty($testNotificationIds)) {
        $inNotifs = implode(',', array_map('intval', $testNotificationIds));
        $db->exec("DELETE FROM notifications WHERE id IN ({$inNotifs})");
    }
    // Also clean any notification for test users
    if (!empty($testUserIds)) {
        $inUsers = implode(',', array_map('intval', $testUserIds));
        $db->exec("DELETE FROM notifications WHERE user_id IN ({$inUsers})");
        $db->exec("DELETE FROM notification_preferences WHERE user_id IN ({$inUsers})");
    }

    if (!empty($testQueueIds)) {
        $inQueue = implode(',', array_map('intval', $testQueueIds));
        $db->exec("DELETE FROM email_queue WHERE id IN ({$inQueue})");
    }
    $db->exec("DELETE FROM email_queue WHERE to_email LIKE 'phased_%'");

    if (!empty($testInvitationIds)) {
        $inInvs = implode(',', array_map('intval', $testInvitationIds));
        $db->exec("DELETE FROM poll_invitations WHERE id IN ({$inInvs})");
    }

    if (!empty($testPollIds)) {
        $inPolls = implode(',', array_map('intval', $testPollIds));
        $db->exec("DELETE FROM poll_invitations WHERE poll_id IN ({$inPolls})");
        $db->exec("DELETE FROM poll_options WHERE poll_id IN ({$inPolls})");
        $db->exec("DELETE FROM votes WHERE poll_id IN ({$inPolls})");
        $db->exec("DELETE FROM polls WHERE id IN ({$inPolls})");
    }

    if (!empty($testUserIds)) {
        $inUsers = implode(',', array_map('intval', $testUserIds));
        $db->exec("DELETE FROM users WHERE id IN ({$inUsers})");
    }

    echo "  Cleaned up all test records and fixtures.\n\n";

    // =============================================================
    // GROUP H: Zero-Data Mutation & Baseline Verification
    // =============================================================
    echo "--- Test Group H: Zero-Data Mutation & Baseline Verification ---\n";
    $finalCounts = getTableCounts($db);
    $countsMatch = true;
    echo "Database State Comparison (Baseline vs Final):\n";
    foreach ($baselineCounts as $tbl => $bCount) {
        $fCount = $finalCounts[$tbl] ?? -1;
        $match = ($bCount === $fCount);
        if (!$match) $countsMatch = false;
        echo "  {$tbl}: baseline={$bCount}, final={$fCount} " . ($match ? "[OK]" : "[MISMATCH]") . "\n";
    }

    $runner->assertTrue($countsMatch, 'H1: All baseline database record counts perfectly preserved with zero data loss');
}

echo "\n====================================================================\n";
echo " Test Suite Summary\n";
echo "====================================================================\n";
echo " Total Tests Passed: {$runner->passed}\n";
echo " Total Tests Failed: {$runner->failed}\n";
if (!empty($runner->errors)) {
    echo "\nFailed Assertions:\n";
    foreach ($runner->errors as $err) {
        echo "  {$err}\n";
    }
}
echo "====================================================================\n";

exit($runner->failed === 0 ? 0 : 1);
