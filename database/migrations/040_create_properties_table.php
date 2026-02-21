<?php
/**
 * Migration: Create properties table for church assets/items.
 */

function up_040_create_properties_table() {
    $db = \App\Core\Database::getInstance();

    $sql = "CREATE TABLE IF NOT EXISTS properties (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT NULL,
        status VARCHAR(50) NOT NULL DEFAULT 'available',
        image_path VARCHAR(500) NULL,
        location VARCHAR(255) NULL,
        purchase_date DATE NULL,
        purchase_cost DECIMAL(10,2) NULL,
        serial_number VARCHAR(255) NULL,
        notes TEXT NULL,
        created_by INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_category (category_id),
        INDEX idx_status (status),
        INDEX idx_name (name),
        INDEX idx_created_by (created_by),
        FOREIGN KEY (category_id) REFERENCES property_categories(id) ON DELETE RESTRICT,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    return $db->query($sql);
}

function down_040_create_properties_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS properties");
}
