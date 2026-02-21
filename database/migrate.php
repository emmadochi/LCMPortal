<?php
/**
 * Database Migration Runner
 * Usage: php database/migrate.php [up|down|fresh]
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
        putenv(trim($name) . '=' . trim($value));
    }
}

use App\Core\Database;

// Get migration files
$migrationDir = __DIR__ . '/migrations';
$migrations = glob($migrationDir . '/*.php');
sort($migrations);

$action = $argv[1] ?? 'up';

echo "Database Migration Tool\n";
echo "======================\n\n";

if ($action === 'fresh') {
    echo "Dropping all tables...\n";
    // Run all down migrations in reverse
    for ($i = count($migrations) - 1; $i >= 0; $i--) {
        $file = $migrations[$i];
        require_once $file;
        $functionName = 'down_' . basename($file, '.php');
        if (function_exists($functionName)) {
            if ($functionName()) {
                echo "✓ " . basename($file) . " (down)\n";
            } else {
                echo "✗ " . basename($file) . " (down) - Error\n";
            }
        }
    }
    echo "\n";
    $action = 'up';
}

if ($action === 'up') {
    echo "Running migrations...\n\n";
    foreach ($migrations as $file) {
        require_once $file;
        $functionName = 'up_' . basename($file, '.php');
        if (function_exists($functionName)) {
            try {
                if ($functionName()) {
                    echo "✓ " . basename($file) . "\n";
                } else {
                    echo "✗ " . basename($file) . " - Error: " . Database::getInstance()->error . "\n";
                }
            } catch (Exception $e) {
                echo "✗ " . basename($file) . " - Exception: " . $e->getMessage() . "\n";
            }
        }
    }
    echo "\n✓ All migrations completed!\n";
} elseif ($action === 'down') {
    echo "Rolling back migrations...\n\n";
    // Run down migrations in reverse order
    for ($i = count($migrations) - 1; $i >= 0; $i--) {
        $file = $migrations[$i];
        require_once $file;
        $functionName = 'down_' . basename($file, '.php');
        if (function_exists($functionName)) {
            if ($functionName()) {
                echo "✓ " . basename($file) . " (rolled back)\n";
            } else {
                echo "✗ " . basename($file) . " - Error\n";
            }
        }
    }
    echo "\n✓ Rollback completed!\n";
} else {
    echo "Usage: php database/migrate.php [up|down|fresh]\n";
    echo "  up    - Run all migrations\n";
    echo "  down  - Rollback all migrations\n";
    echo "  fresh - Drop all tables and run migrations\n";
}

