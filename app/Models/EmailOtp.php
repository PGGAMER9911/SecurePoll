<?php
namespace app\Models;

use app\Core\Model;
use PDO;

class EmailOtp extends Model {
    public function createOtp($email, $otpCode, $purpose, $expiryMinutes = 10) {
        // Invalidate previous OTPs for this email and purpose
        $this->invalidateOtps($email, $purpose);

        $stmt = $this->db->prepare("
            INSERT INTO email_otps (email, otp_code, purpose, expires_at) 
            VALUES (:email, :otp_code, :purpose, DATE_ADD(NOW(), INTERVAL :expires MINUTE))
        ");
        return $stmt->execute([
            'email' => $email,
            'otp_code' => password_hash($otpCode, PASSWORD_DEFAULT),
            'purpose' => $purpose,
            'expires' => $expiryMinutes
        ]);
    }

    public function verifyOtp($email, $otpCode, $purpose) {
        $stmt = $this->db->prepare("
            SELECT * FROM email_otps 
            WHERE email = :email AND purpose = :purpose AND expires_at > NOW() 
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute(['email' => $email, 'purpose' => $purpose]);
        $otpRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$otpRecord) {
            return ['valid' => false, 'message' => 'OTP expired or not found.'];
        }

        if ($otpRecord['attempts'] >= 3) {
            return ['valid' => false, 'message' => 'Maximum attempts reached. Please request a new OTP.'];
        }

        if (password_verify($otpCode, $otpRecord['otp_code'])) {
            $this->invalidateOtps($email, $purpose);
            return ['valid' => true, 'message' => 'OTP verified successfully.'];
        }

        // Increment attempts
        $updateStmt = $this->db->prepare("UPDATE email_otps SET attempts = attempts + 1 WHERE id = :id");
        $updateStmt->execute(['id' => $otpRecord['id']]);

        return ['valid' => false, 'message' => 'Invalid OTP code.'];
    }

    public function invalidateOtps($email, $purpose) {
        $stmt = $this->db->prepare("UPDATE email_otps SET expires_at = NOW() WHERE email = :email AND purpose = :purpose AND expires_at > NOW()");
        $stmt->execute(['email' => $email, 'purpose' => $purpose]);
    }
}
