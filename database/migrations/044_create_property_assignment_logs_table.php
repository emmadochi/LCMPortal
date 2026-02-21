<?php
/**
 * Migration: Create property_assignment_logs table for tracking assignee changes.
 */

function up_044_create_property_assignment_logs_table() {
    $db = \App\Core\Database::getInstance();

    $sql = "CREATE TABLE IF NOT EXISTS property_assignment_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        property_id INT NOT NULL,
        from_user_id INT NULL,
        to_user_id INT NULL,
        assigned_by_user_id INT NULL,
        notes TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_property (property_id),
        INDEX idx_to_user (to_user_id),
        INDEX idx_assigned_by (assigned_by_user_id),
        FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
        FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY (assigned_by_user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    return $db->query($sql);
}

function down_044_create_property_assignment_logs_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS property_assignment_logs");
}

