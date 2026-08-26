<?php
/**
 * Migration: Add phone and address columns to users table
 */

function up_057_add_phone_and_address_to_users() {
    $db = \App\Core\Database::getInstance();
    
    // Check if columns already exist to avoid errors
    $check = $db->query("SHOW COLUMNS FROM users LIKE 'phone'");
    if ($check->num_rows === 0) {
        $sql = "ALTER TABLE users 
                ADD COLUMN phone VARCHAR(20) NULL AFTER age_group,
                ADD COLUMN address VARCHAR(255) NULL AFTER phone";
        return $db->query($sql);
    }
    
    return true;
}

function down_057_add_phone_and_address_to_users() {
    $db = \App\Core\Database::getInstance();
    
    return $db->query("ALTER TABLE users 
                       DROP COLUMN phone,
                       DROP COLUMN address");
}
