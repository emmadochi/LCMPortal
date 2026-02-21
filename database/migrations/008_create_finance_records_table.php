<?php
/**
 * Migration: Create finance_records table
 */

function up_008_create_finance_records_table() {
    $db = \App\Core\Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS finance_records (
        id INT AUTO_INCREMENT PRIMARY KEY,
        unit_id INT NOT NULL,
        transaction_type ENUM('income', 'expense') NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        description TEXT,
        category VARCHAR(100),
        transaction_date DATE NOT NULL,
        recorded_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE CASCADE,
        FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_unit_id (unit_id),
        INDEX idx_transaction_type (transaction_type),
        INDEX idx_transaction_date (transaction_date),
        INDEX idx_category (category)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $db->query($sql);
}

function down_008_create_finance_records_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS finance_records");
}

