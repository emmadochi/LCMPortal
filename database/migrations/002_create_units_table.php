<?php
/**
 * Migration: Create units table
 */

function up_002_create_units_table() {
    $db = \App\Core\Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS units (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_status (status),
        INDEX idx_name (name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $db->query($sql);
}

function down_002_create_units_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS units");
}

