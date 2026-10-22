<?php
namespace app\Helpers;

class Url {
    /**
     * Get application base path prefix.
     * On production domain (securepoll.pgdev.in), root is always ''
     * In subfolder deployments (e.g. localhost/SecurePoll/public), returns '/SecurePoll/public'
     */
    public static function base(): string {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if (strpos($host, 'securepoll.pgdev.in') !== false) {
            return '';
        }

        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = str_replace('\\', '/', dirname($scriptName));
        if ($dir !== '/' && $dir !== '.' && !empty($dir)) {
            return rtrim($dir, '/');
        }

        if (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false) {
            return '/SecurePoll/public';
        }

        return '';
    }

    /**
     * Build an internal URL prefixed with base path.
     */
    public static function to(string $path = '/'): string {
        $base = self::base();
        $cleanPath = '/' . ltrim($path, '/');
        if ($cleanPath === '/' && !empty($base)) {
            return $base;
        }
        return $base . $cleanPath;
    }
}
