<?php
/**
 * Migration: Add unit_head_user_id to church_units table
 * 
 * This allows each church to have a specific head for each of its units,
 * separate from the global Unit Director.
 */

function up_055_add_unit_head_to_church_units() {
    $db = \App\Core\Database::getInstance();
    
    // Add the column
    $sql = "ALTER TABLE church_units 
            ADD COLUMN unit_head_user_id INT NULL AFTER assigned_by,
            ADD CONSTRAINT fk_church_unit_head FOREIGN KEY (unit_head_user_id) REFERENCES users(id) ON DELETE SET NULL";
    
    return $db->query($sql);
}

function down_055_add_unit_head_to_church_units() {
    $db = \App\Core\Database::getInstance();
    
    // Drop the constraint and column
    $db->query("ALTER TABLE church_units DROP FOREIGN KEY fk_church_unit_head");
    return $db->query("ALTER TABLE church_units DROP COLUMN unit_head_user_id");
}
