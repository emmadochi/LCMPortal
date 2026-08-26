<?php
/**
 * Migration: Create pledges table
 */

function up_059_create_pledges_table() {
    $db = \App\Core\Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS pledges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        church_id INT NOT NULL,
        member_id INT NULL,
        donor_name VARCHAR(255) NULL,
        donor_email VARCHAR(255) NULL,
        donor_phone VARCHAR(50) NULL,
        campaign_name VARCHAR(255) NOT NULL,
        target_amount DECIMAL(12, 2) NOT NULL,
        amount_paid DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
        start_date DATE NOT NULL,
        due_date DATE NULL,
        frequency ENUM('one_time', 'weekly', 'monthly', 'quarterly', 'yearly') DEFAULT 'one_time',
        status ENUM('pending', 'in_progress', 'fulfilled', 'cancelled', 'overdue') DEFAULT 'pending',
        notes TEXT NULL,
        recorded_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (church_id) REFERENCES churches(id) ON DELETE CASCADE,
        FOREIGN KEY (member_id) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_pledge_church (church_id),
        INDEX idx_pledge_member (member_id),
        INDEX idx_pledge_status (status),
        INDEX idx_pledge_campaign (campaign_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $db->query($sql);
}

function down_059_create_pledges_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS pledges");
}
