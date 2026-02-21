<?php
/**
 * Migration: Add church_id and allow nullable unit_id for church-wide attendance
 * Church-wide services (e.g. main Sunday service) are stored with church_id set and unit_id NULL.
 */

function up_029_add_church_wide_attendance() {
    $db = \App\Core\Database::getInstance();

    // Add church_id (nullable) for church-wide attendance
    $db->query("ALTER TABLE attendance ADD COLUMN church_id INT NULL AFTER unit_id");
    $db->query("ALTER TABLE attendance ADD INDEX idx_church_id (church_id)");
    $db->query("ALTER TABLE attendance ADD CONSTRAINT fk_attendance_church FOREIGN KEY (church_id) REFERENCES churches(id) ON DELETE CASCADE");

    // Allow unit_id to be NULL when recording church-wide attendance
    $db->query("ALTER TABLE attendance MODIFY COLUMN unit_id INT NULL");

    return true;
}

function down_029_add_church_wide_attendance() {
    $db = \App\Core\Database::getInstance();

    $db->query("ALTER TABLE attendance DROP FOREIGN KEY fk_attendance_church");
    $db->query("ALTER TABLE attendance DROP INDEX idx_church_id");
    $db->query("ALTER TABLE attendance DROP COLUMN church_id");
    $db->query("ALTER TABLE attendance MODIFY COLUMN unit_id INT NOT NULL");

    return true;
}
