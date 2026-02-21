<?php
/**
 * Migration: Add church_id to finance_records and allow unit_id to be nullable
 */

function up_031_add_church_id_to_finance_records() {
    $db = \App\Core\Database::getInstance();

    // Add church_id column (nullable for backfill) and index
    $sql1 = "ALTER TABLE finance_records
        ADD COLUMN church_id INT NULL AFTER id,
        ADD INDEX idx_church_id (church_id)";

    // Make unit_id nullable to support church-level transactions without a unit
    $sql2 = "ALTER TABLE finance_records
        MODIFY COLUMN unit_id INT NULL";

    // Add foreign key to churches (if not already present)
    $sql3 = "ALTER TABLE finance_records
        ADD CONSTRAINT fk_finance_records_church_id
        FOREIGN KEY (church_id) REFERENCES churches(id)
        ON DELETE CASCADE";

    $ok = $db->query($sql1);
    if (!$ok) return false;

    $ok = $db->query($sql2);
    if (!$ok) return false;

    $ok = $db->query($sql3);
    if (!$ok) return false;

    return true;
}

function down_031_add_church_id_to_finance_records() {
    $db = \App\Core\Database::getInstance();

    // Drop foreign key and column (if they exist)
    $db->query("ALTER TABLE finance_records DROP FOREIGN KEY fk_finance_records_church_id");
    $db->query("ALTER TABLE finance_records DROP INDEX idx_church_id");
    $db->query("ALTER TABLE finance_records DROP COLUMN church_id");

    // Revert unit_id to NOT NULL
    $db->query("ALTER TABLE finance_records MODIFY COLUMN unit_id INT NOT NULL");

    return true;
}

