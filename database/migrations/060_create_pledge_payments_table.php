<?php
/**
 * Migration: Create pledge_payments table
 */

function up_060_create_pledge_payments_table() {
    $db = \App\Core\Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS pledge_payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        pledge_id INT NOT NULL,
        finance_record_id INT NULL,
        amount DECIMAL(12, 2) NOT NULL,
        payment_date DATE NOT NULL,
        payment_method VARCHAR(50) DEFAULT 'cash',
        reference_number VARCHAR(100) NULL,
        receipt_number VARCHAR(100) NULL,
        notes TEXT NULL,
        recorded_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (pledge_id) REFERENCES pledges(id) ON DELETE CASCADE,
        FOREIGN KEY (finance_record_id) REFERENCES finance_records(id) ON DELETE SET NULL,
        FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_payment_pledge (pledge_id),
        INDEX idx_payment_finance (finance_record_id),
        INDEX idx_payment_date (payment_date),
        INDEX idx_payment_receipt (receipt_number)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $db->query($sql);
}

function down_060_create_pledge_payments_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS pledge_payments");
}
