<?php
namespace app\Services;

use app\Models\User;
use app\Models\EmailOtp;
use app\Models\LoginSession;
use app\Models\SecurityLog;
use app\Models\AuditLog;
use app\Helpers\Session;

class AuthService {
    private $userModel;
    private $emailOtpModel;
    private $loginSessionModel;
    private $securityLog;
    private $auditLog;
    private $emailService;
    private $deviceService;
    private $passwordResetModel;

    public function __construct($emailService = null) {
        $this->userModel = new User();
        $this->emailOtpModel = new EmailOtp();
        $this->loginSessionModel = new LoginSession();
        $this->securityLog = new SecurityLog();
        $this->auditLog = new AuditLog();
        $this->emailService = $emailService ?? new EmailService();
        $this->deviceService = new DeviceService();
        $this->passwordResetModel = new \app\Models\PasswordReset();
    }

    public function register($data, $deviceData) {
        $existing = $this->userModel->findByEmail($data['email']);
        if ($existing) {
            if ($existing['is_verified'] == 0) {
                // Update unverified user with new details
                $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
                $this->userModel->updateUnverifiedUser($existing['id'], $data['name'], $data['password_hash']);
                
                // Send new OTP
                $otp = $this->generateOtp();
                $this->emailOtpModel->createOtp($data['email'], $otp, 'registration');
                $this->emailService->sendOtp($data['email'], $otp, 'registration');

                return ['success' => true, 'message' => 'Registration pending. We have sent a new OTP to your email.', 'user_id' => $existing['id']];
            }

            $this->securityLog->log(null, 'REGISTRATION_FAILED', 'Low', 'Email already exists: ' . $data['email'], $deviceData['ip_address'], $deviceData['user_agent']);
            return ['success' => false, 'message' => 'Email already registered.'];
        }

        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        // Default role is 3 (Voter)
        $data['role_id'] = 3; 

        $userId = $this->userModel->create($data);
        
        $this->auditLog->log($userId, 'USER_REGISTERED', 'users', $userId, null, ['email' => $data['email']], $deviceData['ip_address']);

        // Send OTP
        $otp = $this->generateOtp();
        $this->emailOtpModel->createOtp($data['email'], $otp, 'registration');
        $this->emailService->sendOtp($data['email'], $otp, 'registration');

        return ['success' => true, 'message' => 'Registration successful. Please verify your email.', 'user_id' => $userId];
    }

    public function login($email, $password, $deviceData, $clientTheme = null, $deviceCookieToken = null) {
        $user = $this->userModel->findByEmail($email);
        
        if (!$user || empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
            $this->securityLog->log(null, 'LOGIN_FAILED', 'Medium', 'Invalid login attempt for: ' . $email, $deviceData['ip_address'], $deviceData['user_agent']);
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }

        if (!$user['is_verified']) {
            return ['success' => false, 'message' => 'Please verify your email first.', 'requires_verification' => true];
        }

        // Check Device Trust using cookie token and device metadata
        $deviceResult = $this->deviceService->handleDeviceTrust($user['id'], $deviceData, $deviceCookieToken);

        if (!$deviceResult['is_trusted']) {
            // Send OTP for new device
            $otp = $this->generateOtp();
            $this->emailOtpModel->createOtp($email, $otp, 'new_device');
            $this->emailService->sendOtp($email, $otp, 'new_device');
            
            $this->securityLog->log($user['id'], 'NEW_DEVICE_DETECTED', 'Low', 'Login attempt from new device.', $deviceData['ip_address'], $deviceData['user_agent']);
            
            return ['success' => true, 'requires_otp' => true, 'message' => 'New device detected. OTP sent to email.', 'fingerprint_id' => $deviceResult['fingerprint_id']];
        }

        // Generate Session with theme reconciliation
        return $this->createActiveSession($user['id'], $deviceResult['fingerprint_id'], $deviceData['ip_address'], $clientTheme);
    }

