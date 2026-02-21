<?php
/**
 * Migration: Add pastor_user_id to churches
 *
 * Links church to a registered user (pastor role) instead of free-text pastor_name.
 */

function up_028_add_pastor_user_id_to_churches() {
    $db = \App\Core\Database::getInstance();

    $db->query("ALTER TABLE churches ADD COLUMN pastor_user_id INT NULL DEFAULT NULL AFTER pastor_name");
    $db->query("ALTER TABLE churches ADD INDEX idx_pastor_user_id (pastor_user_id)");
    $db->query("ALTER TABLE churches ADD CONSTRAINT fk_churches_pastor_user FOREIGN KEY (pastor_user_id) REFERENCES users(id) ON DELETE SET NULL");
    return true;
}

function down_028_add_pastor_user_id_to_churches() {
    $db = \App\Core\Database::getInstance();

    $db->query("ALTER TABLE churches DROP FOREIGN KEY fk_churches_pastor_user");
    $db->query("ALTER TABLE churches DROP INDEX idx_pastor_user_id");
    $db->query("ALTER TABLE churches DROP COLUMN pastor_user_id");
    return true;
}
