<?php
/**
 * Migration: Create unit_user junction table (many-to-many)
 */

function up_003_create_unit_user_table() {
    $db = \App\Core\Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS unit_user (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        unit_id INT NOT NULL,
        role VARCHAR(50) DEFAULT 'member',
        joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_unit (user_id, unit_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id),
        INDEX idx_unit_id (unit_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $db->query($sql);
}

function down_003_create_unit_user_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS unit_user");
}

