<?php
/**
 * Isolated Automated Verification Test Suite for Email Queue & Scheduler Concurrency
 */

if (php_sapi_name() !== 'cli') {
    die("CLI only.\n");
}

require_once __DIR__ . '/app/Helpers/Env.php';
\app\Helpers\Env::load(__DIR__ . '/.env');
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Core/Model.php';
require_once __DIR__ . '/app/Models/EmailQueue.php';

use app\Core\Database;
use app\Models\EmailQueue;

echo "====================================================================\n";
echo " SecurePoll: Email Queue Worker & Concurrency Test Suite\n";
echo "====================================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest($cond, $title, $detail = '') {
    global $passCount, $failCount;
    if ($cond) {
        echo "  [PASS] {$title}\n";
        $passCount++;
    } else {
        echo "  [FAIL] {$title}" . ($detail ? " - {$detail}" : "") . "\n";
        $failCount++;
    }
}

$db = Database::getInstance()->getConnection();
$queue = new EmailQueue();
$testQueueIds = [];

try {
    // -------------------------------------------------------------
    // Test Group 1: Atomic Claiming & Race Condition Prevention
    // -------------------------------------------------------------
    echo "--- 1. Testing Atomic Claiming & Race Condition Prevention ---\n";

    // Insert isolated test email
    $queue->enqueue('test_atomic@example.local', 'Test Atomic Subject', '<p>Test</p>', 'Test text');
    $testId1 = (int)$db->lastInsertId();
    $testQueueIds[] = $testId1;

    assertTest($testId1 > 0, "Enqueued isolated test message #{$testId1}");

    // First claim should SUCCEED
    $claimedFirst = $queue->claimForProcessing($testId1);
    assertTest($claimedFirst === true, "First worker claim succeeds for item #{$testId1}");

    // Inspect database state
    $stmt = $db->prepare("SELECT status, attempts FROM email_queue WHERE id = ?");
    $stmt->execute([$testId1]);
    $row1 = $stmt->fetch(PDO::FETCH_ASSOC);
    assertTest($row1['status'] === 'processing' && (int)$row1['attempts'] === 1, "Database status is 'processing' with attempts=1");

    // Second simulated concurrent worker claim should FAIL (atomic row locking)
    $claimedSecond = $queue->claimForProcessing($testId1);
    assertTest($claimedSecond === false, "Concurrent second claim rejected (prevents duplicate sending)");

    // -------------------------------------------------------------
    // Test Group 2: Process Mutex (flock) Concurrency Guard
    // -------------------------------------------------------------
    echo "\n--- 2. Testing Process Mutex (scheduler.lock) ---\n";

    $lockFile = __DIR__ . '/storage/cache/scheduler.lock';
    $primaryLock = fopen($lockFile, 'c+');
    $locked = flock($primaryLock, LOCK_EX | LOCK_NB);
    assertTest($locked === true, "Acquired exclusive primary lock on scheduler.lock");

    // Spawn another PHP process running scheduler.php while lock is held
    $cmd = 'd:\\xampp\\php\\php.exe scheduler.php --once --verbose';
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w']
    ];
    $proc = proc_open($cmd, $descriptors, $pipes, __DIR__);
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exitCode = proc_close($proc);

    assertTest($exitCode === 0, "Overlapping scheduler process exited cleanly (exit code 0)");
    assertTest(strpos($stdout, 'Another scheduler process is actively running') !== false, 
        "Overlapping process recognized lock and reported busy notice");

    // Release primary lock
    flock($primaryLock, LOCK_UN);
    fclose($primaryLock);

    // -------------------------------------------------------------
    // Test Group 3: Stale Processing Recovery
    // -------------------------------------------------------------
    echo "\n--- 3. Testing Stale Processing Recovery ---\n";

    $queue->enqueue('test_stale@example.local', 'Stale Test', '<p>Stale</p>');
    $testId2 = (int)$db->lastInsertId();
    $testQueueIds[] = $testId2;

    // Simulate crash 15 minutes ago
    $db->prepare("UPDATE email_queue SET status = 'processing', attempts = 1, processed_at = DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 15 MINUTE) WHERE id = ?")
       ->execute([$testId2]);

    $reclaimedCount = $queue->reclaimStaleProcessing(10);
    assertTest($reclaimedCount >= 1, "Reclaimed stale processing item(s): {$reclaimedCount}");

    $stmt = $db->prepare("SELECT status FROM email_queue WHERE id = ?");
    $stmt->execute([$testId2]);
    $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
    assertTest($row2['status'] === 'pending', "Stale item successfully reverted to 'pending' for retry");

    // -------------------------------------------------------------
    // Test Group 4: Simulation / Dry-Run Processing
    // -------------------------------------------------------------
    echo "\n--- 4. Testing Simulation / Dry-Run Execution ---\n";

    $queue->enqueue('dryrun_verify@example.local', 'Dry Run Verification', '<p>Simulated Body</p>');
    $testId3 = (int)$db->lastInsertId();
    $testQueueIds[] = $testId3;

    // Run scheduler in dry-run mode for this item
    $cmdDryRun = 'd:\\xampp\\php\\php.exe scheduler.php --once --dry-run --verbose';
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w']
    ];
    $procDry = proc_open($cmdDryRun, $descriptors, $pipes, __DIR__);
    fclose($pipes[0]);
    $stdoutDry = stream_get_contents($pipes[1]);
    $stderrDry = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exitDry = proc_close($procDry);

    assertTest($exitDry === 0, "Dry-run execution completed with exit code 0");

    $stmt = $db->prepare("SELECT status, sent_at FROM email_queue WHERE id = ?");
    $stmt->execute([$testId3]);
    $row3 = $stmt->fetch(PDO::FETCH_ASSOC);
    assertTest($row3['status'] === 'sent' && !empty($row3['sent_at']), 
        "Dry-run simulation marked test item as sent without SMTP handshake");

    // -------------------------------------------------------------
    // Test Group 5: Sanitized Logging Verification
    // -------------------------------------------------------------
    echo "\n--- 5. Testing Sanitized Logging ---\n";

    $logPath = __DIR__ . '/storage/logs/scheduler.log';
    assertTest(file_exists($logPath), "scheduler.log created successfully");

    $logContent = file_get_contents($logPath);
    // Check that email is masked in logs (e.g., d***y@example.local)
    assertTest(strpos($logContent, 'd***y@example.local') !== false, "Recipient email is masked in log file");
    assertTest(strpos($logContent, 'password') === false, "Zero password/credential strings in log file");
    assertTest(strpos($logContent, '<p>Simulated Body</p>') === false, "Zero raw HTML email bodies exposed in log file");

} finally {
    // Clean up ONLY test records created during this test run
    if (!empty($testQueueIds)) {
        $inClause = implode(',', array_map('intval', $testQueueIds));
        $db->exec("DELETE FROM email_queue WHERE id IN ({$inClause})");
    }
    $db->exec("DELETE FROM email_queue WHERE to_email LIKE '%@example.local'");
    echo "\n  Cleaned up isolated unit test rows. All original queue records preserved.\n";
}

echo "\n====================================================================\n";
echo " Test Results: {$passCount} Passed, {$failCount} Failed\n";
echo "====================================================================\n";

if ($failCount > 0) {
    exit(1);
}
exit(0);
