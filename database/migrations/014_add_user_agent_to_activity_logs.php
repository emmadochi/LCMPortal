<?php
/**
 * Migration: Add user_agent column to activity_logs table
 */

function up_014_add_user_agent_to_activity_logs() {
    $db = \App\Core\Database::getInstance();

    // Add user_agent column if it doesn't exist
    $sql = "ALTER TABLE activity_logs 
            ADD COLUMN user_agent VARCHAR(255) NULL AFTER ip_address";

    try {
        return $db->query($sql);
    } catch (\mysqli_sql_exception $e) {
        // If the column already exists, just treat as success
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            return true;
        }
        throw $e;
    }
}

function down_014_add_user_agent_to_activity_logs() {
    $db = \App\Core\Database::getInstance();

    $sql = "ALTER TABLE activity_logs 
            DROP COLUMN user_agent";

    try {
        return $db->query($sql);
    } catch (\mysqli_sql_exception $e) {
        // If the column doesn't exist, ignore
        if (strpos($e->getMessage(), 'Check that column/key exists') !== false) {
            return true;
        }
        throw $e;
    }
}


