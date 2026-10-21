<?php
date_default_timezone_set('Asia/Kolkata');

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

\app\Helpers\Env::load(__DIR__ . '/.env');

use app\Core\Database;
use app\Models\Poll;
use app\Models\EmailQueue;
use app\Services\NotificationService;
use app\Constants\NotificationTypes;

// Mock session/env for CLI if needed
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

$db = Database::getInstance()->getConnection();
$pollModel = new Poll();
$notificationService = new NotificationService();

// 1. Process Poll Endings (Ended)
$stmt = $db->prepare("
    SELECT * FROM polls 
    WHERE status = 'Active' 
    AND auto_close = 1 
    AND end_time IS NOT NULL 
    AND end_time <= CURRENT_TIMESTAMP
");
$stmt->execute();
$endedPolls = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($endedPolls as $poll) {
    // Update status
    $updateStmt = $db->prepare("UPDATE polls SET status = 'Ended' WHERE id = ?");
    $updateStmt->execute([$poll['id']]);
    
    // Notify Creator
    $notificationService->notifyPollEnded(
        $poll['creator_id'], 
        $poll['id'], 
        $poll['title']
    );

    // Notify accepted participants that results are published
    $invStmt = $db->prepare("SELECT email FROM poll_invitations WHERE poll_id = ? AND status = 'Accepted'");
    $invStmt->execute([$poll['id']]);
    $acceptedInvites = $invStmt->fetchAll(PDO::FETCH_ASSOC);
    $userModel = new \app\Models\User();
    foreach ($acceptedInvites as $inv) {
        $u = $userModel->findByEmail($inv['email']);
        if ($u && $u['id'] != $poll['creator_id']) {
            $notificationService->notifyPollEnded($u['id'], $poll['id'], $poll['title']);
        }
    }
}

// 2. Process Poll Starts (Scheduled -> Active)
$stmt = $db->prepare("
    SELECT * FROM polls 
    WHERE status = 'Scheduled' 
    AND start_time IS NOT NULL 
    AND start_time <= CURRENT_TIMESTAMP
");
$stmt->execute();
$startedPolls = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($startedPolls as $poll) {
    // Update status
    $updateStmt = $db->prepare("UPDATE polls SET status = 'Active' WHERE id = ?");
    $updateStmt->execute([$poll['id']]);
    
    // Notify Creator
    $notificationService->notify(
        $poll['creator_id'], 
        NotificationTypes::POLL_STARTED, 
        'Poll Started', 
        "Your scheduled poll '{$poll['title']}' has started.", 
        ['category' => 'Poll Activity', 'entity_type' => 'poll', 'entity_id' => $poll['id'], 'icon' => 'play']
    );
    
    // Notify voters if VOTING_AVAILABLE is needed
    if ($poll['visibility'] === 'Private') {
        $invStmt = $db->prepare("SELECT email FROM poll_invitations WHERE poll_id = ?");
        $invStmt->execute([$poll['id']]);
        $invites = $invStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $userModel = new \app\Models\User();
        foreach ($invites as $inv) {
            $user = $userModel->findByEmail($inv['email']);
            if ($user) {
                // Prevent duplicate by using group_key
                $notificationService->notify(
                    $user['id'], 
                    NotificationTypes::VOTING_AVAILABLE, 
                    'Voting is now open', 
                    "Voting is now open for '{$poll['title']}'.", 
                    ['category' => 'Voting', 'entity_type' => 'poll', 'entity_id' => $poll['id'], 'icon' => 'unlock', 'group_key' => 'voting_avail_' . $poll['id']]
                );
            }
        }
    }
}

// 3. Process Reminders (Ending Soon)
// Check if custom notification_reminders table exists; if not, query active polls with upcoming end_time
$remindersTableExists = (bool)$db->query("SHOW TABLES LIKE 'notification_reminders'")->fetchColumn();

if ($remindersTableExists) {
    $stmt = $db->prepare("
        SELECT r.poll_id, r.reminder_hours, p.end_time, p.title, p.creator_id, p.status
        FROM notification_reminders r
        JOIN polls p ON r.poll_id = p.id
        WHERE p.status = 'Active' AND p.end_time IS NOT NULL
    ");
    $stmt->execute();
    $reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Standard reminder: 24 hours before poll closes
    $stmt = $db->prepare("
        SELECT id as poll_id, 24 as reminder_hours, end_time, title, creator_id, status
        FROM polls
        WHERE status = 'Active' 
        AND end_time IS NOT NULL 
        AND end_time > CURRENT_TIMESTAMP 
        AND end_time <= DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 24 HOUR)
    ");
    $stmt->execute();
    $reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

foreach ($reminders as $rem) {
    $endTime = new \DateTime($rem['end_time']);
    $now = new \DateTime();
    $diff = $now->diff($endTime);
    $hoursRemaining = $diff->days * 24 + $diff->h;
    
    if ($endTime > $now && $hoursRemaining <= $rem['reminder_hours']) {
        // Notify Creator
        $notificationService->notifyPollEndingSoon(
            $rem['creator_id'], 
            $rem['poll_id'], 
            $rem['title'], 
            $rem['reminder_hours']
        );

        // Also notify accepted invitees who haven't voted yet
        $invStmt = $db->prepare("SELECT email FROM poll_invitations WHERE poll_id = ? AND status = 'Accepted'");
        $invStmt->execute([$rem['poll_id']]);
        $invites = $invStmt->fetchAll(PDO::FETCH_ASSOC);
        $userModel = new \app\Models\User();
        $voteModel = new \app\Models\Vote();
        foreach ($invites as $inv) {
            $u = $userModel->findByEmail($inv['email']);
            if ($u && $u['id'] != $rem['creator_id']) {
                $hasVoted = $voteModel->hasVotedByUserId($rem['poll_id'], $u['id']);
                if (!$hasVoted) {
                    $notificationService->notifyPollEndingSoon(
                        $u['id'], 
                        $rem['poll_id'], 
                        $rem['title'], 
                        $rem['reminder_hours']
                    );
                }
            }
        }
    }
}

// 4. Process Email Queue (Batch of 50)
$emailQueue = new EmailQueue();
$pending = $emailQueue->getPending(50);
$emailService = new \app\Services\EmailService();
$userModel = new \app\Models\User();
$prefService = new \app\Services\NotificationPreferenceService();

foreach ($pending as $emailRow) {
    $emailQueue->markProcessing($emailRow['id']);
    $toEmail = $emailRow['to_email'];
    $bodyText = $emailRow['body_text'] ?? '';
    
    // Check if optional queued email and if recipient has disabled it
    $type = null;
    if (strpos($bodyText, 'type:') === 0) {
        $type = trim(substr($bodyText, 5));
    }
    
    $recipientUser = $userModel->findByEmail($toEmail);
    if ($recipientUser && $type && !$prefService->isMandatorySecurityType($type)) {
        // Re-check current preferences before sending
        if (!$prefService->canSendEmail($recipientUser['id'], $type)) {
            $emailQueue->markFailed($emailRow['id'], 'Skipped: User disabled email notifications.');
            echo "Email #{$emailRow['id']} to {$toEmail} skipped (user disabled notifications).\n";
            continue;
        }
    }
    
    // Enforce retry limit (attempts was incremented in markProcessing)
    if ((int)$emailRow['attempts'] > 3) {
        $emailQueue->markFailed($emailRow['id'], 'Max retry attempts reached.');
        echo "Email #{$emailRow['id']} to {$toEmail} permanently failed (max retries exceeded).\n";
        continue;
    }
    
    try {
        $result = $emailService->sendEmail($emailRow['to_email'], $emailRow['subject'], $emailRow['body_html']);
        if ($result) {
            $emailQueue->markSent($emailRow['id']);
            echo "Email #{$emailRow['id']} to {$toEmail} successfully handed off to SMTP transport.\n";
        } else {
            $errDetail = $emailService->getLastError() ?: 'SMTP transport handoff failed.';
            $emailQueue->markFailed($emailRow['id'], substr($errDetail, 0, 255));
            echo "Email #{$emailRow['id']} to {$toEmail} failed during SMTP handoff: {$errDetail}\n";
        }
    } catch (\Exception $e) {
        $safeMsg = substr($e->getMessage(), 0, 255);
        $emailQueue->markFailed($emailRow['id'], $safeMsg);
        echo "Email #{$emailRow['id']} to {$toEmail} error: {$safeMsg}\n";
    }
}

echo "Scheduler run completed.\n";
