<?php
/**
 * Migration: Add payment_method and reference_number to finance_records
 */

function up_033_add_payment_fields_to_finance_records() {
    $db = \App\Core\Database::getInstance();

    // Add payment_method and reference_number as nullable fields
    $sql = "ALTER TABLE finance_records
        ADD COLUMN payment_method VARCHAR(50) NULL AFTER transaction_date,
        ADD COLUMN reference_number VARCHAR(191) NULL AFTER payment_method";

    return $db->query($sql);
}

function down_033_add_payment_fields_to_finance_records() {
    $db = \App\Core\Database::getInstance();

    $db->query("ALTER TABLE finance_records DROP COLUMN reference_number");
    $db->query("ALTER TABLE finance_records DROP COLUMN payment_method");

    return true;
}

