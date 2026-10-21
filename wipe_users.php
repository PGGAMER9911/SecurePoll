<?php
// Security Guard: Prevent HTTP access to maintenance scripts
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Access denied: This script can only be run via CLI.\n");
}

if (!isset($argv[1]) || $argv[1] !== '--confirm-wipe') {
    die("Safety warning: This script truncates user data. To execute, run: php wipe_users.php --confirm-wipe\n");
}

require_once __DIR__ . '/app/Helpers/Env.php';
\app\Helpers\Env::load(__DIR__ . '/.env');
require_once __DIR__ . '/app/Core/Database.php';

use app\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    // The user wants to remove all registered users, but we might want to keep the admin or just truncate everything.
    // Wait, the user said "alredy register mail he to us ki info database ke sare registered users ko remove kar do"
    // So let's delete all users and email_otps.
    
    // We should truncate or delete from users and email_otps.
    // If there are foreign key constraints (like polls.creator_id references users.id),
    // we should temporarily disable foreign key checks.
    
    $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $db->exec("TRUNCATE TABLE users;");
    $db->exec("TRUNCATE TABLE email_otps;");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1;");
    
    echo "Successfully removed all registered users and OTPs to allow fresh testing.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
