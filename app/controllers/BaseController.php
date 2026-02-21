<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Utilities\Validator;

abstract class BaseController {
    protected $request;
    protected $response;
    protected $session;

    public function __construct() {
        $this->request = new Request();
        $this->response = new Response();
        $this->session = Session::getInstance();
    }

    protected function render($view, $data = []) {
        $layout = $this->getLayout();
        
        // Render view content first
        ob_start();
        $viewPath = __DIR__ . "/../views/{$view}.php";
        if (file_exists($viewPath)) {
            extract($data); // Extract data for view
            require_once $viewPath;
        } else {
            die("View not found: {$view}");
        }
        $viewContent = ob_get_clean();
        
        // Add content to data array
        $data['content'] = $viewContent;
        extract($data);
        
        // Render layout with content
        $layoutPath = __DIR__ . "/../views/layouts/{$layout}.php";
        if (file_exists($layoutPath)) {
            require_once $layoutPath;
        } else {
            echo $viewContent;
        }
    }

    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($url) {
        $base = $this->request->basePath();
        $location = $base . (strpos($url, '/') === 0 ? $url : '/' . $url);
        header("Location: {$location}");
        exit;
    }

    protected function validate($rules) {
        $validator = new Validator();
        return $validator->validate($this->request->all(), $rules);
    }

    protected function authorize($permission) {
        if (!$this->session->hasPermission($permission)) {
            $this->redirect('/unauthorized');
        }
    }

    protected function getLayout() {
        // Check if we're on auth pages
        $request = new \App\Core\Request();
        $uri = $request->uri();
        
        if (strpos($uri, '/login') !== false || strpos($uri, '/register') !== false || strpos($uri, '/forgot') !== false) {
            return 'auth';
        }
        
        // For authenticated pages, use admin layout
        if ($this->session->has('user_id')) {
            return 'admin';
        }
        
        // Default to auth for unauthenticated
        return 'auth';
    }
}

