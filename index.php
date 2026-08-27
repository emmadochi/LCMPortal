<?php
// Display errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Load environment variables
$envFiles = [
    __DIR__ . '/.env',
    __DIR__ . '/../.env',
    dirname(__DIR__) . '/.env'
];
foreach ($envFiles as $file) {
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
                putenv("$name=$value");
            }
        }
        break;
    }
}

// Autoloader
$autoloadFiles = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    dirname(__DIR__) . '/vendor/autoload.php'
];
$loaded = false;
foreach ($autoloadFiles as $autoload) {
    if (file_exists($autoload)) {
        require_once $autoload;
        $loaded = true;
        break;
    }
}
if (!$loaded) {
    die("Autoloader not found. Please ensure vendor directory is present.");
}

// Set timezone
$configFile = file_exists(__DIR__ . '/config/config.php') ? __DIR__ . '/config/config.php' : __DIR__ . '/../config/config.php';
$config = require $configFile;
date_default_timezone_set($config['timezone'] ?? 'UTC');

// Start application
try {
    $app = new \App\Core\App();
    $app->run();
} catch (\Throwable $e) {
    echo "<h1>Application Error</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
