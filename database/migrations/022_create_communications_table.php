<?php
/**
 * Migration: Create communications table
 */

function up_022_create_communications_table() {
    $db = \App\Core\Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS communications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        delivery_method ENUM('email', 'sms', 'in_app', 'all') NOT NULL,
        membership_types JSON,
        include_unengaged BOOLEAN DEFAULT FALSE,
        send_immediately BOOLEAN DEFAULT TRUE,
        scheduled_time DATETIME NULL,
        sent_by INT NOT NULL,
        sent_at TIMESTAMP NULL,
        recipient_count INT DEFAULT 0,
        delivered_count INT DEFAULT 0,
        failed_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sent_by) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_delivery_method (delivery_method),
        INDEX idx_sent_at (sent_at),
        INDEX idx_scheduled_time (scheduled_time)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $db->query($sql);
}

function down_022_create_communications_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS communications");
}