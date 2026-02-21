<?php
/**
 * Migration: Create project_units junction table (many-to-many for collaborations)
 */

function up_011_create_project_units_table() {
    $db = \App\Core\Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS project_units (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT NOT NULL,
        unit_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_project_unit (project_id, unit_id),
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE CASCADE,
        INDEX idx_project_id (project_id),
        INDEX idx_unit_id (unit_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $db->query($sql);
}

function down_011_create_project_units_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS project_units");
}

