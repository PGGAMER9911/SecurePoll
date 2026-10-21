<?php
namespace app\Models;

use app\Core\Model;
use PDO;

class NotificationPreference extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    public function getForUser($userId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM notification_preferences WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            $prefs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $result = [];
            foreach ($prefs as $pref) {
                $result[$pref['notification_type']] = [
                    'in_app' => isset($pref['in_app']) ? (bool)$pref['in_app'] : true,
                    'email' => isset($pref['email']) ? (bool)$pref['email'] : true
                ];
            }
            return $result;
        } catch (\PDOException $e) {
            error_log("NotificationPreference::getForUser error: " . $e->getMessage());
            return [];
        }
    }

    public function getForUserAndType($userId, $type) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM notification_preferences WHERE user_id = :user_id AND notification_type = :type");
            $stmt->execute(['user_id' => $userId, 'type' => $type]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (\PDOException $e) {
            error_log("NotificationPreference::getForUserAndType error: " . $e->getMessage());
            return null;
        }
    }

    public function set($userId, $type, $inApp, $email) {
        try {
            $sql = "INSERT INTO notification_preferences (user_id, notification_type, in_app, email) 
                    VALUES (:user_id, :type, :in_app, :email)
                    ON DUPLICATE KEY UPDATE in_app = :in_app2, email = :email2";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'user_id' => $userId,
                'type' => $type,
                'in_app' => $inApp ? 1 : 0,
                'email' => $email ? 1 : 0,
                'in_app2' => $inApp ? 1 : 0,
                'email2' => $email ? 1 : 0
            ]);
        } catch (\PDOException $e) {
            error_log("NotificationPreference::set error: " . $e->getMessage());
            return false;
        }
    }
    
    public function setPreset($userId, $preset) {
        try {
            $stmt = $this->db->prepare("UPDATE users SET notification_preset = :preset WHERE id = :id");
            return $stmt->execute(['preset' => $preset, 'id' => $userId]);
        } catch (\PDOException $e) {
            error_log("NotificationPreference::setPreset error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getPreset($userId) {
        try {
            $stmt = $this->db->prepare("SELECT notification_preset FROM users WHERE id = :id");
            $stmt->execute(['id' => $userId]);
            return $stmt->fetchColumn() ?: 'Standard';
        } catch (\PDOException $e) {
            error_log("NotificationPreference::getPreset error: " . $e->getMessage());
            return 'Standard';
        }
    }
}
