<?php
namespace App\Core;

use mysqli;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $config = require __DIR__ . '/../../config/database.php';
        
        $this->connection = new mysqli(
            $config['host'],
            $config['username'],
            $config['password'],
            $config['database']
        );

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }

        $this->connection->set_charset("utf8mb4");
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

