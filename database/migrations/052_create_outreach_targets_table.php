<?php
/**
 * Migration: Create outreach_targets table (target vs actual metrics per report)
 */

function up_052_create_outreach_targets_table() {
    $db = \App\Core\Database::getInstance();

    $sql = "CREATE TABLE IF NOT EXISTS outreach_targets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        outreach_report_id INT NOT NULL,
        target_name VARCHAR(150) NOT NULL,
        target_value DECIMAL(14,2) NOT NULL,
        actual_value DECIMAL(14,2) NULL,
        unit VARCHAR(50) NULL,
        notes TEXT NULL,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (outreach_report_id) REFERENCES outreach_reports(id) ON DELETE CASCADE,
        INDEX idx_outreach_report_id (outreach_report_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    return $db->query($sql);
}

function down_052_create_outreach_targets_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS outreach_targets");
}
