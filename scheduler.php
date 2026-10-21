<?php
/**
 * SecurePoll Unified Automation Scheduler & Email Queue Worker
 *
 * Handles:
 * 1. Automatic poll closure (Active -> Ended) when end_time expires.
 * 2. Scheduled poll publishing (Scheduled -> Active) when start_time arrives.
 * 3. Reminder notifications for polls ending soon.
 * 4. Reliable, concurrent-safe asynchronous email queue dispatching.
 *
 * Usage:
 *   php scheduler.php                 Run a single cycle (default, for Task Scheduler / Cron)
 *   php scheduler.php --once          Explicitly run a single cycle
 *   php scheduler.php --daemon        Run continuously as a worker daemon
 *   php scheduler.php --interval=30   Set loop interval in seconds (default: 30)
 *   php scheduler.php --limit=50      Set email batch size per cycle (default: 50)
 *   php scheduler.php --dry-run       Simulate email dispatching without SMTP transport
 *   php scheduler.php --verbose       Print verbose log output to console
 *   php scheduler.php --status        Display queue statistics and exit
 *   php scheduler.php --help          Display usage instructions
 */

date_default_timezone_set('Asia/Kolkata');

// Security Guard: Prevent HTTP access to CLI worker
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Access denied: This worker can only be executed via CLI.\n");
}

// Autoloader function for application classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load Composer Autoloader for PHPMailer
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Load Environment Configuration
\app\Helpers\Env::load(__DIR__ . '/.env');

use app\Core\Database;
use app\Models\Poll;
use app\Models\EmailQueue;
use app\Models\User;
use app\Models\Vote;
use app\Services\NotificationService;
use app\Services\NotificationPreferenceService;
use app\Services\EmailService;
use app\Constants\NotificationTypes;

// Parse Command-Line Options
$shortopts = "hvd";
$longopts = [
    "help",
    "once",
    "daemon",
    "interval::",
    "limit::",
    "dry-run",
    "mock",
    "verbose",
    "status"
];
$options = getopt($shortopts, $longopts);

if (isset($options['help']) || isset($options['h'])) {
    echo "====================================================================\n";
    echo " SecurePoll Unified Automation Scheduler & Email Queue Worker\n";
    echo "====================================================================\n";
    echo "Usage:\n";
    echo "  php scheduler.php [options]\n\n";
    echo "Options:\n";
    echo "  --once              Run one cycle and exit (default)\n";
    echo "  --daemon, -d        Run continuously in background worker loop\n";
    echo "  --interval=<sec>    Sleep interval between cycles in daemon mode (default: 30)\n";
    echo "  --limit=<n>         Maximum emails to process per cycle (default: 50)\n";
    echo "  --dry-run, --mock   Simulate email dispatching without connecting to SMTP\n";
    echo "  --verbose, -v       Echo log entries to console in addition to log file\n";
    echo "  --status            Print email queue counts and exit\n";
    echo "  --help, -h          Show this help message\n";
    exit(0);
}

// Initialize Logging Subsystem
$logDir = __DIR__ . '/storage/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}
$logFile = $logDir . '/scheduler.log';

function scheduler_log($level, $message, $verbose = false) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $line = "[{$timestamp}] [{$level}] {$message}\n";
    @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    if ($verbose || php_sapi_name() === 'cli' && in_array($level, ['ERROR', 'CRITICAL'])) {
        echo $line;
    }
}

function mask_email_safe($email) {
    if (!$email || strpos($email, '@') === false) return '***';
    $parts = explode('@', $email, 2);
    $name = $parts[0];
    $domain = $parts[1];
    $maskedName = strlen($name) <= 2 ? $name[0] . '*' : $name[0] . '***' . substr($name, -1);
    return $maskedName . '@' . $domain;
}

