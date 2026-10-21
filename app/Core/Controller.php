<?php
namespace app\Core;

class Controller {
    protected function preventCaching() {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Expires: 0');
    }

    protected function requireAuth() {
        $this->preventCaching();
        \app\Helpers\Session::init();
        if (!\app\Helpers\Session::get('user_id')) {
            \app\Helpers\Session::setFlash('error', 'You must be logged in to access this page.');
            $this->redirect('/login');
        }
    }

    protected function requireAuthApi() {
        $this->preventCaching();
        \app\Helpers\Session::init();
        if (!\app\Helpers\Session::get('user_id')) {
            $this->json(['success' => false, 'message' => 'Unauthorized access. Please log in.'], 401);
        }
    }

    protected function requireCsrfApi() {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (empty($token) || !\app\Helpers\Security::verifyCsrfToken($token)) {
            $this->json(['success' => false, 'message' => 'CSRF token validation failed.'], 403);
        }
    }

    protected function view($view, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View does not exist: $viewFile");
        }
    }

    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($url) {
        header('Location: /SecurePoll/public' . $url);
        exit;
    }
}



