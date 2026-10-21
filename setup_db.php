<?php
require_once __DIR__ . '/app/Helpers/Env.php';
\app\Helpers\Env::load(__DIR__ . '/.env');

$host = \app\Helpers\Env::get('DB_HOST', '127.0.0.1');
$username = \app\Helpers\Env::get('DB_USERNAME', 'root');
$password = \app\Helpers\Env::get('DB_PASSWORD', '');
$database = \app\Helpers\Env::get('DB_DATABASE', 'secure_poll');

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to MySQL. Recreating database $database...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `$database`");
    $pdo->exec("CREATE DATABASE `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$database`");
    
    echo "Importing schema...\n";
    $sql = file_get_contents(__DIR__ . '/docs/schema.sql');
    
    // Split by semicolons for basic execution, or execute directly
    $pdo->exec($sql);
    
    echo "Schema imported successfully!\n";
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage() . "\n");
}
