<?php
/**
 * Migration: Add audience_scope to notification_broadcasts for church/unit scoped broadcasts.
 */

function up_038_add_audience_scope_to_notification_broadcasts() {
    $db = \App\Core\Database::getInstance();
    $sql = "ALTER TABLE notification_broadcasts ADD COLUMN audience_scope JSON NULL AFTER audience_roles";
    return $db->query($sql);
}

function down_038_add_audience_scope_to_notification_broadcasts() {
    $db = \App\Core\Database::getInstance();
    return $db->query("ALTER TABLE notification_broadcasts DROP COLUMN audience_scope");
}
