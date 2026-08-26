<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;

/**
 * Middleware for pastor-director role combinations
 * Handles access control for pastors who are also unit directors
 */
class PastorDirectorMiddleware {
    public function handle($next) {
        $session = Session::getInstance();
        $request = new Request();
        
        // Get user from session
        $userId = $session->get('user_id');
        $userRole = $session->get('user_role');
        
        if (!$userId) {
            $base = $request->basePath();
            header('Location: ' . $base . '/login');
            exit;
        }

        // Admin has access to everything
        if ($userRole === 'admin') {
            return $next();
        }

        // Check if user is a pastor (head pastor or regular pastor)
        $isPastor = $session->get('is_pastor', false);
        if (!$isPastor) {
            $base = $request->basePath();
            header('Location: ' . $base . '/unauthorized');
            exit;
        }
        
        // Check if user is also a director (for pastor-director combinations)
        $isDirector = $session->get('is_director', false);
        if (!$isDirector) {
            // User is a pastor but not a director - they can access pastor content but not director content
            // This middleware is specifically for pastor-director combinations
            $base = $request->basePath();
            header('Location: ' . $base . '/unauthorized');
            exit;
        }
        
        // User is both a pastor and a director - allow access
        return $next();
    }
}