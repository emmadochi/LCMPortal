<?php
/**
 * Migration: Create attendance table
 */

function up_007_create_attendance_table() {
    $db = \App\Core\Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        unit_id INT NOT NULL,
        user_id INT NOT NULL,
        event_date DATE NOT NULL,
        event_type VARCHAR(50) NOT NULL,
        notes TEXT,
        status VARCHAR(20) DEFAULT 'present',
        recorded_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_unit_id (unit_id),
        INDEX idx_user_id (user_id),
        INDEX idx_event_date (event_date),
        INDEX idx_event_type (event_type),
        INDEX idx_recorded_by (recorded_by)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $db->query($sql);
}

function down_007_create_attendance_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS attendance");
}

