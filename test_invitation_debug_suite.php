<?php
/**
 * SecurePoll: Poll Invitation Email & Queue Debugging CLI Test Suite
 */

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

\app\Helpers\Env::load(__DIR__ . '/.env');

use app\Core\Database;
use app\Models\User;
use app\Models\Poll;
use app\Models\PollInvitation;
use app\Models\EmailQueue;
use app\Models\Notification;
use app\Services\NotificationService;
use app\Services\NotificationPreferenceService;
use app\Services\EmailService;
use app\Services\PollService;
use app\Constants\NotificationTypes;

echo "====================================================================\n";
echo " SecurePoll: Poll Invitation Email & Queue Debugging CLI Suite\n";
echo "====================================================================\n\n";

$db = Database::getInstance()->getConnection();

// Record baseline counts
$tables = [
    'users', 'polls', 'poll_options', 'votes', 'notifications',
    'login_sessions', 'trusted_devices', 'password_resets',
    'email_otps', 'eligible_voters', 'poll_invitations', 'email_queue',
    'notification_preferences'
];

$baselines = [];
foreach ($tables as $t) {
    $baselines[$t] = (int)$db->query("SELECT COUNT(*) FROM {$t}")->fetchColumn();
}

$passCount = 0;
$failCount = 0;

function assertTest($cond, $name) {
    global $passCount, $failCount;
    if ($cond) {
        echo "  [PASS] {$name}\n";
        $passCount++;
    } else {
        echo "  [FAIL] {$name}\n";
        $failCount++;
    }
}

$testUserIds = [];
$testPollIds = [];
$testInvitationIds = [];
$testQueueIds = [];
$testNotificationIds = [];

