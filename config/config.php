<?php
$appUrl   = $_ENV['APP_URL']   ?? getenv('APP_URL')   ?: 'http://localhost/ADMIN_PORTAL';
$timezone = $_ENV['TIMEZONE']  ?? getenv('TIMEZONE')  ?: 'UTC';
$debug    = ($_ENV['APP_DEBUG'] ?? getenv('DEBUG') ?? 'false') === 'true';

return [
    'app_name' => 'Church Reporting Portal',
    'app_url'  => $appUrl,
    'timezone' => $timezone,
    'debug'    => $debug,
    
    'upload' => [
        'max_size'      => 5242880, // 5MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'mp3', 'mp4'],
        'path'          => __DIR__ . '/../uploads'
    ],
    
    'session' => [
        'lifetime' => 7200, // 2 hours
        'name'     => 'church_portal_session'
    ],

    'mail' => [
        'from_email' => $_ENV['MAIL_FROM_EMAIL'] ?? getenv('MAIL_FROM_EMAIL') ?: 'noreply@churchportal.local',
        'from_name'  => $_ENV['MAIL_FROM_NAME']  ?? getenv('MAIL_FROM_NAME')  ?: 'Church Portal',
    ]
];
