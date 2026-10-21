<?php
namespace app\Core;

class Router {
    private static $routes = [];

    public static function get($route, $action) {
        self::$routes['GET'][$route] = $action;
    }

    public static function post($route, $action) {
        self::$routes['POST'][$route] = $action;
    }

    public static function dispatch($url) {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'HEAD') {
            $method = 'GET';
        }
        $url = rtrim($url, '/');
        if (empty($url)) {
            $url = '/';
        }

        if (isset(self::$routes[$method]) && array_key_exists($url, self::$routes[$method])) {
            $action = self::$routes[$method][$url];
            if (is_array($action)) {
                $controller = new $action[0]();
                $methodName = $action[1];
                $controller->$methodName();
            } else if (is_callable($action)) {
                call_user_func($action);
            }
        } else {
            http_response_code(404);
            echo "404 Not Found";
        }
    }
}
