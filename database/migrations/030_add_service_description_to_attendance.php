<?php
/**
 * Migration: Add optional service_description to attendance for roll-call context
 * (e.g. "First service", "Easter Sunday"). Stored per row; same value for all rows of a service.
 */

function up_030_add_service_description_to_attendance() {
    $db = \App\Core\Database::getInstance();
    $db->query("ALTER TABLE attendance ADD COLUMN service_description VARCHAR(255) NULL AFTER event_type");
    return true;
}

function down_030_add_service_description_to_attendance() {
    $db = \App\Core\Database::getInstance();
    $db->query("ALTER TABLE attendance DROP COLUMN service_description");
    return true;
}
