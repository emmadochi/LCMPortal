<?php
/**
 * Migration: Create memberships table
 * Date: 2024
 */

function up_015_create_memberships_table() {
    $db = \App\Core\Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS memberships (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        unit_id INT NOT NULL,
        membership_type ENUM('visitor', 'member', 'elder', 'deacon', 'pastor') DEFAULT 'visitor',
        status ENUM('active', 'inactive', 'suspended', 'transferred') DEFAULT 'active',
        join_date DATE,
        baptism_date DATE,
        tithe_status ENUM('regular', 'irregular', 'non_tither') DEFAULT 'non_tither',
        engagement_score INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id),
        INDEX idx_unit_id (unit_id),
        INDEX idx_membership_type (membership_type),
        INDEX idx_status (status),
        INDEX idx_engagement_score (engagement_score)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $db->query($sql);
}

function down_015_create_memberships_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS memberships");
}