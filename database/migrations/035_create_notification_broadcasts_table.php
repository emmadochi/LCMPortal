<?php
/**
 * Migration: Create notification_broadcasts table for admin-sent notification history and audit.
 */

function up_035_create_notification_broadcasts_table() {
    $db = \App\Core\Database::getInstance();

    $sql = "CREATE TABLE IF NOT EXISTS notification_broadcasts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sent_by_user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        link VARCHAR(500) NULL,
        audience_type VARCHAR(20) NOT NULL DEFAULT 'all',
        audience_roles VARCHAR(255) NULL,
        channels VARCHAR(20) NOT NULL DEFAULT 'both',
        recipient_count INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_sent_by (sent_by_user_id),
        INDEX idx_created_at (created_at),
        FOREIGN KEY (sent_by_user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    return $db->query($sql);
}

function down_035_create_notification_broadcasts_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS notification_broadcasts");
}
