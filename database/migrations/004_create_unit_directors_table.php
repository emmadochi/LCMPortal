<?php
/**
 * Migration: Create unit_directors junction table (many-to-many)
 */

function up_004_create_unit_directors_table() {
    $db = \App\Core\Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS unit_directors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        unit_id INT NOT NULL,
        assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_director_unit (user_id, unit_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id),
        INDEX idx_unit_id (unit_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $db->query($sql);
}

function down_004_create_unit_directors_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS unit_directors");
}

