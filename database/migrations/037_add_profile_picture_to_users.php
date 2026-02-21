<?php
/**
 * Migration: Add profile_picture to users table.
 */

function up_037_add_profile_picture_to_users() {
    $db = \App\Core\Database::getInstance();
    return $db->query("ALTER TABLE users ADD COLUMN profile_picture VARCHAR(500) NULL AFTER last_name");
}

function down_037_add_profile_picture_to_users() {
    $db = \App\Core\Database::getInstance();
    return $db->query("ALTER TABLE users DROP COLUMN profile_picture");
}
