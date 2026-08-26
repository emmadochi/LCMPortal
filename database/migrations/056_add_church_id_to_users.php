<?php
/**
 * Migration: Add church_id to users table
 * 
 * Establishes a direct association between a user/member and a specific church branch.
 */

function up_056_add_church_id_to_users() {
    $db = \App\Core\Database::getInstance();
    
    // Add the column and foreign key constraint
    $sql = "ALTER TABLE users 
            ADD COLUMN church_id INT NULL AFTER role,
            ADD CONSTRAINT fk_user_church FOREIGN KEY (church_id) REFERENCES churches(id) ON DELETE SET NULL";
    
    return $db->query($sql);
}

function down_056_add_church_id_to_users() {
    $db = \App\Core\Database::getInstance();
    
    // Drop the constraint and column
    $db->query("ALTER TABLE users DROP FOREIGN KEY fk_user_church");
    return $db->query("ALTER TABLE users DROP COLUMN church_id");
}
