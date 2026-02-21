<?php
/**
 * Migration: Create outreach_challenges table (challenges encountered per report)
 */

function up_051_create_outreach_challenges_table() {
    $db = \App\Core\Database::getInstance();

    $sql = "CREATE TABLE IF NOT EXISTS outreach_challenges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        outreach_report_id INT NOT NULL,
        description TEXT NOT NULL,
        category VARCHAR(100) NULL,
        severity ENUM('low', 'medium', 'high') NULL,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (outreach_report_id) REFERENCES outreach_reports(id) ON DELETE CASCADE,
        INDEX idx_outreach_report_id (outreach_report_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    return $db->query($sql);
}

function down_051_create_outreach_challenges_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS outreach_challenges");
}
