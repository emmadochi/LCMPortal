<?php
namespace App\Core;

class App {
    private $router;

    public function __construct() {
        $this->router = new Router();
    }

    public function getRouter() {
        return $this->router;
    }

    public function run() {
        // Load routes - pass router instance
        $router = $this->router;
        require_once __DIR__ . '/../../routes/web.php';
        
        // Dispatch request
        $this->router->dispatch();
    }
}

