<?php
/**
 * Migration: Add image_path to notification_broadcasts and notifications for attached images.
 */

function up_036_add_image_path_to_notifications() {
    $db = \App\Core\Database::getInstance();

    $db->query("ALTER TABLE notification_broadcasts ADD COLUMN image_path VARCHAR(500) NULL AFTER link");
    $db->query("ALTER TABLE notifications ADD COLUMN image_path VARCHAR(500) NULL AFTER link");

    return true;
}

function down_036_add_image_path_to_notifications() {
    $db = \App\Core\Database::getInstance();
    $db->query("ALTER TABLE notification_broadcasts DROP COLUMN image_path");
    $db->query("ALTER TABLE notifications DROP COLUMN image_path");
    return true;
}
