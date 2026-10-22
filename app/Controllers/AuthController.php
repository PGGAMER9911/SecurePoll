<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Services\AuthService;
use app\Helpers\Security;
use app\Middleware\RateLimiter;
use app\Middleware\CsrfMiddleware;

class AuthController extends Controller {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function showLogin() {
        \app\Helpers\Session::init();
        if (\app\Helpers\Session::get('user_id')) {
            return $this->redirect('/dashboard');
        }
        return $this->view('auth/login');
    }

    public function showRegister() {
        \app\Helpers\Session::init();
        if (\app\Helpers\Session::get('user_id')) {
            return $this->redirect('/dashboard');
        }
        return $this->view('auth/register');
    }

    public function register() {
        RateLimiter::enforce('register', 3, 3600); // 3 registrations per hour per IP
        CsrfMiddleware::enforce();

        \app\Helpers\Session::init();
        if (\app\Helpers\Session::get('user_id')) {
            return $this->json(['success' => false, 'message' => 'You are already signed in.'], 400);
        }

        $accountType = strtolower(trim($_POST['account_type'] ?? 'personal'));
        if ($accountType === 'organization') {
            return $this->json(['success' => false, 'message' => 'Organization accounts are coming soon.'], 400);
        }

        $data = [
            'name' => Security::sanitize($_POST['name'] ?? ''),
            'email' => Security::sanitize($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? ''
        ];

        // Basic validation
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            return $this->json(['success' => false, 'message' => 'All fields are required.'], 400);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json(['success' => false, 'message' => 'Invalid email address.'], 400);
        }

        if (strlen($data['password']) < 8) {
            return $this->json(['success' => false, 'message' => 'Password must be at least 8 characters long.'], 400);
        }

        $deviceData = $this->getDeviceData();
        $result = $this->authService->register($data, $deviceData);

        return $this->json($result, $result['success'] ? 201 : 400);
    }

    public function login() {
        RateLimiter::enforce('login', 5, 300); // 5 attempts per 5 mins
        CsrfMiddleware::enforce();

        \app\Helpers\Session::init();
        if (\app\Helpers\Session::get('user_id')) {
            return $this->json(['success' => false, 'message' => 'You are already signed in.'], 400);
        }

        $email = Security::sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            return $this->json(['success' => false, 'message' => 'Email and password are required.'], 400);
        }

        $clientTheme = Security::sanitize($_POST['theme'] ?? ($_COOKIE['securepoll_theme'] ?? ''));
        if (!in_array($clientTheme, ['light', 'dark', 'system'], true)) {
            $clientTheme = null;
        }

        $deviceData = $this->getDeviceData();
        $deviceCookieToken = $_COOKIE['sp_device_token'] ?? null;
        $result = $this->authService->login($email, $password, $deviceData, $clientTheme, $deviceCookieToken);

        if (!empty($result['requires_otp'])) {
            \app\Helpers\Session::set('pending_device_verification', [
                'email' => $email,
                'fingerprint_id' => $result['fingerprint_id'] ?? null,
                'expires_at' => time() + 600
            ]);
        } elseif (!empty($result['success'])) {
            $intendedUrl = \app\Helpers\Session::get('intended_url');
            \app\Helpers\Session::remove('intended_url');
            $redirectDest = '/dashboard';
            if ($intendedUrl && is_string($intendedUrl)) {
                if (strpos($intendedUrl, '/') === 0 && strpos($intendedUrl, '//') !== 0 && strpos($intendedUrl, '/\\') !== 0) {
                    $redirectDest = $intendedUrl;
                }
            }
            $result['redirect'] = $redirectDest;
        }

