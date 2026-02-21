<?php
/**
 * Migration: Add user_id to finance_records (alongside recorded_by)
 */

function up_032_add_user_id_to_finance_records() {
    $db = \App\Core\Database::getInstance();

    // Add user_id column (nullable for backfill) and index
    $sql1 = "ALTER TABLE finance_records
        ADD COLUMN user_id INT NULL AFTER unit_id,
        ADD INDEX idx_finance_user_id (user_id)";

    // Add foreign key to users (if not already present)
    $sql2 = "ALTER TABLE finance_records
        ADD CONSTRAINT fk_finance_records_user_id
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL";

    $ok = $db->query($sql1);
    if (!$ok) return false;

    $ok = $db->query($sql2);
    if (!$ok) return false;

    return true;
}

function down_032_add_user_id_to_finance_records() {
    $db = \App\Core\Database::getInstance();

    // Drop foreign key and column (if they exist)
    $db->query("ALTER TABLE finance_records DROP FOREIGN KEY fk_finance_records_user_id");
    $db->query("ALTER TABLE finance_records DROP INDEX idx_finance_user_id");
    $db->query("ALTER TABLE finance_records DROP COLUMN user_id");

    return true;
}

