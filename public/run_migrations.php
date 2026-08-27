<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');
echo "<!DOCTYPE html><html><head><title>Database Migration & Setup</title>";
echo "<style>body{font-family:system-ui,-apple-system,sans-serif;max-width:800px;margin:40px auto;padding:20px;line-height:1.6;background:#f8fafc;color:#1e293b;} .card{background:#fff;padding:24px;border-radius:12px;box-shadow:0 4px 6px -1px rgb(0 0 0 / 0.1);} .success{color:#16a34a;font-weight:600;} .error{color:#dc2626;font-weight:600;} .btn{display:inline-block;background:#4f46e5;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:600;margin-top:16px;}</style></head><body><div class='card'>";

echo "<h1>🚀 Portal Database Setup & Migrations</h1>";

// 1. Load environment
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

// 2. Load Autoloader
$autoloadFiles = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    dirname(__DIR__) . '/vendor/autoload.php'
];
foreach ($autoloadFiles as $autoload) {
    if (file_exists($autoload)) {
        require_once $autoload;
        break;
    }
}

$db = \App\Core\Database::getInstance();
$db->query("SET FOREIGN_KEY_CHECKS = 0;");

// 3. Run all migrations
$migrationDirs = [
    __DIR__ . '/database/migrations',
    __DIR__ . '/../database/migrations',
    dirname(__DIR__) . '/database/migrations'
];
$migrationDir = null;
foreach ($migrationDirs as $d) {
    if (is_dir($d)) {
        $migrationDir = $d;
        break;
    }
}

if (!$migrationDir) {
    die("<p class='error'>Migrations directory not found.</p></div></body></html>");
}

$migrations = glob($migrationDir . '/*.php');
sort($migrations);

echo "<h3>Running Migrations (" . count($migrations) . " files found):</h3><ul>";

$passedCount = 0;
$skippedCount = 0;

foreach ($migrations as $file) {
    $baseName = basename($file, '.php');
    require_once $file;
    $functionName = 'up_' . $baseName;
    if (function_exists($functionName)) {
        try {
            $res = $functionName();
            echo "<li><span class='success'>✓</span> " . htmlspecialchars($baseName) . "</li>";
            $passedCount++;
        } catch (\Throwable $e) {
            echo "<li><span class='error'>✗</span> " . htmlspecialchars($baseName) . " - " . htmlspecialchars($e->getMessage()) . "</li>";
        }
    }
}
echo "</ul>";

$db->query("SET FOREIGN_KEY_CHECKS = 1;");

// 4. Seed Default Admin User if not exists
echo "<h3>Default Admin Account:</h3>";
try {
    $userModel = new \App\Models\User();
    $admin = $userModel->findByEmail('admin@church.com');
    if ($admin) {
        echo "<p class='success'>✓ Admin user already exists (admin@church.com)</p>";
    } else {
        $adminId = $userModel->create([
            'email' => 'admin@church.com',
            'password' => \App\Utilities\Security::hashPassword('admin123'),
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'role' => 'admin',
            'status' => 'active'
        ]);
        if ($adminId) {
            echo "<p class='success'>✓ Created Super Admin user successfully!</p>";
            echo "<p><strong>Email:</strong> <code>admin@church.com</code> | <strong>Password:</strong> <code>admin123</code></p>";
        } else {
            echo "<p class='error'>Failed to seed admin user: " . htmlspecialchars($db->error) . "</p>";
        }
    }
} catch (\Throwable $e) {
    echo "<p class='error'>Error seeding admin: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr><a href='login' class='btn'>Go to Login Page →</a>";
echo "</div></body></html>";
