<?php
return [
    'app_name' => 'Church Reporting Portal',
    'app_url' => getenv('APP_URL') ?: 'http://localhost/ADMIN_PORTAL',
    'timezone' => getenv('TIMEZONE') ?: 'UTC',
    'debug' => getenv('DEBUG') === 'true',
    
    'upload' => [
        'max_size' => 5242880, // 5MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'mp3', 'mp4'],
        'path' => __DIR__ . '/../uploads'
    ],
    
    'session' => [
        'lifetime' => 7200, // 2 hours
        'name' => 'church_portal_session'
    ],

    'mail' => [
        'from_email' => getenv('MAIL_FROM_EMAIL') ?: 'noreply@churchportal.local',
        'from_name'  => getenv('MAIL_FROM_NAME') ?: 'Church Portal',
    ]
];

