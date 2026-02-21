<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;

class RoleMiddleware {
    private $allowedRoles;

    public function __construct(...$roles) {
        $this->allowedRoles = $roles;
    }

    public function handle($next) {
        $session = Session::getInstance();
        $userRole = $session->get('user_role');
        
        if (!in_array($userRole, $this->allowedRoles)) {
            $request = new Request();
            $base = $request->basePath();
            header('Location: ' . $base . '/unauthorized');
            exit;
        }
        
        return $next();
    }
}

