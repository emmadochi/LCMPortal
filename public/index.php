<?php
// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
        putenv(trim($name) . '=' . trim($value));
    }
}

// Autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Set timezone
$config = require __DIR__ . '/../config/config.php';
date_default_timezone_set($config['timezone']);

// Start application
use App\Core\App;

$app = new App();
$app->run();

