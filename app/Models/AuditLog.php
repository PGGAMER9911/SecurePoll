<?php
namespace app\Models;

use app\Core\Model;
use PDO;

class AuditLog extends Model {
    public function log($userId, $action, $entityType, $entityId, $oldValues = null, $newValues = null, $ipAddress = null) {
        $stmt = $this->db->prepare("
            INSERT INTO audit_logs (user_id, action, entity_type, entity_id, old_values, new_values, ip_address) 
            VALUES (:user_id, :action, :entity_type, :entity_id, :old_values, :new_values, :ip)
        ");
        $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip' => $ipAddress
        ]);
    }
}
