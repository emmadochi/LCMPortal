<?php
/**
 * Migration: Add church_id and assigned_to_user_id to properties.
 */

function up_042_add_church_and_assigned_to_properties() {
    $db = \App\Core\Database::getInstance();

    // Add columns and indexes
    $sql1 = "ALTER TABLE properties
        ADD COLUMN church_id INT NULL AFTER id,
        ADD COLUMN assigned_to_user_id INT NULL AFTER created_by,
        ADD INDEX idx_properties_church_id (church_id),
        ADD INDEX idx_properties_assigned_to (assigned_to_user_id)";

    // Add foreign keys
    $sql2 = "ALTER TABLE properties
        ADD CONSTRAINT fk_properties_church_id
            FOREIGN KEY (church_id) REFERENCES churches(id) ON DELETE CASCADE,
        ADD CONSTRAINT fk_properties_assigned_user_id
            FOREIGN KEY (assigned_to_user_id) REFERENCES users(id) ON DELETE SET NULL";

    if (!$db->query($sql1)) {
        return false;
    }
    if (!$db->query($sql2)) {
        return false;
    }

    return true;
}

function down_042_add_church_and_assigned_to_properties() {
    $db = \App\Core\Database::getInstance();

    // Drop foreign keys and indexes if they exist
    @$db->query("ALTER TABLE properties DROP FOREIGN KEY fk_properties_church_id");
    @$db->query("ALTER TABLE properties DROP FOREIGN KEY fk_properties_assigned_user_id");
    @$db->query("ALTER TABLE properties DROP INDEX idx_properties_church_id");
    @$db->query("ALTER TABLE properties DROP INDEX idx_properties_assigned_to");
    @$db->query("ALTER TABLE properties DROP COLUMN church_id");
    @$db->query("ALTER TABLE properties DROP COLUMN assigned_to_user_id");

    return true;
}

