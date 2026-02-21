<?php
/**
 * Migration: Add project_id to attendance table
 */

function up_017_add_project_id_to_attendance() {
    $db = \App\Core\Database::getInstance();
    
    // Add project_id column
    $sql = "ALTER TABLE attendance ADD COLUMN project_id INT NULL AFTER event_type,
            ADD FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
            ADD INDEX idx_project_id (project_id)";
    
    return $db->query($sql);
}

function down_017_add_project_id_to_attendance() {
    $db = \App\Core\Database::getInstance();
    
    // Remove project_id column
    $sql = "ALTER TABLE attendance DROP FOREIGN KEY attendance_ibfk_4,
            DROP INDEX idx_project_id,
            DROP COLUMN project_id";
    
    return $db->query($sql);
}