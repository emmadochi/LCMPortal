<?php
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
use App\Core\App;

$app = new App();
$app->run();
