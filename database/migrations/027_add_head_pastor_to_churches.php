<?php
/**
 * Migration: Add head pastor assignment to churches
 */

function up_027_add_head_pastor_to_churches() {
    $db = \App\Core\Database::getInstance();
    
    $cols = $db->query("SHOW COLUMNS FROM churches LIKE 'head_pastor_user_id'");
    if ($cols && $cols->num_rows === 0) {
        $db->query("ALTER TABLE churches ADD COLUMN head_pastor_user_id INT NULL AFTER pastor_name");
        $db->query("ALTER TABLE churches ADD INDEX idx_head_pastor_user_id (head_pastor_user_id)");
    }
    
    return true;
}

function down_027_add_head_pastor_to_churches() {
    $db = \App\Core\Database::getInstance();
    
    $cols = $db->query("SHOW COLUMNS FROM churches LIKE 'head_pastor_user_id'");
    if ($cols && $cols->num_rows > 0) {
        $db->query("ALTER TABLE churches DROP COLUMN head_pastor_user_id");
    }
    
    return true;
}