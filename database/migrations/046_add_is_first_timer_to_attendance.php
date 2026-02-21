<?php
/**
 * Migration: Add is_first_timer to attendance (per-church: first time at this church).
 * Used with age_group for segment reporting: returning/first-timer × adult/child/teen.
 */

function up_046_add_is_first_timer_to_attendance() {
    $db = \App\Core\Database::getInstance();
    $db->query("ALTER TABLE attendance ADD COLUMN is_first_timer TINYINT(1) NOT NULL DEFAULT 0 AFTER status");
    $db->query("ALTER TABLE attendance ADD INDEX idx_is_first_timer (is_first_timer)");
    return true;
}

function down_046_add_is_first_timer_to_attendance() {
    $db = \App\Core\Database::getInstance();
    $db->query("ALTER TABLE attendance DROP INDEX idx_is_first_timer");
    $db->query("ALTER TABLE attendance DROP COLUMN is_first_timer");
    return true;
}