    public function verifyRegistrationOtp($email, $otp) {
        $result = $this->emailOtpModel->verifyOtp($email, $otp, 'registration');
        if ($result['valid']) {
            $user = $this->userModel->findByEmail($email);
            if ($user) {
                $this->userModel->markAsVerified($user['id']);
                $this->auditLog->log($user['id'], 'EMAIL_VERIFIED', 'users', $user['id']);
                return ['success' => true, 'message' => 'Email verified successfully. You can now log in.'];
            }
        }
        return ['success' => false, 'message' => $result['message']];
    }

    public function verifyDeviceOtp($email, $otp, $fingerprintId, $ipAddress, $clientTheme = null) {
        $result = $this->emailOtpModel->verifyOtp($email, $otp, 'new_device');
        if ($result['valid']) {
            $user = $this->userModel->findByEmail($email);
            if ($user) {
                $this->deviceService->trustDevice($user['id'], $fingerprintId);
                $this->auditLog->log($user['id'], 'DEVICE_TRUSTED', 'device_fingerprints', $fingerprintId, null, null, $ipAddress);
                return $this->createActiveSession($user['id'], $fingerprintId, $ipAddress, $clientTheme);
            }
        }
        return ['success' => false, 'message' => $result['message']];
    }

    private function createActiveSession($userId, $fingerprintId, $ipAddress, $clientTheme = null) {
        if (!headers_sent()) {
            session_regenerate_id(true);
        }
        $token = bin2hex(random_bytes(32));
        $this->loginSessionModel->createSession($userId, $token, $fingerprintId, $ipAddress);
        
        $user = $this->userModel->findById($userId);
        
        Session::set('auth_token', $token);
        Session::set('user_id', $userId);
        $finalTheme = 'system';
        if ($user) {
            Session::set('user_name', $user['name']);
            Session::set('user_email', $user['email']);
            
            $dbTheme = $user['theme'] ?? 'system';
            $anonTheme = $clientTheme ?? ($_COOKIE['securepoll_theme'] ?? null);
            if (!in_array($anonTheme, ['light', 'dark', 'system'], true)) {
                $anonTheme = null;
            }

            // Reconcile theme preference:
            // If DB theme is 'system' and guest chose an explicit theme ('dark'/'light'), persist guest choice to DB
            // Otherwise, keep the user account's saved preference
            $finalTheme = $dbTheme;
            if (($dbTheme === 'system' || empty($dbTheme)) && $anonTheme && $anonTheme !== 'system') {
                $finalTheme = $anonTheme;
                $this->userModel->updateTheme($userId, $finalTheme);
            }

            Session::set('theme', $finalTheme);
            if (!headers_sent()) {
                setcookie('securepoll_theme', $finalTheme, [
                    'expires' => time() + 31536000,
                    'path' => '/',
                    'httponly' => false,
                    'samesite' => 'Lax'
                ]);
            }
        }
        
        $this->securityLog->log($userId, 'LOGIN_SUCCESS', 'Low', 'User logged in.', $ipAddress, null);

        return ['success' => true, 'message' => 'Login successful.', 'token' => $token, 'theme' => $finalTheme];
    }


    public function logout() {
        if (Session::has('auth_token')) {
            $this->loginSessionModel->revokeSession(Session::get('auth_token'));
            $this->securityLog->log(Session::get('user_id'), 'LOGOUT', 'Low', 'User logged out.', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', null);
        }
        Session::destroy(false);
    }

    public function requestPasswordReset($email, $ipAddress) {
        $user = $this->userModel->findByEmail($email);
        if ($user) {
            $rawToken = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $rawToken);
            $this->passwordResetModel->createToken($user['id'], $email, $tokenHash, 30);

            $appUrl = \app\Helpers\Env::get('APP_URL', 'http://localhost/SecurePoll/public');
            $resetLink = rtrim($appUrl, '/') . '/reset-password?token=' . $rawToken;
            $this->emailService->sendPasswordResetLink($email, $resetLink);
            $this->securityLog->log($user['id'], 'PASSWORD_RESET_REQUESTED', 'Medium', 'Password reset requested via email link.', $ipAddress, null);
        }
        // Always return generic success to prevent email enumeration
        return ['success' => true, 'message' => 'If your email is registered, a password reset link has been sent to your email.'];
    }

