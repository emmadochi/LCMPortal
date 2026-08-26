<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;
use App\Models\Church;

class HeadPastorMiddleware {
    public function handle($next) {
        $session = Session::getInstance();
        $request = new Request();
        $churchModel = new Church();
        
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

        // Check if user is a head pastor
        if (!$session->isHeadPastor()) {
            $base = $request->basePath();
            header('Location: ' . $base . '/unauthorized');
            exit;
        }
        
        // Get the church ID from URL if present (for church-scoped routes)
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $churchIdFromUrl = null;
        
        // Patterns that represent church-scoped resources
        $patterns = [
            '#/churches/(\d+)#',
            '#/reports/(\d+)#',
            '#/finance/(\d+)#',
            '#/media/(\d+)#',
            '#/projects/(\d+)#',
            '#/notifications/(\d+)#'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $path, $matches)) {
                $churchIdFromUrl = (int)$matches[1];
                break;
            }
        }
        
        // If URL has church ID, verify it matches the head pastor's assigned church
        if ($churchIdFromUrl !== null) {
            $headPastorChurchId = $session->getHeadPastorChurchId();
            
            if ($churchIdFromUrl !== $headPastorChurchId) {
                error_log("HeadPastorMiddleware: Access denied - User {$userId} trying to access church {$churchIdFromUrl}, assigned to {$headPastorChurchId}");
                $base = $request->basePath();
                header('Location: ' . $base . '/unauthorized');
                exit;
            }
        }
        
        return $next();
    }
}