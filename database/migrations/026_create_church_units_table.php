<?php
/**
 * Migration: Create church_units table
 * 
 * This table establishes the many-to-many relationship between churches and units,
 * allowing units to be associated with specific church locations.
 */

function up_026_create_church_units_table() {
    $db = \App\Core\Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS church_units (
        id INT AUTO_INCREMENT PRIMARY KEY,
        church_id INT NOT NULL,
        unit_id INT NOT NULL,
        assigned_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        assigned_by INT NOT NULL,
        is_primary BOOLEAN DEFAULT FALSE,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (church_id) REFERENCES churches(id) ON DELETE CASCADE,
        FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE CASCADE,
        FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_church_unit (church_id, unit_id),
        INDEX idx_church_id (church_id),
        INDEX idx_unit_id (unit_id),
        INDEX idx_assigned_by (assigned_by)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $db->query($sql);
}

function down_026_create_church_units_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS church_units");
}