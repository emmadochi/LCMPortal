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
        // Force 'admin' layout for church-specific views if needed
        $layout = $this->getLayout();
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($uri, '/churches/') !== false && $layout === 'auth') {
            $layout = 'admin';
        }

        // Ensure churchId is available if it exists in the controller
        if (!isset($data['churchId']) && isset($this->churchId)) {
            $data['churchId'] = $this->churchId;
        }

        // Standardize base path to app directory
        $appPath = realpath(dirname(__DIR__));
        
        // Render view content first
        ob_start();
        $viewFound = false;
        foreach (['views', 'Views'] as $vDir) {
            $viewPath = $appPath . DIRECTORY_SEPARATOR . $vDir . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $view) . '.php';
            if (file_exists($viewPath)) {
                extract($data);
                include $viewPath;
                $viewFound = true;
                break;
            }
        }
        
        if (!$viewFound) {
            die("View not found: {$view}");
        }
        
        $viewContent = ob_get_clean();
        
        // Add content to data array
        $data['content'] = $viewContent;
        extract($data);
        
        // Render layout with content
        $layoutFound = false;
        foreach (['views', 'Views'] as $vDir) {
            $vDirPath = $appPath . DIRECTORY_SEPARATOR . $vDir;
            if (is_dir($vDirPath)) {
                $layoutPath = $vDirPath . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $layout . '.php';
                if (file_exists($layoutPath)) {
                    include $layoutPath;
                    $layoutFound = true;
                    break;
                }
            }
        }
        
        if (!$layoutFound) {
            // Output diagnostic comment and just the content
            echo "<!-- Layout '{$layout}' not found in any views/layouts directory -->\n";
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

