<?php
// Autoloader function for classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Load Environment Variables
\app\Helpers\Env::load(__DIR__ . '/.env');

// Load Composer Autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} else {
    echo "Warning: vendor/autoload.php not found. PHPMailer may fail.\n";
}

// Init Session
session_start();

use app\Services\AuthService;
use app\Models\User;

echo "Starting Registration Test...\n";

$authService = new AuthService();

$data = [
    'name' => 'John Doe Tester',
    'email' => 'test' . time() . '@example.com', // Change this to the user's email if we want to receive it, but let's just use a fake one and see if PHPMailer tries to send
    'password' => 'SecurePass123'
];

$deviceData = [
    'browser' => 'CLI',
    'os' => 'Windows',
    'user_agent' => 'PHP CLI',
    'screen_resolution' => 'N/A',
    'language' => 'en',
    'timezone' => 'UTC',
    'ip_address' => '127.0.0.1'
];

// Instead of a fake email, I'll use the SMTP_USERNAME so the user actually gets the email to prove it works!
$data['email'] = \app\Helpers\Env::get('SMTP_USERNAME');

echo "Attempting to register and send OTP to: " . $data['email'] . "\n";

$result = $authService->register($data, $deviceData);

if ($result['success']) {
    echo "SUCCESS: " . $result['message'] . "\n";
    echo "User ID: " . $result['user_id'] . "\n";
} else {
    echo "FAILED: " . $result['message'] . "\n";
}

echo "\nDone.\n";
