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
        // Boot session before anything else so $_SESSION is available
        // for CSRF token generation and validation on every request.
        Session::getInstance();

        // Load routes - pass router instance
        $router = $this->router;
        require_once __DIR__ . '/../../routes/web.php';
        
        // Dispatch request
        $this->router->dispatch();
    }
}

