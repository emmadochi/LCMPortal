<?php
/**
 * Seed: Create admin user
 * Usage: php database/seeds/create_admin_user.php
 */

require_once __DIR__ . '/../../vendor/autoload.php';

// Load environment
if (file_exists(__DIR__ . '/../../.env')) {
    $lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
        putenv(trim($name) . '=' . trim($value));
    }
}

use App\Models\User;
use App\Utilities\Security;

echo "Creating Admin User\n";
echo "===================\n\n";

$userModel = new User();

// Check if admin already exists
$admin = $userModel->findByEmail('admin@church.com');
if ($admin) {
    echo "Admin user already exists!\n";
    echo "Email: admin@church.com\n";
    exit;
}

// Create admin user
$adminData = [
    'email' => 'admin@church.com',
    'password' => Security::hashPassword('admin123'), // Change this password!
    'first_name' => 'Admin',
    'last_name' => 'User',
    'role' => 'admin',
    'status' => 'active'
];

$userId = $userModel->create($adminData);

if ($userId) {
    echo "✓ Admin user created successfully!\n\n";
    echo "Login Credentials:\n";
    echo "Email: admin@church.com\n";
    echo "Password: admin123\n\n";
    echo "⚠️  IMPORTANT: Change the password after first login!\n";
} else {
    echo "✗ Failed to create admin user\n";
    $db = \App\Core\Database::getInstance();
    echo "Error: " . $db->error . "\n";
}

