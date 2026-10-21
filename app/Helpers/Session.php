<?php
namespace app\Helpers;

class Session {
    private static $isValidated = false;

    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_strict_mode', '1');
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                      (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
                      (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        if ($key === 'user_id' && isset($_SESSION['user_id']) && !self::$isValidated) {
            $token = $_SESSION['auth_token'] ?? '';
            if ($token) {
                $loginSessionModel = new \app\Models\LoginSession();
                if (!$loginSessionModel->verifySession($token)) {
                    unset($_SESSION['user_id'], $_SESSION['auth_token']);
                    return $default;
                }
                self::$isValidated = true;
            } else {
                unset($_SESSION['user_id']);
                return $default;
            }
        }
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    public static function has($key) {
        return isset($_SESSION[$key]);
    }

    public static function remove($key) {
        if (self::has($key)) {
            unset($_SESSION[$key]);
        }
    }

    public static function setFlash($key, $message) {
        if (session_status() === PHP_SESSION_NONE) {
            self::init();
        }
        $_SESSION['flash'][$key] = $message;
    }

    public static function getFlash($key) {
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }

    public static function destroy($expireCookie = true) {
        // Unset all session variables
        $_SESSION = array();

        // Expire the session cookie if requested
        if ($expireCookie && ini_get("session.use_cookies") && !headers_sent()) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destroy the session storage on server
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        self::$isValidated = false;
    }

    public static function resetValidationState() {
        self::$isValidated = false;
    }
}