        return $this->json($result, $result['success'] ? 200 : 401);
    }
    
    public function verifyOtp() {
        RateLimiter::enforce('verify_otp', 5, 300);
        CsrfMiddleware::enforce();
        
        $email = Security::sanitize($_POST['email'] ?? '');
        $otp = Security::sanitize($_POST['otp'] ?? '');
        $purpose = Security::sanitize($_POST['purpose'] ?? ''); // 'registration', 'new_device', 'password_reset'
        
        if (empty($email) || empty($otp) || empty($purpose)) {
             return $this->json(['success' => false, 'message' => 'Missing parameters.'], 400);
        }
        
        $clientTheme = Security::sanitize($_POST['theme'] ?? ($_COOKIE['securepoll_theme'] ?? ''));
        if (!in_array($clientTheme, ['light', 'dark', 'system'], true)) {
            $clientTheme = null;
        }
        
        if ($purpose === 'registration') {
            $result = $this->authService->verifyRegistrationOtp($email, $otp);
            if (!empty($result['success'])) {
                $result['redirect'] = '/login';
            }
        } else if ($purpose === 'new_device') {
            \app\Helpers\Session::init();
            $pending = \app\Helpers\Session::get('pending_device_verification');
            if (!$pending || empty($pending['email']) || strtolower($pending['email']) !== strtolower($email) || time() > ($pending['expires_at'] ?? 0)) {
                return $this->json(['success' => false, 'message' => 'Invalid or expired verification session. Please log in again.'], 400);
            }
            $fingerprintId = $pending['fingerprint_id'] ?? Security::sanitize($_POST['fingerprint_id'] ?? '');
            $result = $this->authService->verifyDeviceOtp($email, $otp, $fingerprintId, $_SERVER['REMOTE_ADDR'], $clientTheme);
            if (!empty($result['success'])) {
                \app\Helpers\Session::remove('pending_device_verification');
                $intendedUrl = \app\Helpers\Session::get('intended_url');
                \app\Helpers\Session::remove('intended_url');
                $redirectDest = '/dashboard';
                if ($intendedUrl && is_string($intendedUrl)) {
                    if (strpos($intendedUrl, '/') === 0 && strpos($intendedUrl, '//') !== 0 && strpos($intendedUrl, '/\\') !== 0) {
                        $redirectDest = $intendedUrl;
                    }
                }
                $result['redirect'] = $redirectDest;
            }
        } else if ($purpose === 'password_reset') {
            $result = $this->authService->verifyPasswordResetOtp($email, $otp);
        } else {
            return $this->json(['success' => false, 'message' => 'Invalid purpose.'], 400);
        }
        
        return $this->json($result, $result['success'] ? 200 : 400);
    }

    public function resendOtp() {
        RateLimiter::enforce('resend_otp_ip', 5, 300);
        CsrfMiddleware::enforce();

        \app\Helpers\Session::init();
        $email = Security::sanitize($_POST['email'] ?? '');
        $purpose = Security::sanitize($_POST['purpose'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['success' => false, 'message' => 'A valid email address is required.'], 400);
        }

        if (!in_array($purpose, ['registration', 'new_device', 'password_reset'], true)) {
            return $this->json(['success' => false, 'message' => 'Invalid verification purpose.'], 400);
        }

        $pending = null;
        if ($purpose === 'new_device') {
            $pending = \app\Helpers\Session::get('pending_device_verification');
            if (!$pending || empty($pending['email']) || strtolower($pending['email']) !== strtolower($email) || time() > ($pending['expires_at'] ?? 0)) {
                return $this->json(['success' => false, 'message' => 'Verification session expired. Please log in again.'], 400);
            }
        }

        // 60-second cooldown per email + purpose
        $cooldownKey = 'resend_cooldown_' . md5(strtolower($email) . '_' . $purpose);
        if (!RateLimiter::check($cooldownKey, 1, 60)) {
            return $this->json(['success' => false, 'message' => 'Please wait 60 seconds before requesting another code.', 'retry_after' => 60], 429);
        }

        $result = $this->authService->resendOtp($email, $purpose, $pending);
        return $this->json($result, $result['success'] ? 200 : 400);
    }
    
    public function showForgotPassword() {
        \app\Helpers\Session::init();
        if (\app\Helpers\Session::get('user_id')) {
            return $this->redirect('/dashboard');
        }
        return $this->view('auth/forgot_password');
    }

    public function showResetPassword() {
        \app\Helpers\Session::init();
        if (\app\Helpers\Session::get('user_id')) {
            return $this->redirect('/dashboard');
        }

        $rawToken = Security::sanitize($_GET['token'] ?? '');
        $validation = $this->authService->validateResetToken($rawToken);

        return $this->view('auth/reset_password', [
            'token' => $rawToken,
            'isValid' => $validation['valid'],
            'errorMessage' => $validation['valid'] ? '' : $validation['message']
        ]);
    }

    public function forgotPassword() {
        RateLimiter::enforce('forgot_password', 3, 900);
        CsrfMiddleware::enforce();
        
        $email = Security::sanitize($_POST['email'] ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['success' => false, 'message' => 'Please enter a valid email address.'], 400);
        }
        
        $result = $this->authService->requestPasswordReset($email, $_SERVER['REMOTE_ADDR']);
        return $this->json($result, $result['success'] ? 200 : 400);
    }

    public function processForgotPassword() {
        return $this->forgotPassword();
    }

    public function resetPassword() {
        RateLimiter::enforce('reset_password', 5, 900);
        CsrfMiddleware::enforce();
        
        $rawToken = Security::sanitize($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($rawToken)) {
            return $this->json(['success' => false, 'message' => 'Missing password reset token.'], 400);
        }

        if (empty($password) || empty($confirmPassword)) {
            return $this->json(['success' => false, 'message' => 'Both password fields are required.'], 400);
        }
        
        if (strlen($password) < 8) {
            return $this->json(['success' => false, 'message' => 'Password must be at least 8 characters long.'], 400);
        }

        if ($password !== $confirmPassword) {
            return $this->json(['success' => false, 'message' => 'New password and confirmation do not match.'], 400);
        }
        
        $result = $this->authService->resetPasswordWithToken($rawToken, $password, $_SERVER['REMOTE_ADDR']);
        return $this->json($result, $result['success'] ? 200 : 400);
    }

    public function processResetPassword() {
        return $this->resetPassword();
    }

    public function logout() {
        CsrfMiddleware::enforce();
        $this->authService->logout();
        \app\Helpers\Session::setFlash('success', 'You have been logged out successfully.');
        return $this->redirect('/login');
    }

    private function getDeviceData() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $parsed = $this->parseUserAgent($userAgent);

        $clientBrowser = Security::sanitize($_POST['browser'] ?? '');
        $clientOs = Security::sanitize($_POST['os'] ?? '');

        $browser = (!empty($clientBrowser) && $clientBrowser !== 'Unknown') ? $clientBrowser : $parsed['browser'];
        $os = (!empty($clientOs) && $clientOs !== 'Unknown') ? $clientOs : $parsed['os'];

        return [
            'browser' => $browser,
            'os' => $os,
            'user_agent' => $userAgent,
            'screen_resolution' => Security::sanitize($_POST['screen_resolution'] ?? ''),
            'language' => Security::sanitize($_POST['language'] ?? ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '')),
            'timezone' => Security::sanitize($_POST['timezone'] ?? ''),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ];
    }

    private function parseUserAgent($userAgent) {
        $browser = 'Unknown Browser';
        $os = 'Unknown OS';

        if (empty($userAgent)) {
            return ['browser' => $browser, 'os' => $os];
        }

        if (preg_match('/windows nt 10/i', $userAgent)) {
            $os = 'Windows 10/11';
        } elseif (preg_match('/windows nt 6\.3/i', $userAgent)) {
            $os = 'Windows 8.1';
        } elseif (preg_match('/windows nt 6\.2/i', $userAgent)) {
            $os = 'Windows 8';
        } elseif (preg_match('/windows nt 6\.1/i', $userAgent)) {
            $os = 'Windows 7';
        } elseif (preg_match('/windows/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $os = 'iOS';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $os = 'Linux';
        }

        if (preg_match('/edg/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/opr|opera/i', $userAgent)) {
            $browser = 'Opera';
        } elseif (preg_match('/chrome|crios/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/firefox|fxios/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome|crios/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/msie|trident/i', $userAgent)) {
            $browser = 'Internet Explorer';
        }

        return ['browser' => $browser, 'os' => $os];
    }
}
