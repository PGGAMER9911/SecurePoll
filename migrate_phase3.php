<?php
require_once __DIR__ . '/app/Helpers/Env.php';
\app\Helpers\Env::load(__DIR__ . '/.env');
require_once __DIR__ . '/app/Core/Database.php';

use app\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    echo "Connected to database.\n";

    // 1. Alter polls.status
    $sql = "ALTER TABLE polls MODIFY COLUMN status ENUM('Draft', 'Scheduled', 'Active', 'Paused', 'Ended', 'Archived') DEFAULT 'Draft'";
    $db->exec($sql);
    echo "Successfully altered polls.status enum.\n";

    // Ensure votes table has correct indexes (already has UNIQUE(poll_id, voter_hash))
    echo "Database migrations for Phase 3 Audit complete.\n";
} catch (Exception $e) {
    die("Migration Failed: " . $e->getMessage() . "\n");
}
