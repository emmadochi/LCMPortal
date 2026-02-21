<?php
/**
 * Migration: Add age_group to users (adult, child, teen) for attendance segment reporting.
 * Avoids asking for exact age; users choose a category.
 */

function up_045_add_age_group_to_users() {
    $db = \App\Core\Database::getInstance();
    $db->query("ALTER TABLE users ADD COLUMN age_group ENUM('adult','child','teen') NULL AFTER last_name");
    return true;
}

function down_045_add_age_group_to_users() {
    $db = \App\Core\Database::getInstance();
    $db->query("ALTER TABLE users DROP COLUMN age_group");
    return true;
}
