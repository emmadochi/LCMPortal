<?php
namespace App\Core;

class Request {
    public function method() {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Base path when app is in a subdirectory (e.g. /ADMIN_PORTAL/public).
     * Use this when building redirect URLs and links so they work under any path.
     */
    public function basePath() {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $path = str_replace('\\', '/', dirname($scriptName));
        return ($path === '/' || $path === '.') ? '' : $path;
    }

    public function uri() {
        // Raw URI from server
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = parse_url($uri, PHP_URL_PATH);

        $basePath = $this->basePath();

        if ($basePath !== '' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        // Strip /index.php from beginning if present (e.g. /index.php or /index.php/dashboard)
        if (strpos($uri, '/index.php') === 0) {
            $uri = substr($uri, 10);
        }

        if ($uri === '' || $uri === false) {
            $uri = '/';
        }

        return $uri !== '/' ? rtrim($uri, '/') : '/';
    }


    public function isGet() {
        return $this->method() === 'GET';
    }

    public function isPost() {
        return $this->method() === 'POST';
    }

    public function isPut() {
        return $this->method() === 'PUT';
    }

    public function isDelete() {
        return $this->method() === 'DELETE';
    }

    public function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    public function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    public function all() {
        return array_merge($_GET, $_POST);
    }

    public function input($key, $default = null) {
        return $this->all()[$key] ?? $default;
    }

    public function has($key) {
        return isset($_GET[$key]) || isset($_POST[$key]);
    }

    public function file($key) {
        return $_FILES[$key] ?? null;
    }

    public function ip() {
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }

    public function userAgent() {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
}

