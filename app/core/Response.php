<?php
namespace App\Core;

class Response {
    public function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function redirect($url, $status = 302) {
        $request = new Request();
        $base = $request->basePath();
        $location = $base . (strpos($url, '/') === 0 ? $url : '/' . $url);
        http_response_code($status);
        header("Location: {$location}");
        exit;
    }

    public function status($code) {
        http_response_code($code);
        return $this;
    }

    public function header($name, $value) {
        header("{$name}: {$value}");
        return $this;
    }
}

