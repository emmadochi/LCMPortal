<?php
return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
    'database' => getenv('DB_DATABASE') ?: 'church_reporting_portal',
    'charset' => 'utf8mb4'
];

