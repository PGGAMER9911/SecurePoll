<?php
namespace app\Middleware;

use app\Models\LoginSession;
use app\Helpers\Session;

class AuthMiddleware {
    public static function enforce() {
        Session::init();
        $token = Session::get('auth_token');
        
        if (!$token) {
            self::unauthorized();
        }

        $loginSessionModel = new LoginSession();
        $session = $loginSessionModel->verifySession($token);

        if (!$session) {
            Session::remove('auth_token');
            Session::remove('user_id');
            self::unauthorized();
        }
    }

    private static function unauthorized() {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
        exit;
    }
}