try {
    // -------------------------------------------------------------
    // SETUP FIXTURES
    // -------------------------------------------------------------
    echo "--- Setting Up Isolated Test Fixtures ---\n";
    $userModel = new User();
    $invitationModel = new PollInvitation();
    $queueModel = new EmailQueue();
    $notifModel = new Notification();
    $prefService = new NotificationPreferenceService();
    $notificationService = new NotificationService();
    $pollService = new PollService();

    // 1. Creator / Inviter User
    $db->prepare("INSERT INTO users (role_id, name, email, password_hash, is_verified) VALUES (1, 'Debug Inviter', 'debug_inviter@example.com', 'hash', 1)")->execute();
    $inviterId = (int)$db->lastInsertId();
    $testUserIds[] = $inviterId;

    // 2. Invitee User A (Enabled prefs)
    $db->prepare("INSERT INTO users (role_id, name, email, password_hash, is_verified) VALUES (1, 'Debug Invitee A', 'debug_invitee_a@example.com', 'hash', 1)")->execute();
    $inviteeAId = (int)$db->lastInsertId();
    $testUserIds[] = $inviteeAId;

    // 3. Invitee User B (Disabled category prefs)
    $db->prepare("INSERT INTO users (role_id, name, email, password_hash, is_verified) VALUES (1, 'Debug Invitee B', 'debug_invitee_b@example.com', 'hash', 1)")->execute();
    $inviteeBId = (int)$db->lastInsertId();
    $testUserIds[] = $inviteeBId;

    // 4. Test Private Poll
    $db->prepare("INSERT INTO polls (creator_id, title, description, visibility, voting_type, status) VALUES (?, 'Debug Private Poll', 'Testing invitations', 'Private', 'Single', 'Active')")->execute([$inviterId]);
    $pollId = (int)$db->lastInsertId();
    $testPollIds[] = $pollId;

    echo "  Created test users (#{$inviterId}, #{$inviteeAId}, #{$inviteeBId}) and private poll (#{$pollId}).\n\n";

    // -------------------------------------------------------------
    // TEST 1: Global ON + Invitations ON -> Queued
    // -------------------------------------------------------------
    echo "--- Test 1: Global ON + Poll Invitations ON ---\n";
    $prefService->updateEmailPreferences($inviteeAId, [
        'global' => true,
        'invitations' => true,
        'reminders' => true,
        'updates' => true,
        'results' => true
    ]);

    $token1 = bin2hex(random_bytes(16));
    $invId1 = $invitationModel->create([
        'poll_id' => $pollId,
        'email' => 'debug_invitee_a@example.com',
        'invitation_token' => $token1,
        'status' => 'Pending'
    ]);
    $testInvitationIds[] = $invId1;

    $qCountBefore = (int)$db->query("SELECT COUNT(*) FROM email_queue WHERE to_email = 'debug_invitee_a@example.com'")->fetchColumn();
    $notificationService->notifyNewInvitation($inviteeAId, $pollId, 'Debug Private Poll', 'Debug Inviter', $token1);
    $qCountAfter = (int)$db->query("SELECT COUNT(*) FROM email_queue WHERE to_email = 'debug_invitee_a@example.com'")->fetchColumn();

    assertTest($qCountAfter === $qCountBefore + 1, 'Test 1: Invitation email successfully queued when Global ON & Category ON');

    $qRow1 = $db->query("SELECT * FROM email_queue WHERE to_email = 'debug_invitee_a@example.com' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $testQueueIds[] = $qRow1['id'];
    assertTest($qRow1['status'] === 'pending', 'Test 1: Queued message initial status is pending');
    assertTest($qRow1['attempts'] == 0, 'Test 1: Queued message initial attempts is 0');
    assertTest(strpos($qRow1['body_html'], 'token=' . $token1) !== false, 'Test 1: Queued email contains valid invitation token link');

    $notif1 = $notifModel->getForUser($inviteeAId, 1)[0];
    $testNotificationIds[] = $notif1['id'];
    assertTest($notif1['type'] === NotificationTypes::NEW_INVITATION, 'Test 1: In-app notification also generated');

    // -------------------------------------------------------------
    // TEST 2: Global OFF -> Email NOT Queued
    // -------------------------------------------------------------
    echo "\n--- Test 2: Global Email OFF ---\n";
    $prefService->updateEmailPreferences($inviteeAId, [
        'global' => false,
        'invitations' => true,
        'reminders' => true,
        'updates' => true,
        'results' => true
    ]);

    $token2 = bin2hex(random_bytes(16));
    $qCountBefore2 = (int)$db->query("SELECT COUNT(*) FROM email_queue WHERE to_email = 'debug_invitee_a@example.com'")->fetchColumn();
    $notificationService->notifyNewInvitation($inviteeAId, $pollId, 'Debug Private Poll 2', 'Debug Inviter', $token2);
    $qCountAfter2 = (int)$db->query("SELECT COUNT(*) FROM email_queue WHERE to_email = 'debug_invitee_a@example.com'")->fetchColumn();

    assertTest($qCountAfter2 === $qCountBefore2, 'Test 2: Invitation email is NOT queued when Global email is OFF');

    $notif2 = $db->query("SELECT * FROM notifications WHERE user_id = {$inviteeAId} ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $testNotificationIds[] = $notif2['id'];
    assertTest($notif2['title'] === 'Poll Invitation: Debug Private Poll 2', 'Test 2: In-app notification is still generated when Global email is OFF');

    // -------------------------------------------------------------
    // TEST 3: Global ON + Invitations Category OFF -> Email NOT Queued
    // -------------------------------------------------------------
    echo "\n--- Test 3: Global ON + Invitations Category OFF ---\n";
    $prefService->updateEmailPreferences($inviteeBId, [
        'global' => true,
        'invitations' => false, // explicitly turned off
        'reminders' => true,
        'updates' => true,
        'results' => true
    ]);

    $token3 = bin2hex(random_bytes(16));
    $qCountBefore3 = (int)$db->query("SELECT COUNT(*) FROM email_queue WHERE to_email = 'debug_invitee_b@example.com'")->fetchColumn();
    $notificationService->notifyNewInvitation($inviteeBId, $pollId, 'Debug Private Poll 3', 'Debug Inviter', $token3);
    $qCountAfter3 = (int)$db->query("SELECT COUNT(*) FROM email_queue WHERE to_email = 'debug_invitee_b@example.com'")->fetchColumn();

    assertTest($qCountAfter3 === $qCountBefore3, 'Test 3: Invitation email is NOT queued when Invitations category is OFF');

    $notif3 = $notifModel->getForUser($inviteeBId, 1)[0];
    $testNotificationIds[] = $notif3['id'];
    assertTest($notif3['title'] === 'Poll Invitation: Debug Private Poll 3', 'Test 3: In-app notification still created when category email is OFF');

    // -------------------------------------------------------------
    // TEST 4: Recipient vs Inviter Preferences Isolation
    // -------------------------------------------------------------
    echo "\n--- Test 4: Recipient Preferences Used (Not Inviter) ---\n";
    // Set inviter to Global OFF
    $prefService->updateEmailPreferences($inviterId, [
        'global' => false,
        'invitations' => false,
        'reminders' => false,
        'updates' => false,
        'results' => false
    ]);
    // Set invitee A back to Global ON & Invitations ON
    $prefService->updateEmailPreferences($inviteeAId, [
        'global' => true,
        'invitations' => true,
        'reminders' => true,
        'updates' => true,
        'results' => true
    ]);

    $token4 = bin2hex(random_bytes(16));
    $qCountBefore4 = (int)$db->query("SELECT COUNT(*) FROM email_queue WHERE to_email = 'debug_invitee_a@example.com'")->fetchColumn();
    $notificationService->notifyNewInvitation($inviteeAId, $pollId, 'Debug Private Poll 4', 'Debug Inviter', $token4);
    $qCountAfter4 = (int)$db->query("SELECT COUNT(*) FROM email_queue WHERE to_email = 'debug_invitee_a@example.com'")->fetchColumn();

    assertTest($qCountAfter4 === $qCountBefore4 + 1, 'Test 4: Inviter having Global OFF does NOT block email to recipient who has Global ON');
    $qRow4 = $db->query("SELECT * FROM email_queue WHERE to_email = 'debug_invitee_a@example.com' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $testQueueIds[] = $qRow4['id'];

    // -------------------------------------------------------------
    // TEST 5: Worker Queue Pickup & State Transitions
    // -------------------------------------------------------------
    echo "\n--- Test 5: Worker Queue Pickup & State Transitions ---\n";
    $pendingItems = $queueModel->getPending(100);
    $foundQRow = false;
    foreach ($pendingItems as $p) {
        if ($p['id'] == $qRow4['id']) {
            $foundQRow = true;
            break;
        }
    }
    assertTest($foundQRow, 'Test 5: Pending invitation email is correctly retrieved by worker getPending() query');

    // Mark processing
    $queueModel->markProcessing($qRow4['id']);
    $procRow = $db->query("SELECT * FROM email_queue WHERE id = {$qRow4['id']}")->fetch(PDO::FETCH_ASSOC);
    assertTest($procRow['status'] === 'processing', 'Test 5: Status transitions to processing');
    assertTest((int)$procRow['attempts'] === 1, 'Test 5: Attempts incremented to 1');

    // Mark sent
    $queueModel->markSent($qRow4['id']);
    $sentRow = $db->query("SELECT * FROM email_queue WHERE id = {$qRow4['id']}")->fetch(PDO::FETCH_ASSOC);
    assertTest($sentRow['status'] === 'sent', 'Test 5: Status transitions to sent');
    assertTest(!empty($sentRow['sent_at']), 'Test 5: sent_at timestamp is populated');

    // -------------------------------------------------------------
    // TEST 6: Queue Failure Reporting & Error Persistence
    // -------------------------------------------------------------
    echo "\n--- Test 6: Queue Failure Reporting & Error Persistence ---\n";
    $queueModel->enqueue('debug_invitee_a@example.com', 'Failing Message Test', '<p>Test</p>', 'type:new_invitation');
    $failQId = (int)$db->lastInsertId();
    $testQueueIds[] = $failQId;

    $queueModel->markProcessing($failQId);
    $testErrMsg = 'SMTP Error: Connection refused by remote host (554)';
    $queueModel->markFailed($failQId, $testErrMsg);

    $failedRow = $db->query("SELECT * FROM email_queue WHERE id = {$failQId}")->fetch(PDO::FETCH_ASSOC);
    assertTest($failedRow['status'] === 'failed', 'Test 6: Status transitions to failed');
    assertTest($failedRow['error_message'] === $testErrMsg, 'Test 6: Detailed error message persisted accurately without loss');

    // Verify EmailService exposes getLastError()
    $emailService = new EmailService();
    assertTest(method_exists($emailService, 'getLastError'), 'Test 6: EmailService implements getLastError() method');

    // -------------------------------------------------------------
    // TEST 7: Private Poll Invitation Token Security & Acceptance
    // -------------------------------------------------------------
    echo "\n--- Test 7: Private Poll Invitation Security & Token Validation ---\n";
    $invCheck = $invitationModel->getByToken($token1);
    assertTest(!empty($invCheck), 'Test 7: Token lookup succeeds for valid token');
    assertTest($invCheck['status'] === 'Pending', 'Test 7: Token initial status is Pending');

    // Accept invitation
    $used = $invitationModel->markAsUsed($invCheck['id']);
    assertTest($used, 'Test 7: markAsUsed() returns true');

    $acceptedInv = $invitationModel->getById($invCheck['id']);
    assertTest($acceptedInv['status'] === 'Accepted', 'Test 7: Invitation status transitioned to Accepted');

    // Verify invitee with Accepted status is recognized for private poll
    $invByEmail = $invitationModel->getByPollAndEmail($pollId, 'debug_invitee_a@example.com');
    assertTest($invByEmail && $invByEmail['status'] === 'Accepted', 'Test 7: Invitee with Accepted status is recognized by poll invitation authorization');

} finally {
    // -------------------------------------------------------------
    // CLEANUP FIXTURES
    // -------------------------------------------------------------
    echo "\n--- Cleaning Up Test Fixtures ---\n";
    if (!empty($testNotificationIds)) {
        $inNotifs = implode(',', array_map('intval', $testNotificationIds));
        $db->exec("DELETE FROM notifications WHERE id IN ({$inNotifs})");
    }
    if (!empty($testQueueIds)) {
        $inQ = implode(',', array_map('intval', $testQueueIds));
        $db->exec("DELETE FROM email_queue WHERE id IN ({$inQ})");
    }
    if (!empty($testInvitationIds)) {
        $inInvs = implode(',', array_map('intval', $testInvitationIds));
        $db->exec("DELETE FROM poll_invitations WHERE id IN ({$inInvs})");
    }
    if (!empty($testPollIds)) {
        $inPolls = implode(',', array_map('intval', $testPollIds));
        $db->exec("DELETE FROM polls WHERE id IN ({$inPolls})");
    }
    if (!empty($testUserIds)) {
        $inUsers = implode(',', array_map('intval', $testUserIds));
        $db->exec("DELETE FROM notification_preferences WHERE user_id IN ({$inUsers})");
        $db->exec("DELETE FROM users WHERE id IN ({$inUsers})");
    }
    echo "  Cleaned up all test records and fixtures.\n\n";
}

// -------------------------------------------------------------
// ZERO-DATA MUTATION & BASELINE COMPARISON
// -------------------------------------------------------------
echo "--- Baseline Record Count Verification ---\n";
$allMatch = true;
foreach ($tables as $t) {
    $current = (int)$db->query("SELECT COUNT(*) FROM {$t}")->fetchColumn();
    $expected = $baselines[$t];
    if ($current !== $expected) {
        echo "  [FAIL] Table {$t}: baseline={$expected}, current={$current}\n";
        $allMatch = false;
    } else {
        echo "  Table {$t}: baseline={$expected}, current={$current} [OK]\n";
    }
}
assertTest($allMatch, 'Zero data mutation: All baseline table counts preserved');

echo "\n====================================================================\n";
echo " Test Suite Summary: {$passCount} Passed, {$failCount} Failed\n";
echo "====================================================================\n";

if ($failCount > 0) {
    exit(1);
}
