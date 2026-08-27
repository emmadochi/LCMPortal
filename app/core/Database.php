<?php
namespace App\Core;

use mysqli;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        
        // Turn off automatic exceptions to catch connect errors cleanly
        mysqli_report(MYSQLI_REPORT_OFF);

        $this->connection = @new mysqli(
            $config['host'],
            $config['username'],
            $config['password'],
            $config['database']
        );

        if ($this->connection->connect_error) {
            die("Database Connection Error: " . htmlspecialchars($this->connection->connect_error) . " (Host: {$config['host']}, DB: {$config['database']}, User: {$config['username']})");
        }

        $this->connection->set_charset($config['charset'] ?? "utf8mb4");
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->connection;
    }

    public function __clone() {
        throw new \Exception("Cannot clone singleton");
    }

    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
