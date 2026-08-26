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
            
            // Store the original URL in session for redirect after login
            // Exclude login, register, and forgot password pages from redirect storage
            $currentUri = $_SERVER['REQUEST_URI'] ?? '/';
            $excludedPaths = ['/login', '/register', '/forgot-password', '/reset-password'];
            $shouldStoreRedirect = true;
            
            foreach ($excludedPaths as $path) {
                if (strpos($currentUri, $path) !== false) {
                    $shouldStoreRedirect = false;
                    break;
                }
            }
            
            if ($shouldStoreRedirect) {
                // Only store if not already set (prevent overwriting)
                if (!$session->has('redirect_after_login')) {
                    $session->set('redirect_after_login', $currentUri);
                }
            }
            
            header('Location: ' . $base . '/login');
            exit;
        }
        
        return $next();
    }
}

