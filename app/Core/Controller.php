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
            $reqMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            if ($reqMethod === 'GET') {
                $rawUri = $_SERVER['REQUEST_URI'] ?? '/dashboard';
                $parsedPath = parse_url($rawUri, PHP_URL_PATH);
                $cleanPath = str_replace('/SecurePoll/public', '', $parsedPath);
                if (!in_array($cleanPath, ['/login', '/logout', '/register', '/verify-otp', '/resend-otp'], true)) {
                    $qs = parse_url($rawUri, PHP_URL_QUERY);
                    $fullTarget = $cleanPath . ($qs ? '?' . $qs : '');
                    \app\Helpers\Session::set('intended_url', $fullTarget);
                }
            }
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
        $basePrefix = \app\Helpers\Url::base();
        if (!isset($data['basePrefix'])) {
            $data['basePrefix'] = $basePrefix;
        }
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
        $basePrefix = \app\Helpers\Url::base();
        // If url already starts with basePrefix, don't duplicate it
        $target = ($basePrefix !== '' && strpos($url, $basePrefix) === 0) ? $url : ($basePrefix . '/' . ltrim($url, '/'));
        header('Location: ' . $target);
        exit;
    }
}



