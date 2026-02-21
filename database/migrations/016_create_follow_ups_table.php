<?php
/**
 * Migration: Create follow_ups table
 */

function up_016_create_follow_ups_table() {
    $db = \App\Core\Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS follow_ups (
        id INT AUTO_INCREMENT PRIMARY KEY,
        member_id INT NOT NULL,
        type ENUM('new_convert', 'prayer_request', 'counseling', 'visitation', 'general') DEFAULT 'general',
        status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
        due_date DATE,
        completed_date DATETIME NULL,
        assigned_to INT NULL,
        notes TEXT,
        priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (member_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
        INDEX idx_member_id (member_id),
        INDEX idx_assigned_to (assigned_to),
        INDEX idx_status (status),
        INDEX idx_due_date (due_date),
        INDEX idx_priority (priority)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $db->query($sql);
}

function down_016_create_follow_ups_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS follow_ups");
}
