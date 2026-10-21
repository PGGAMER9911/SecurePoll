<?php
namespace app\Models;

use app\Core\Model;
use PDO;

class SecurityLog extends Model {
    public function log($userId, $eventType, $severity, $description, $ipAddress, $userAgent) {
        $stmt = $this->db->prepare("
            INSERT INTO security_logs (user_id, event_type, severity, description, ip_address, user_agent) 
            VALUES (:user_id, :event_type, :severity, :description, :ip, :user_agent)
        ");
        $stmt->execute([
            'user_id' => $userId,
            'event_type' => $eventType,
            'severity' => $severity,
            'description' => $description,
            'ip' => $ipAddress,
            'user_agent' => $userAgent
        ]);
    }
}
