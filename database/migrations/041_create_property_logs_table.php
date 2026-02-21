<?php
/**
 * Migration: Create property_logs table for tracking all property changes and status updates.
 */

function up_041_create_property_logs_table() {
    $db = \App\Core\Database::getInstance();

    $sql = "CREATE TABLE IF NOT EXISTS property_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        property_id INT NOT NULL,
        user_id INT NULL,
        action VARCHAR(50) NOT NULL,
        old_status VARCHAR(50) NULL,
        new_status VARCHAR(50) NULL,
        notes TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_property (property_id),
        INDEX idx_user (user_id),
        INDEX idx_action (action),
        INDEX idx_created_at (created_at),
        FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    return $db->query($sql);
}

function down_041_create_property_logs_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS property_logs");
}
