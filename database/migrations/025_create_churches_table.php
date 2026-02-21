<?php
/**
 * Migration: Create churches table
 * 
 * This table stores information about different church locations/branches
 * that can be managed within the system.
 */

function up_025_create_churches_table() {
    $db = \App\Core\Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS churches (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        address TEXT,
        city VARCHAR(100),
        state VARCHAR(100),
        postal_code VARCHAR(20),
        country VARCHAR(100) DEFAULT 'USA',
        phone VARCHAR(20),
        email VARCHAR(255),
        website VARCHAR(255),
        established_date DATE,
        denomination VARCHAR(100),
        pastor_name VARCHAR(255),
        status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
        is_headquarters BOOLEAN DEFAULT FALSE,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_status (status),
        INDEX idx_city (city),
        INDEX idx_state (state),
        INDEX idx_denomination (denomination),
        INDEX idx_created_by (created_by)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $db->query($sql);
}

function down_025_create_churches_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS churches");
}