<?php
/**
 * Migration: Create events table
 */

function up_023_create_events_table() {
    $db = \App\Core\Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        event_type VARCHAR(100) NOT NULL,
        start_date DATETIME NOT NULL,
        end_date DATETIME NOT NULL,
        location VARCHAR(255),
        organizer_id INT,
        status ENUM('draft', 'published', 'cancelled', 'completed') DEFAULT 'draft',
        capacity INT NULL,
        registration_required BOOLEAN DEFAULT FALSE,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_event_type (event_type),
        INDEX idx_status (status),
        INDEX idx_start_date (start_date),
        INDEX idx_created_by (created_by)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $db->query($sql);
}

function down_023_create_events_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS events");
}