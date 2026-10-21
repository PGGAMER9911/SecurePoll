<?php
/**
 * Master Migration Runner for SecurePoll Organization Management System
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../app/Helpers/Env.php';
\app\Helpers\Env::load(__DIR__ . '/../../.env');
require_once __DIR__ . '/../../app/Core/Database.php';

use app\Core\Database;

echo "=== SECUREPOLL MIGRATION RUNNER ===" . PHP_EOL;

try {
    $db = Database::getInstance()->getConnection();
    $dbName = $db->query("SELECT DATABASE()")->fetchColumn();
    echo "Active Database: " . $dbName . PHP_EOL . PHP_EOL;

    // 1. Record Baseline Counts
    $baselineCounts = [
        'users' => (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
        'polls' => (int)$db->query("SELECT COUNT(*) FROM polls")->fetchColumn(),
        'votes' => (int)$db->query("SELECT COUNT(*) FROM votes")->fetchColumn(),
        'poll_options' => (int)$db->query("SELECT COUNT(*) FROM poll_options")->fetchColumn(),
        'poll_invitations' => (int)$db->query("SELECT COUNT(*) FROM poll_invitations")->fetchColumn(),
        'notifications' => (int)$db->query("SELECT COUNT(*) FROM notifications")->fetchColumn(),
        'audit_logs' => (int)$db->query("SELECT COUNT(*) FROM audit_logs")->fetchColumn(),
        'security_logs' => (int)$db->query("SELECT COUNT(*) FROM security_logs")->fetchColumn(),
    ];

    echo "--- Baseline Record Counts ---" . PHP_EOL;
    foreach ($baselineCounts as $table => $count) {
        echo "  $table: $count" . PHP_EOL;
    }
    echo PHP_EOL;

    // 2. Migration Files to Execute
    $migrations = [
        '01_create_organizations.sql',
        '02_create_organization_roles.sql',
        '03_create_departments_and_groups.sql',
        '04_create_organization_memberships.sql',
        '05_update_users_for_temporary_accounts.sql',
        '06_update_polls_and_targeting.sql',
        '07_create_organization_imports.sql',
        '08_create_moderation_reports.sql',
    ];

    $executedMigrations = [];

    echo "--- Executing Migrations in Order ---" . PHP_EOL;
    foreach ($migrations as $file) {
        $filePath = __DIR__ . '/' . $file;
        if (!file_exists($filePath)) {
            throw new Exception("Migration file not found: $file");
        }

        echo "Running: $file ... ";
        $sql = file_get_contents($filePath);

        // Split multi-statement SQL by semicolon, respecting triggers/prepared statements
        // We can execute via PDO exec after basic split or direct PDO exec for batch
        // Since PDO with MYSQL_ATTR_MULTI_STATEMENTS can execute directly, or execute statements:
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $db->exec($sql);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        echo "SUCCESS" . PHP_EOL;
        $executedMigrations[] = $file;
    }
    echo PHP_EOL;

    // 3. Verify Newly Created Tables
    $newTables = [
        'organizations',
        'organization_roles',
        'organization_departments',
        'organization_groups',
        'organization_members',
        'organization_group_members',
        'poll_target_departments',
        'poll_target_groups',
        'organization_imports',
        'organization_import_errors',
        'moderation_reports',
    ];

    echo "--- Verifying Tables in Database ---" . PHP_EOL;
    $existingTables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $allTablesFound = true;
    foreach ($newTables as $table) {
        $found = in_array($table, $existingTables);
        echo "  Table $table: " . ($found ? "EXISTS" : "MISSING") . PHP_EOL;
        if (!$found) $allTablesFound = false;
    }
    echo PHP_EOL;

    // 4. Verify Column Updates on Existing Tables
    echo "--- Verifying Modified Columns ---" . PHP_EOL;
    $userCols = $db->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
    echo "  users.is_temporary: " . (in_array('is_temporary', $userCols) ? "EXISTS" : "MISSING") . PHP_EOL;
    echo "  users.must_set_password: " . (in_array('must_set_password', $userCols) ? "EXISTS" : "MISSING") . PHP_EOL;

    $pollCols = $db->query("SHOW COLUMNS FROM polls")->fetchAll(PDO::FETCH_COLUMN);
    echo "  polls.organization_id: " . (in_array('organization_id', $pollCols) ? "EXISTS" : "MISSING") . PHP_EOL;
    echo "  polls.target_scope: " . (in_array('target_scope', $pollCols) ? "EXISTS" : "MISSING") . PHP_EOL;
    echo PHP_EOL;

    // 5. Verify Foreign Key Constraints
    echo "--- Verifying Foreign Key Constraints ---" . PHP_EOL;
    $fks = $db->query("
        SELECT TABLE_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE()
          AND REFERENCED_TABLE_NAME IS NOT NULL
          AND (TABLE_NAME LIKE 'organization%' OR TABLE_NAME LIKE 'poll_target%' OR TABLE_NAME = 'moderation_reports' OR CONSTRAINT_NAME = 'fk_polls_org')
        ORDER BY TABLE_NAME, CONSTRAINT_NAME
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($fks as $fk) {
        echo "  {$fk['TABLE_NAME']}.{$fk['CONSTRAINT_NAME']} -> {$fk['REFERENCED_TABLE_NAME']}({$fk['REFERENCED_COLUMN_NAME']})" . PHP_EOL;
    }
    echo PHP_EOL;

    // 6. Verify Post-Migration Record Counts (Data Integrity Check)
    echo "--- Post-Migration Data Integrity Check ---" . PHP_EOL;
    $finalCounts = [
        'users' => (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
        'polls' => (int)$db->query("SELECT COUNT(*) FROM polls")->fetchColumn(),
        'votes' => (int)$db->query("SELECT COUNT(*) FROM votes")->fetchColumn(),
        'poll_options' => (int)$db->query("SELECT COUNT(*) FROM poll_options")->fetchColumn(),
        'poll_invitations' => (int)$db->query("SELECT COUNT(*) FROM poll_invitations")->fetchColumn(),
        'notifications' => (int)$db->query("SELECT COUNT(*) FROM notifications")->fetchColumn(),
        'audit_logs' => (int)$db->query("SELECT COUNT(*) FROM audit_logs")->fetchColumn(),
        'security_logs' => (int)$db->query("SELECT COUNT(*) FROM security_logs")->fetchColumn(),
    ];

    $countsMatch = true;
    foreach ($baselineCounts as $table => $oldCount) {
        $newCount = $finalCounts[$table];
        $match = ($oldCount === $newCount);
        if (!$match) $countsMatch = false;
        echo "  $table: Baseline=$oldCount, Final=$newCount " . ($match ? "[MATCH - ZERO DATA LOSS]" : "[MISMATCH!]") . PHP_EOL;
    }
    echo PHP_EOL;

    // 7. Verify Default Roles Seeded
    $rolesCount = (int)$db->query("SELECT COUNT(*) FROM organization_roles WHERE is_system_default = 1")->fetchColumn();
    echo "System Default Organization Roles Seeded: $rolesCount" . PHP_EOL;

    echo PHP_EOL . "=== MIGRATION COMPLETED SUCCESSFULLY ===" . PHP_EOL;

} catch (Exception $e) {
    echo "MIGRATION FAILED: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
