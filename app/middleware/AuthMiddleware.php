<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;

class AuthMiddleware {
    public function handle($next) {
        $session = Session::getInstance();
        
        if (!$session->has('user_id')) {
            $request = new Request();
            $base = $request->basePath();
            header('Location: ' . $base . '/login');
            exit;
        }
        
        return $next();
    }
}

