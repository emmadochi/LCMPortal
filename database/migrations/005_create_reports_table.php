<?php
/**
 * Migration: Create reports table
 */

function up_005_create_reports_table() {
    $db = \App\Core\Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS reports (
        id INT AUTO_INCREMENT PRIMARY KEY,
        unit_id INT NOT NULL,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        report_type ENUM('weekly', 'event', 'departmental', 'outreach', 'media', 'technical', 'other') DEFAULT 'weekly',
        status ENUM('draft', 'submitted', 'approved', 'rejected') DEFAULT 'draft',
        submitted_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_unit_id (unit_id),
        INDEX idx_user_id (user_id),
        INDEX idx_status (status),
        INDEX idx_report_type (report_type),
        INDEX idx_submitted_at (submitted_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $db->query($sql);
}

function down_005_create_reports_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS reports");
}

