<?php
/**
 * Migration: Add head pastor assignment to churches
 * 
 * This migration adds the ability to assign a head pastor to each church
 * and creates the necessary relationship between users and churches.
 */

function up_027_add_head_pastor_to_churches() {
    $db = \App\Core\Database::getInstance();
    
    // Add head_pastor_user_id column to churches table
    $sql = "ALTER TABLE churches 
            ADD COLUMN head_pastor_user_id INT NULL AFTER pastor_name,
            ADD FOREIGN KEY (head_pastor_user_id) REFERENCES users(id) ON DELETE SET NULL,
            ADD INDEX idx_head_pastor_user_id (head_pastor_user_id)";
    
    return $db->query($sql);
}

function down_027_add_head_pastor_to_churches() {
    $db = \App\Core\Database::getInstance();
    
    // Remove the column and foreign key
    $sql = "ALTER TABLE churches 
            DROP FOREIGN KEY churches_ibfk_2,
            DROP INDEX idx_head_pastor_user_id,
            DROP COLUMN head_pastor_user_id";
    
    return $db->query($sql);
}