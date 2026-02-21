<?php
/**
 * Migration: Add member_id to finance_records to link income to specific members
 */

function up_034_add_member_id_to_finance_records() {
    $db = \App\Core\Database::getInstance();

    // Add member_id column (nullable) and index
    $sql1 = "ALTER TABLE finance_records
        ADD COLUMN member_id INT NULL AFTER user_id,
        ADD INDEX idx_finance_member_id (member_id)";

    // Add foreign key to users (member) if not already present
    $sql2 = "ALTER TABLE finance_records
        ADD CONSTRAINT fk_finance_records_member_id
        FOREIGN KEY (member_id) REFERENCES users(id)
        ON DELETE SET NULL";

    $ok = $db->query($sql1);
    if (!$ok) {
        return false;
    }

    $ok = $db->query($sql2);
    if (!$ok) {
        return false;
    }

    return true;
}

function down_034_add_member_id_to_finance_records() {
    $db = \App\Core\Database::getInstance();

    // Drop foreign key and column (if they exist)
    $db->query("ALTER TABLE finance_records DROP FOREIGN KEY fk_finance_records_member_id");
    $db->query("ALTER TABLE finance_records DROP INDEX idx_finance_member_id");
    $db->query("ALTER TABLE finance_records DROP COLUMN member_id");

    return true;
}

