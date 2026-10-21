<?php
namespace app\Models;

use app\Core\Model;
use PDO;

class PasswordReset extends Model {
    public function createToken($userId, $email, $tokenHash, $expiryMinutes = 30) {
        // Invalidate any existing active tokens for this user to enforce single active token
        $this->invalidateUserTokens($userId);

        $stmt = $this->db->prepare("
            INSERT INTO password_resets (user_id, email, token_hash, expires_at)
            VALUES (:user_id, :email, :token_hash, DATE_ADD(NOW(), INTERVAL :expires MINUTE))
        ");
        return $stmt->execute([
            'user_id' => $userId,
            'email' => $email,
            'token_hash' => $tokenHash,
            'expires' => $expiryMinutes
        ]);
    }

    public function findActiveByTokenHash($tokenHash) {
        if (empty($tokenHash)) {
            return false;
        }

        $stmt = $this->db->prepare("
            SELECT * FROM password_resets 
            WHERE token_hash = :hash 
              AND used_at IS NULL 
              AND expires_at > NOW() 
            LIMIT 1
        ");
        $stmt->execute(['hash' => $tokenHash]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function markAsUsed($id) {
        $stmt = $this->db->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function invalidateUserTokens($userId) {
        $stmt = $this->db->prepare("
            UPDATE password_resets 
            SET expires_at = NOW() 
            WHERE user_id = :user_id 
              AND used_at IS NULL 
              AND expires_at > NOW()
        ");
        return $stmt->execute(['user_id' => $userId]);
    }
}
