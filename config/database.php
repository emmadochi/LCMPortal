<?php
$host     = $_ENV['DB_HOST']     ?? getenv('DB_HOST')     ?: 'localhost';
$username = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: 'root';
$password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '';
$database = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: 'church_reporting_portal';
$charset  = $_ENV['DB_CHARSET']  ?? getenv('DB_CHARSET')  ?: 'utf8mb4';

return [
    'host'     => $host,
    'username' => $username,
    'password' => $password,
    'database' => $database,
    'charset'  => $charset
];
