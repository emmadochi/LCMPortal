<?php
namespace App\Core;

class Router {
    private $routes = [];
    private $middleware = [];
    private $groupMiddleware = [];
    private $groupPrefix = '';

    public function get($uri, $handler, $middleware = []) {
        return $this->addRoute('GET', $uri, $handler, $middleware);
    }

    public function post($uri, $handler, $middleware = []) {
        return $this->addRoute('POST', $uri, $handler, $middleware);
    }

    public function put($uri, $handler, $middleware = []) {
        return $this->addRoute('PUT', $uri, $handler, $middleware);
    }

    public function delete($uri, $handler, $middleware = []) {
        return $this->addRoute('DELETE', $uri, $handler, $middleware);
    }

    private function addRoute($method, $uri, $handler, $middleware = []) {
        $uri = $this->groupPrefix . $uri;
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'handler' => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware)
        ];
        return $this;
    }

    public function group($prefix, $callback, $middleware = []) {
        $previousPrefix = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;
        
        $this->groupPrefix = $previousPrefix . $prefix;
        $this->groupMiddleware = array_merge($previousMiddleware, $middleware);
        
        $callback($this);
        
        $this->groupPrefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    public function dispatch() {
        $request = new Request();
        $method = $request->method();
        $uri = $request->uri();

        // Handle PUT/DELETE from POST with _method
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchRoute($route['uri'], $uri)) {
                // Execute middleware
                foreach ($route['middleware'] as $middleware) {
                    if (is_string($middleware)) {
                        $middleware = new $middleware();
                    }
                    if (method_exists($middleware, 'handle')) {
                        $middleware->handle(function() {});
                    }
                }

                // Execute handler
                return $this->executeHandler($route['handler'], $this->extractParams($route['uri'], $uri));
            }
        }

        // 404 Not Found
        http_response_code(404);
        require_once __DIR__ . '/../views/errors/404.php';
        exit;
    }

    private function matchRoute($routeUri, $requestUri) {
        $routePattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $routeUri);
        $routePattern = '#^' . $routePattern . '$#';
        return preg_match($routePattern, $requestUri);
    }

    private function extractParams($routeUri, $requestUri) {
        $params = [];
        $routeParts = explode('/', trim($routeUri, '/'));
        $requestParts = explode('/', trim($requestUri, '/'));

        foreach ($routeParts as $index => $part) {
            if (preg_match('/\{([a-zA-Z0-9_]+)\}/', $part, $matches)) {
                $params[$matches[1]] = $requestParts[$index] ?? null;
            }
        }

        return $params;
    }

    private function executeHandler($handler, $params = []) {
        // Pass as positional arguments to avoid PHP 8+ "Unknown named parameter" with call_user_func_array
        $args = array_values($params);

        if (is_string($handler) && strpos($handler, '@') !== false) {
            [$controller, $method] = explode('@', $handler);
            $controllerClass = "App\\Controllers\\{$controller}";
            
            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();
                if (method_exists($controllerInstance, $method)) {
                    return call_user_func_array([$controllerInstance, $method], $args);
                }
            }
        } elseif (is_callable($handler)) {
            return call_user_func_array($handler, $args);
        }

        throw new \Exception("Handler not found or invalid");
    }
}

