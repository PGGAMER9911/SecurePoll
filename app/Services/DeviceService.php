<?php
namespace app\Services;

use app\Models\DeviceFingerprint;
use app\Models\TrustedDevice;

class DeviceService {
    private $fingerprintModel;
    private $trustedDeviceModel;

    public function __construct() {
        $this->fingerprintModel = new DeviceFingerprint();
        $this->trustedDeviceModel = new TrustedDevice();
    }

    public function handleDeviceTrust($userId, $deviceData, $deviceCookieToken = null) {
        $deviceHash = $this->generateDeviceHash($deviceData);
        $deviceData['device_hash'] = $deviceHash;

        $fingerprint = $this->fingerprintModel->findByHash($userId, $deviceHash);
        
        if (!$fingerprint) {
            $fingerprintId = $this->fingerprintModel->create($userId, $deviceData);
            $fingerprint = ['id' => $fingerprintId];
        }

        // Verify device trust using cryptographically secure cookie token
        if (!empty($deviceCookieToken)) {
            $tokenHash = hash('sha256', $deviceCookieToken);
            $trustedRecord = $this->trustedDeviceModel->isTokenTrusted($userId, $tokenHash);
            if ($trustedRecord) {
                $this->fingerprintModel->markAsTrusted($fingerprint['id']);
                return ['is_trusted' => true, 'fingerprint_id' => $fingerprint['id']];
            }
        }

        // Untrusted device: requires OTP verification
        return ['is_trusted' => false, 'fingerprint_id' => $fingerprint['id']];
    }

    public function trustDevice($userId, $fingerprintId) {
        $deviceToken = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $deviceToken);

        $this->fingerprintModel->markAsTrusted($fingerprintId);
        $this->trustedDeviceModel->add($userId, $fingerprintId, $tokenHash, 90);

        if (!headers_sent()) {
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                      (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
                      (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
            setcookie('sp_device_token', $deviceToken, [
                'expires' => time() + (90 * 86400),
                'path' => '/',
                'httponly' => true,
                'secure' => $secure,
                'samesite' => 'Lax'
            ]);
        }

        return $deviceToken;
    }

    private function generateDeviceHash($data) {
        $stringToHash = ($data['browser'] ?? 'Unknown') . '|' . 
                        ($data['os'] ?? 'Unknown') . '|' . 
                        ($data['user_agent'] ?? '') . '|' . 
                        ($data['screen_resolution'] ?? '') . '|' . 
                        ($data['language'] ?? '') . '|' . 
                        ($data['timezone'] ?? '');
        return hash('sha256', $stringToHash);
    }
}
