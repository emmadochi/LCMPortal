<?php
/**
 * Migration: Create outreach_publicity table (publicity channels per report)
 */

function up_048_create_outreach_publicity_table() {
    $db = \App\Core\Database::getInstance();

    $sql = "CREATE TABLE IF NOT EXISTS outreach_publicity (
        id INT AUTO_INCREMENT PRIMARY KEY,
        outreach_report_id INT NOT NULL,
        channel VARCHAR(100) NOT NULL,
        details TEXT NULL,
        estimated_reach INT NULL,
        cost DECIMAL(10,2) NULL,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (outreach_report_id) REFERENCES outreach_reports(id) ON DELETE CASCADE,
        INDEX idx_outreach_report_id (outreach_report_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    return $db->query($sql);
}

function down_048_create_outreach_publicity_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS outreach_publicity");
}
