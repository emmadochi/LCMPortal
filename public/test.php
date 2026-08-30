<?php
// Diagnostic Test Script
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');
echo "<h2>Portal Diagnostic Check</h2>";

function checkStep($title, $callback) {
    echo "<p><strong>Checking " . htmlspecialchars($title) . "...</strong> ";
    try {
        $result = $callback();
        echo "<span style='color:green;font-weight:bold;'>[PASS]</span> " . htmlspecialchars((string)$result) . "</p>";
    } catch (\Throwable $e) {
        echo "<span style='color:red;font-weight:bold;'>[FAIL]</span> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre style='background:#fee;padding:8px;border:1px solid #fcc;'>" . htmlspecialchars($e->getFile() . " on line " . $e->getLine()) . "\n" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
}

// 1. PHP Version & Extensions
checkStep("PHP Version & Extensions", function() {
    $required = ['mysqli', 'session', 'json', 'mbstring'];
    $missing = [];
    foreach ($required as $ext) {
        if (!extension_loaded($ext)) {
            $missing[] = $ext;
        }
    }
    if (!empty($missing)) {
        throw new \Exception("Missing PHP extensions: " . implode(', ', $missing));
    }
    return "PHP " . PHP_VERSION . " (All required extensions loaded)";
});

// 2. Locate and Read .env
checkStep(".env Configuration", function() {
    $envPaths = [
        __DIR__ . '/.env',
        __DIR__ . '/../.env',
        dirname(__DIR__) . '/.env'
    ];
    $found = null;
    foreach ($envPaths as $p) {
        if (file_exists($p)) {
            $found = $p;
            break;
        }
    }
    if (!$found) {
        throw new \Exception("No .env file found in paths: " . implode(', ', $envPaths));
    }

    $lines = file($found, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($k, $v) = explode('=', $line, 2);
            $k = trim($k);
            $v = trim($v, " \t\n\r\0\x0B\"'");
            $_ENV[$k] = $v;
            putenv("$k=$v");
        }
    }
    return "Found at {$found} | DB_DATABASE=" . ($_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?? 'NOT SET');
});

// 3. Test Database Connection
checkStep("MySQL Database Connection", function() {
    $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost';
    $user = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: 'root';
    $pass = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '';
    $db   = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: '';

    $conn = @new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        throw new \Exception("Connection failed: " . $conn->connect_error . " (Host: $host, User: $user, DB: $db)");
    }
    $conn->close();
    return "Connected successfully to database '$db' on '$host'";
});

// 4. Test Autoloader
checkStep("Composer Autoloader", function() {
    $autoloadPaths = [
        __DIR__ . '/vendor/autoload.php',
        __DIR__ . '/../vendor/autoload.php',
        dirname(__DIR__) . '/vendor/autoload.php'
    ];
    $found = null;
    foreach ($autoloadPaths as $p) {
        if (file_exists($p)) {
            $found = $p;
            require_once $p;
            break;
        }
    }
    if (!$found) {
        throw new \Exception("vendor/autoload.php not found in: " . implode(', ', $autoloadPaths));
    }
    if (!class_exists('App\\Core\\App')) {
        throw new \Exception("App\\Core\\App class could not be autoloaded!");
    }
    return "Autoloader loaded from $found | App class found";
});

// 5. Test Session
checkStep("Session Initialization", function() {
    $session = \App\Core\Session::getInstance();
    $session->set('__diagnostic_test', time());
    return "Session active. Token: " . \App\Utilities\Security::generateCSRFToken();
});

// 6. Test App Boot & AuthController
checkStep("AuthController & View Rendering", function() {
    $controller = new \App\Controllers\AuthController();
    ob_start();
    $controller->showLogin();
    $output = ob_get_clean();
    if (strlen($output) < 100) {
        throw new \Exception("Rendered login output is too short (" . strlen($output) . " bytes)");
    }
    return "Login view rendered successfully (" . strlen($output) . " bytes generated)";
});

echo "<hr><p style='color:blue;'><strong>If all steps passed above, your backend is 100% operational!</strong></p>";
