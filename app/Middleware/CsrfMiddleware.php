<?php
namespace app\Middleware;

use app\Helpers\Security;

class CsrfMiddleware {
    public static function enforce() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            
            if (empty($token) || !Security::verifyCsrfToken($token)) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'CSRF token validation failed.']);
                exit;
            }
        }
    }
}
