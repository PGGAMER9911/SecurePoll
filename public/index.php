<?php
// Set application timezone to match MySQL and expected user timezone
date_default_timezone_set('Asia/Kolkata');

// Autoloader function for classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Load Composer autoloader for PHPMailer and other dependencies
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Load Environment Variables
\app\Helpers\Env::load(__DIR__ . '/../.env');

// Initialize Secure Session
\app\Helpers\Session::init();

// Load routes
require_once __DIR__ . '/../routes/web.php';
