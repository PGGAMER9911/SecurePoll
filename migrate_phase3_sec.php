<?php
require_once __DIR__ . '/app/Helpers/Env.php';
\app\Helpers\Env::load(__DIR__ . '/.env');
require_once __DIR__ . '/app/Core/Database.php';

use app\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    echo "Connected to database.\n";

    $sql = "ALTER TABLE poll_invitations 
            ADD COLUMN expires_at TIMESTAMP NULL AFTER sent_at,
            ADD COLUMN used_at TIMESTAMP NULL AFTER expires_at,
            ADD COLUMN bound_device_token VARCHAR(255) NULL AFTER used_at";
            
    $db->exec($sql);
    echo "Successfully altered poll_invitations table for enhanced security.\n";

} catch (Exception $e) {
    // If columns already exist, this might fail, which is fine to catch.
    echo "Migration Note: " . $e->getMessage() . "\n";
}