// Queue Status Mode
if (isset($options['status'])) {
    try {
        $db = Database::getInstance()->getConnection();
        $emailQueue = new EmailQueue();
        $summary = $emailQueue->getQueueSummary();
        $total = (int)$db->query("SELECT COUNT(*) FROM email_queue")->fetchColumn();
        echo "====================================================================\n";
        echo " SecurePoll Email Queue Status\n";
        echo "====================================================================\n";
        echo "Total Queue Records: {$total}\n";
        echo "  - Pending:    " . ($summary['pending'] ?? 0) . "\n";
        echo "  - Processing: " . ($summary['processing'] ?? 0) . "\n";
        echo "  - Sent:       " . ($summary['sent'] ?? 0) . "\n";
        echo "  - Failed:     " . ($summary['failed'] ?? 0) . "\n";
        exit(0);
    } catch (\Throwable $e) {
        echo "Error fetching queue status: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Configuration Parameters
$isDaemon = isset($options['daemon']) || isset($options['d']);
$interval = isset($options['interval']) ? max(5, (int)$options['interval']) : 30;
$batchLimit = isset($options['limit']) ? max(1, (int)$options['limit']) : 50;
$isDryRun = isset($options['dry-run']) || isset($options['mock']);
$isVerbose = isset($options['verbose']) || isset($options['v']);

// Concurrency Lock: Prevent overlapping runs via flock
$lockDir = __DIR__ . '/storage/cache';
if (!is_dir($lockDir)) {
    @mkdir($lockDir, 0755, true);
}
$lockFile = $lockDir . '/scheduler.lock';
$lockHandle = fopen($lockFile, 'c+');

if (!$lockHandle || !flock($lockHandle, LOCK_EX | LOCK_NB)) {
    $msg = "Another scheduler process is actively running (lock busy). Exiting to prevent concurrency collision.";
    scheduler_log('NOTICE', $msg, $isVerbose);
    if ($isVerbose) {
        echo $msg . "\n";
    }
    exit(0);
}

scheduler_log('INFO', "Scheduler started (Mode: " . ($isDaemon ? "Daemon, Interval: {$interval}s" : "Single Cycle") . ($isDryRun ? ", DRY-RUN" : "") . ")", $isVerbose);

/**
 * Executes a single complete scheduler processing cycle.
 *
 * @param PDO $db
 * @param array $config
 * @return array Metrics of operations performed in cycle
 */
function run_scheduler_cycle($db, $config) {
    $metrics = [
        'polls_ended' => 0,
        'polls_started' => 0,
        'reminders_checked' => 0,
        'emails_processed' => 0,
        'emails_sent' => 0,
        'emails_failed' => 0,
        'emails_skipped' => 0,
    ];

    $notificationService = new NotificationService();
    $userModel = new User();
    $pollModel = new Poll();
    $voteModel = new Vote();
    $emailQueue = new EmailQueue();
    $prefService = new NotificationPreferenceService();
    $emailService = new EmailService();
    $isDryRun = $config['is_dry_run'];
    $isVerbose = $config['is_verbose'];

    // -------------------------------------------------------------
    // 1. Process Poll Endings (Active -> Ended)
    // -------------------------------------------------------------
    try {
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
            $updateStmt = $db->prepare("UPDATE polls SET status = 'Ended' WHERE id = ?");
            $updateStmt->execute([$poll['id']]);
            $metrics['polls_ended']++;

            scheduler_log('INFO', "Poll #{$poll['id']} auto-closed (status -> Ended).", $isVerbose);

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

            foreach ($acceptedInvites as $inv) {
                $u = $userModel->findByEmail($inv['email']);
                if ($u && $u['id'] != $poll['creator_id']) {
                    $notificationService->notifyPollEnded($u['id'], $poll['id'], $poll['title']);
                }
            }
        }
    } catch (\Throwable $t) {
        scheduler_log('ERROR', "Error processing poll endings: " . $t->getMessage(), true);
    }

    // -------------------------------------------------------------
    // 2. Process Poll Starts (Scheduled -> Active)
    // -------------------------------------------------------------
    try {
        $stmt = $db->prepare("
            SELECT * FROM polls 
            WHERE status = 'Scheduled' 
            AND start_time IS NOT NULL 
            AND start_time <= CURRENT_TIMESTAMP
        ");
        $stmt->execute();
        $startedPolls = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($startedPolls as $poll) {
            $updateStmt = $db->prepare("UPDATE polls SET status = 'Active' WHERE id = ?");
            $updateStmt->execute([$poll['id']]);
            $metrics['polls_started']++;

            scheduler_log('INFO', "Scheduled poll #{$poll['id']} started (status -> Active).", $isVerbose);

            // Notify Creator
            $notificationService->notify(
                $poll['creator_id'], 
                NotificationTypes::POLL_STARTED, 
                'Poll Started', 
                "Your scheduled poll '{$poll['title']}' has started.", 
                ['category' => 'Poll Activity', 'entity_type' => 'poll', 'entity_id' => $poll['id'], 'icon' => 'play']
            );
            
            // Notify private poll voters
            if ($poll['visibility'] === 'Private') {
                $invStmt = $db->prepare("SELECT email FROM poll_invitations WHERE poll_id = ?");
                $invStmt->execute([$poll['id']]);
                $invites = $invStmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($invites as $inv) {
                    $user = $userModel->findByEmail($inv['email']);
                    if ($user) {
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
    } catch (\Throwable $t) {
        scheduler_log('ERROR', "Error processing poll starts: " . $t->getMessage(), true);
    }

    // -------------------------------------------------------------
    // 3. Process Reminders (Ending Soon)
    // -------------------------------------------------------------
    try {
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
                $metrics['reminders_checked']++;

                // Notify Creator
                $notificationService->notifyPollEndingSoon(
                    $rem['creator_id'], 
                    $rem['poll_id'], 
                    $rem['title'], 
                    $rem['reminder_hours']
                );

                // Notify accepted invitees who haven't voted yet
                $invStmt = $db->prepare("SELECT email FROM poll_invitations WHERE poll_id = ? AND status = 'Accepted'");
                $invStmt->execute([$rem['poll_id']]);
                $invites = $invStmt->fetchAll(PDO::FETCH_ASSOC);

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
    } catch (\Throwable $t) {
        scheduler_log('ERROR', "Error processing reminders: " . $t->getMessage(), true);
    }

    // -------------------------------------------------------------
    // 4. Process Email Queue (Batched, Concurrent-Safe)
    // -------------------------------------------------------------
    try {
        // Reclaim any items stuck in 'processing' due to earlier hard crash / reboot
        $reclaimed = $emailQueue->reclaimStaleProcessing(10);
        if ($reclaimed > 0) {
            scheduler_log('INFO', "Reclaimed {$reclaimed} stale processing email(s) back to pending.", $isVerbose);
        }

        $pending = $emailQueue->getPending($config['batch_limit']);

        foreach ($pending as $emailRow) {
            $queueId = (int)$emailRow['id'];

            // Concurrency Guard: Atomically claim item
            if (!$emailQueue->claimForProcessing($queueId)) {
                // Another concurrent worker claimed this item, skip safely
                continue;
            }

            $metrics['emails_processed']++;
            $toEmail = $emailRow['to_email'];
            $maskedTo = mask_email_safe($toEmail);
            $subject = substr($emailRow['subject'] ?? 'No Subject', 0, 50);
            $bodyText = $emailRow['body_text'] ?? '';
            $currentAttempts = (int)($emailRow['attempts'] ?? 0) + 1; // reflected increment

            // Respect Retry Limits (Permanent Failure at > 3 attempts)
            if ($currentAttempts > 3) {
                $emailQueue->markFailed($queueId, 'Max retry attempts (3) exceeded.');
                $metrics['emails_failed']++;
                scheduler_log('WARNING', "Email #{$queueId} to {$maskedTo} marked permanently failed (attempts: {$currentAttempts} > 3).", $isVerbose);
                continue;
            }

            // Check User Notification Preferences
            $type = null;
            if (strpos($bodyText, 'type:') === 0) {
                $type = trim(substr($bodyText, 5));
            }

            $recipientUser = $userModel->findByEmail($toEmail);
            if ($recipientUser && $type && !$prefService->isMandatorySecurityType($type)) {
                if (!$prefService->canSendEmail($recipientUser['id'], $type)) {
                    $emailQueue->markFailed($queueId, 'Skipped: Recipient disabled email notifications.');
                    $metrics['emails_skipped']++;
                    scheduler_log('INFO', "Email #{$queueId} to {$maskedTo} skipped (recipient opted out).", $isVerbose);
                    continue;
                }
            }

            // Dry-Run Simulation Mode
            if ($isDryRun) {
                $emailQueue->markSent($queueId);
                $metrics['emails_sent']++;
                scheduler_log('INFO', "[DRY-RUN] Simulated delivery for Email #{$queueId} to {$maskedTo} (Subject: '{$subject}')", $isVerbose);
                continue;
            }

            // Real SMTP Delivery
            try {
                $sent = $emailService->sendEmail($toEmail, $emailRow['subject'], $emailRow['body_html']);
                if ($sent) {
                    $emailQueue->markSent($queueId);
                    $metrics['emails_sent']++;
                    scheduler_log('INFO', "Email #{$queueId} to {$maskedTo} successfully sent via SMTP.", $isVerbose);
                } else {
                    $err = $emailService->getLastError() ?: 'SMTP transport handoff failed.';
                    $safeErr = substr(preg_replace('/[^\w\s\-\.\:\/]/', '', $err), 0, 200);
                    $emailQueue->markFailed($queueId, $safeErr);
                    $metrics['emails_failed']++;
                    scheduler_log('WARNING', "Email #{$queueId} to {$maskedTo} failed: {$safeErr}", $isVerbose);
                }
            } catch (\Throwable $e) {
                $safeErr = substr(preg_replace('/[^\w\s\-\.\:\/]/', '', $e->getMessage()), 0, 200);
                $emailQueue->markFailed($queueId, $safeErr);
                $metrics['emails_failed']++;
                scheduler_log('ERROR', "Email #{$queueId} to {$maskedTo} exception: {$safeErr}", $isVerbose);
            }
        }
    } catch (\Throwable $t) {
        scheduler_log('CRITICAL', "Exception during email queue processing: " . $t->getMessage(), true);
    }

    return $metrics;
}

// Execution Loop or One-Shot
$cycleConfig = [
    'batch_limit' => $batchLimit,
    'is_dry_run' => $isDryRun,
    'is_verbose' => $isVerbose
];

try {
    $db = Database::getInstance()->getConnection();

    if ($isDaemon) {
        echo "SecurePoll Queue Daemon active. Press Ctrl+C to terminate.\n";
        echo "Logging to: {$logFile}\n";

        while (true) {
            $cycleStart = microtime(true);
            $metrics = run_scheduler_cycle($db, $cycleConfig);
            $duration = round((microtime(true) - $cycleStart) * 1000, 2);

            if ($metrics['emails_processed'] > 0 || $metrics['polls_ended'] > 0 || $metrics['polls_started'] > 0 || $isVerbose) {
                scheduler_log('INFO', sprintf(
                    "Cycle completed in %d ms (Emails processed: %d, Sent: %d, Failed: %d, Skipped: %d)",
                    $duration,
                    $metrics['emails_processed'],
                    $metrics['emails_sent'],
                    $metrics['emails_failed'],
                    $metrics['emails_skipped']
                ), $isVerbose);
            }

            sleep($interval);
        }
    } else {
        // One-shot execution (default for Task Scheduler)
        $cycleStart = microtime(true);
        $metrics = run_scheduler_cycle($db, $cycleConfig);
        $duration = round((microtime(true) - $cycleStart) * 1000, 2);

        $summaryMsg = sprintf(
            "Scheduler run completed in %d ms (Polls ended: %d, Polls started: %d, Emails processed: %d, Sent: %d, Failed: %d, Skipped: %d).",
            $duration,
            $metrics['polls_ended'],
            $metrics['polls_started'],
            $metrics['emails_processed'],
            $metrics['emails_sent'],
            $metrics['emails_failed'],
            $metrics['emails_skipped']
        );
        scheduler_log('INFO', $summaryMsg, $isVerbose);
        if ($isVerbose || $metrics['emails_processed'] > 0) {
            echo $summaryMsg . "\n";
        }
    }
} catch (\Throwable $t) {
    scheduler_log('CRITICAL', "Fatal scheduler exception: " . $t->getMessage(), true);
    exit(1);
} finally {
    if (is_resource($lockHandle)) {
        flock($lockHandle, LOCK_UN);
        fclose($lockHandle);
    }
}
