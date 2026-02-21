<?php
/**
 * Migration: Create outreach_reports table (Event/Outreach program reporting)
 */

function up_047_create_outreach_reports_table() {
    $db = \App\Core\Database::getInstance();

    $sql = "CREATE TABLE IF NOT EXISTS outreach_reports (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NULL,
        church_id INT NULL,
        unit_id INT NULL,
        title VARCHAR(255) NOT NULL,
        program_date DATE NOT NULL,
        end_date DATE NULL,
        description TEXT NULL,
        status ENUM('draft', 'submitted', 'approved') DEFAULT 'draft',
        total_attendance INT NULL,
        first_timers_count INT NULL,
        budget_total DECIMAL(12,2) NULL,
        actual_total DECIMAL(12,2) NULL,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL,
        FOREIGN KEY (church_id) REFERENCES churches(id) ON DELETE SET NULL,
        FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE SET NULL,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_program_date (program_date),
        INDEX idx_status (status),
        INDEX idx_church_id (church_id),
        INDEX idx_unit_id (unit_id),
        INDEX idx_created_by (created_by)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    return $db->query($sql);
}

function down_047_create_outreach_reports_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS outreach_reports");
}
