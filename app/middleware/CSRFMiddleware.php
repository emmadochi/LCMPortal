<?php
namespace App\Middleware;

use App\Core\Request;
use App\Utilities\Security;

class CSRFMiddleware {
    public function handle($next) {
        $request = new Request();
        
        // Skip CSRF for GET requests
        if ($request->isGet()) {
            return $next();
        }
        
        $token = $request->post('_token') ?? $request->input('_token');
        
        if (!$token || !Security::validateCSRFToken($token)) {
            http_response_code(403);
            die('CSRF token validation failed');
        }
        
        return $next();
    }
}

