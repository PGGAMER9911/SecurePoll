<?php
namespace app\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $pdo;

    private function connect() {
        $config = require __DIR__ . '/../../config/database.php';
        $dsn = "mysql:host=" . $config['host'] . ";dbname=" . $config['database'] . ";charset=" . $config['charset'];
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    private function __construct() {
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        try {
            $this->pdo->query("SELECT 1");
        } catch (PDOException $e) {
            if ($e->getCode() == 'HY000' && (strpos($e->getMessage(), '2006') !== false || strpos($e->getMessage(), '2013') !== false)) {
                $this->connect();
            }
        }
        return $this->pdo;
    }
}

