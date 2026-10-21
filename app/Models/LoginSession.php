<?php
namespace app\Models;

use app\Core\Model;
use PDO;

class LoginSession extends Model {
    public function createSession($userId, $token, $fingerprintId, $ipAddress, $expiryDays = 30) {
        $stmt = $this->db->prepare("
            INSERT INTO login_sessions (user_id, session_token, fingerprint_id, ip_address, expires_at) 
            VALUES (:user_id, :token, :fingerprint_id, :ip, DATE_ADD(NOW(), INTERVAL :days DAY))
        ");
        $stmt->execute([
            'user_id' => $userId,
            'token' => hash('sha256', $token),
            'fingerprint_id' => $fingerprintId,
            'ip' => $ipAddress,
            'days' => $expiryDays
        ]);
    }

    public function verifySession($token) {
        $hashedToken = hash('sha256', $token);
        $stmt = $this->db->prepare("
            SELECT * FROM login_sessions 
            WHERE session_token = :token AND is_active = 1 AND expires_at > NOW()
        ");
        $stmt->execute(['token' => $hashedToken]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function revokeSession($token) {
        $hashedToken = hash('sha256', $token);
        $stmt = $this->db->prepare("UPDATE login_sessions SET is_active = 0 WHERE session_token = :token");
        return $stmt->execute(['token' => $hashedToken]);
    }
    
    public function revokeAllUserSessions($userId) {
        $stmt = $this->db->prepare("UPDATE login_sessions SET is_active = 0 WHERE user_id = :user_id");
        return $stmt->execute(['user_id' => $userId]);
    }

    public function revokeOtherUserSessions($userId, $currentToken) {
        $hashedToken = hash('sha256', $currentToken);
        $stmt = $this->db->prepare("
            UPDATE login_sessions 
            SET is_active = 0 
            WHERE user_id = :user_id AND session_token != :current_token AND is_active = 1
        ");
        return $stmt->execute([
            'user_id' => $userId,
            'current_token' => $hashedToken
        ]);
    }
}
