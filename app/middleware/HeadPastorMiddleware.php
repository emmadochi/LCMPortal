<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Models\Church;
use App\Models\User;

class HeadPastorMiddleware {
    private $request;
    private $response;
    private $churchModel;
    private $userModel;

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
        $this->churchModel = new Church();
        $this->userModel = new User();
    }

    public function handle() {
        $user = $_SESSION['user'] ?? null;
        
        if (!$user) {
            $this->response->redirect('/login');
            return false;
        }

        // Check if user is admin (they can access everything)
        if ($user['role'] === 'admin') {
            return true;
        }

        // Check if user is a head pastor
        $isHeadPastor = $this->churchModel->isHeadPastorOfAnyChurch($user['id']);
        
        if ($isHeadPastor) {
            // Get the church the user is head pastor of
            $church = $this->churchModel->getChurchByHeadPastor($user['id']);
            
            // For routes that involve a specific church ID, verify the user is head pastor of that church
            $routeParams = $this->request->getRouteParams();
            
            // Check if this is a church-related route that requires head pastor access to the specific church
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            
            // Extract church ID from URL if present
            $churchId = null;
            if (preg_match('/\/churches\/(\d+)/', $path, $matches)) {
                $churchId = (int)$matches[1];
            }
            
            // If accessing a specific church, verify the user is head pastor of that church
            if ($churchId && $church) {
                if ($church['id'] != $churchId) {
                    // User is head pastor of a different church
                    $this->response->redirect('/unauthorized');
                    return false;
                }
            }
            
            return true;
        }
        
        // Check if user has other roles that grant access
        if ($user['role'] === 'director' || $user['role'] === 'member') {
            return true;
        }
        
        $this->response->redirect('/unauthorized');
        return false;
    }
}