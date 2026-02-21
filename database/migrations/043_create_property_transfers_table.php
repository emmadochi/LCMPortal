<?php
/**
 * Migration: Create property_transfers table for church/location moves.
 */

function up_043_create_property_transfers_table() {
    $db = \App\Core\Database::getInstance();

    $sql = "CREATE TABLE IF NOT EXISTS property_transfers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        property_id INT NOT NULL,
        from_church_id INT NULL,
        to_church_id INT NULL,
        from_location VARCHAR(255) NULL,
        to_location VARCHAR(255) NULL,
        user_id INT NULL,
        notes TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_property (property_id),
        INDEX idx_user (user_id),
        INDEX idx_created_at (created_at),
        FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
        FOREIGN KEY (from_church_id) REFERENCES churches(id) ON DELETE SET NULL,
        FOREIGN KEY (to_church_id) REFERENCES churches(id) ON DELETE SET NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    return $db->query($sql);
}

function down_043_create_property_transfers_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS property_transfers");
}

