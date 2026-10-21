<?php
namespace app\Models;

use app\Core\Model;
use PDO;

class TrustedDevice extends Model {
    public function isTokenTrusted($userId, $tokenHash) {
        if (empty($tokenHash)) {
            return false;
        }
        $stmt = $this->db->prepare("
            SELECT * FROM trusted_devices 
            WHERE user_id = :user_id 
              AND device_token_hash = :hash 
              AND (expires_at IS NULL OR expires_at > NOW())
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId, 'hash' => $tokenHash]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isTrusted($userId, $fingerprintId) {
        $stmt = $this->db->prepare("
            SELECT 1 FROM trusted_devices 
            WHERE user_id = :user_id 
              AND fingerprint_id = :fingerprint_id
              AND (expires_at IS NULL OR expires_at > NOW())
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId, 'fingerprint_id' => $fingerprintId]);
        return (bool)$stmt->fetch();
    }

    public function add($userId, $fingerprintId, $tokenHash = null, $expiryDays = 90) {
        $stmt = $this->db->prepare("
            INSERT INTO trusted_devices (user_id, fingerprint_id, device_token_hash, expires_at) 
            VALUES (:user_id, :fingerprint_id, :device_token_hash, DATE_ADD(NOW(), INTERVAL :days DAY))
        ");
        return $stmt->execute([
            'user_id' => $userId,
            'fingerprint_id' => $fingerprintId,
            'device_token_hash' => $tokenHash,
            'days' => $expiryDays
        ]);
    }

    public function revokeDeviceToken($userId, $tokenHash) {
        $stmt = $this->db->prepare("
            DELETE FROM trusted_devices 
            WHERE user_id = :user_id AND device_token_hash = :hash
        ");
        return $stmt->execute(['user_id' => $userId, 'hash' => $tokenHash]);
    }

    public function revokeAllForUser($userId) {
        $stmt = $this->db->prepare("DELETE FROM trusted_devices WHERE user_id = :user_id");
        return $stmt->execute(['user_id' => $userId]);
    }
}
