<?php
namespace app\Middleware;

class RateLimiter {
    public static function check($key, $limit, $timeWindow) {
        $dir = __DIR__ . '/../../storage/cache';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        
        $hash = md5($key);
        $file = $dir . '/ratelimit_' . $hash . '.json';
        $now = time();

        $fp = @fopen($file, 'c+');
        if (!$fp) {
            return true; // Fail open if unable to open file
        }

        flock($fp, LOCK_EX);

        $size = @filesize($file);
        $content = ($size > 0) ? @fread($fp, $size) : '';
        $requests = $content ? json_decode($content, true) : [];
        if (!is_array($requests)) {
            $requests = [];
        }

        // Filter out expired requests
        $requests = array_filter($requests, function($timestamp) use ($now, $timeWindow) {
            return ($timestamp > ($now - $timeWindow));
        });

        if (count($requests) >= $limit) {
            flock($fp, LOCK_UN);
            fclose($fp);
            return false; // Rate limit exceeded
        }

        $requests[] = $now;
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode(array_values($requests)));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        return true;
    }

    public static function enforce($action, $limit = 5, $timeWindow = 300) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $key = $action . '_' . $ip;
        
        if (!self::check($key, $limit, $timeWindow)) {
            http_response_code(429);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Too many requests. Please try again later.']);
            exit;
        }
    }
}
