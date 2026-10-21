<?php
namespace app\Models;

use app\Core\Model;
use PDO;

class DeviceFingerprint extends Model {
    public function findByHash($userId, $deviceHash) {
        $stmt = $this->db->prepare("
            SELECT * FROM device_fingerprints 
            WHERE user_id = :user_id AND device_hash = :hash
        ");
        $stmt->execute(['user_id' => $userId, 'hash' => $deviceHash]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($userId, $data) {
        $stmt = $this->db->prepare("
            INSERT INTO device_fingerprints 
            (user_id, device_hash, browser, os, user_agent, screen_resolution, language, timezone, ip_address) 
            VALUES (:user_id, :device_hash, :browser, :os, :user_agent, :screen, :lang, :tz, :ip)
        ");
        $stmt->execute([
            'user_id' => $userId,
            'device_hash' => $data['device_hash'],
            'browser' => $data['browser'] ?? null,
            'os' => $data['os'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
            'screen' => $data['screen_resolution'] ?? null,
            'lang' => $data['language'] ?? null,
            'tz' => $data['timezone'] ?? null,
            'ip' => $data['ip_address'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function markAsTrusted($id) {
        $stmt = $this->db->prepare("UPDATE device_fingerprints SET is_trusted = 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
