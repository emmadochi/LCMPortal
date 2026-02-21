<?php
/**
 * Migration: Create church_unit_targets table
 *
 * Stores targets for churches (church-wide) or for church-specific units.
 * unit_id NULL = church-wide target; unit_id set = target for that unit in that church.
 */

function up_053_create_church_unit_targets_table() {
    $db = \App\Core\Database::getInstance();

    $sql = "CREATE TABLE IF NOT EXISTS church_unit_targets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        church_id INT NOT NULL,
        unit_id INT NULL,
        target_type VARCHAR(80) NOT NULL,
        target_value DECIMAL(14,2) NOT NULL,
        period_type VARCHAR(20) NOT NULL,
        period_value VARCHAR(30) NOT NULL,
        unit_label VARCHAR(50) NULL,
        notes TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (church_id) REFERENCES churches(id) ON DELETE CASCADE,
        FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE CASCADE,
        INDEX idx_church_id (church_id),
        INDEX idx_unit_id (unit_id),
        INDEX idx_period (period_type, period_value),
        INDEX idx_church_period (church_id, period_type, period_value)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    return $db->query($sql);
}

function down_053_create_church_unit_targets_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS church_unit_targets");
}