    public function validateResetToken($rawToken) {
        if (empty($rawToken)) {
            return ['valid' => false, 'message' => 'Missing password reset token.'];
        }
        $tokenHash = hash('sha256', $rawToken);
        $record = $this->passwordResetModel->findActiveByTokenHash($tokenHash);
        if (!$record) {
            return ['valid' => false, 'message' => 'This password reset link is invalid, expired, or has already been used.'];
        }
        return ['valid' => true, 'record' => $record];
    }

    public function resetPasswordWithToken($rawToken, $password, $ipAddress) {
        if (empty($rawToken)) {
            return ['success' => false, 'message' => 'Missing password reset token.'];
        }
        if (strlen($password) < 8) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters long.'];
        }

        $tokenHash = hash('sha256', $rawToken);
        $record = $this->passwordResetModel->findActiveByTokenHash($tokenHash);
        if (!$record) {
            return ['success' => false, 'message' => 'This password reset link is invalid, expired, or has already been used.'];
        }

        $user = $this->userModel->findById($record['user_id']);
        if (!$user) {
            return ['success' => false, 'message' => 'User account not found.'];
        }

        $newHash = password_hash($password, PASSWORD_DEFAULT);
        
        $db = \app\Core\Database::getInstance()->getConnection();
        $db->beginTransaction();
        try {
            $this->userModel->updatePassword($user['id'], $newHash);
            $this->passwordResetModel->markAsUsed($record['id']);
            $this->passwordResetModel->invalidateUserTokens($user['id']);

            // Invalidate all existing login sessions for security
            $this->loginSessionModel->revokeAllUserSessions($user['id']);

            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => 'Failed to reset password. Please try again.'];
        }

        $this->securityLog->log($user['id'], 'PASSWORD_RESET_SUCCESS', 'High', 'Password reset successfully via reset link.', $ipAddress, null);
        $this->auditLog->log($user['id'], 'PASSWORD_CHANGED', 'users', $user['id'], null, null, $ipAddress);

        return ['success' => true, 'message' => 'Password reset successfully. You can now log in with your new password.'];
    }

    public function resendOtp($email, $purpose, $pendingVerification = null) {
        $purpose = strtolower(trim($purpose));

        if ($purpose === 'registration') {
            $user = $this->userModel->findByEmail($email);
            if ($user && empty($user['is_verified'])) {
                $otp = $this->generateOtp();
                $this->emailOtpModel->createOtp($email, $otp, 'registration');
                $this->emailService->sendOtp($email, $otp, 'registration');
            }
            // Anti-enumeration: return uniform success message
            return ['success' => true, 'message' => 'A new verification code has been sent if the email is pending verification.'];
        }

        if ($purpose === 'new_device') {
            $user = $this->userModel->findByEmail($email);
            // Verify binding with pending verification session
            if ($user && !empty($user['is_verified']) && $pendingVerification && 
                !empty($pendingVerification['email']) && 
                strtolower($pendingVerification['email']) === strtolower($email) && 
                time() <= ($pendingVerification['expires_at'] ?? 0)) {
                
                $otp = $this->generateOtp();
                $this->emailOtpModel->createOtp($email, $otp, 'new_device');
                $this->emailService->sendOtp($email, $otp, 'new_device');
            }
            return ['success' => true, 'message' => 'A new verification code has been sent if the request is valid.'];
        }

        if ($purpose === 'password_reset') {
            $user = $this->userModel->findByEmail($email);
            if ($user) {
                $otp = $this->generateOtp();
                $this->emailOtpModel->createOtp($email, $otp, 'password_reset');
                $this->emailService->sendOtp($email, $otp, 'password_reset');
            }
            return ['success' => true, 'message' => 'If your email is registered, a new verification code has been sent.'];
        }

        return ['success' => false, 'message' => 'Invalid verification purpose.'];
    }

    private function generateOtp(): string {
        return sprintf("%06d", random_int(0, 999999));
    }
}

