<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;

/**
 * Middleware for all pastor types (head pastors and regular pastors)
 * Handles access control based on pastor role and additional permissions
 */
class PastorMiddleware {
    private $requiredPermission;
    
    public function __construct($requiredPermission = null) {
        $this->requiredPermission = $requiredPermission;
    }
    
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

        // Check if user is any type of pastor
        $isPastor = $session->get('is_pastor', false);
        if (!$isPastor) {
            $base = $request->basePath();
            header('Location: ' . $base . '/unauthorized');
            exit;
        }
        
        // If specific permission is required, check it
        if ($this->requiredPermission && !$session->hasPermission($this->requiredPermission)) {
            $base = $request->basePath();
            header('Location: ' . $base . '/unauthorized');
            exit;
        }
        
        // User is a pastor and has required permission - allow access
        return $next();
    }
}