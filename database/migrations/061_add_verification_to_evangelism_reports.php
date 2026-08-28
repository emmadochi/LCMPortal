<?php
use App\Core\Database;

class AddVerificationToEvangelismReports {
    public function up() {
        $db = Database::getInstance();
        
        // Add status column if not exists
        try {
            $db->query("ALTER TABLE evangelism_reports ADD COLUMN status ENUM('pending', 'verified', 'flagged') DEFAULT 'verified' AFTER notes");
        } catch (\Exception $e) {}

        // Add church_id column if not exists
        try {
            $db->query("ALTER TABLE evangelism_reports ADD COLUMN church_id INT NULL AFTER user_id");
        } catch (\Exception $e) {}

        // Add verified_by column if not exists
        try {
            $db->query("ALTER TABLE evangelism_reports ADD COLUMN verified_by INT NULL AFTER status");
        } catch (\Exception $e) {}

        // Add verified_at column if not exists
        try {
            $db->query("ALTER TABLE evangelism_reports ADD COLUMN verified_at DATETIME NULL AFTER verified_by");
        } catch (\Exception $e) {}

        // Add converts_data JSON / text column if not exists
        try {
            $db->query("ALTER TABLE evangelism_reports ADD COLUMN converts_data TEXT NULL AFTER notes");
        } catch (\Exception $e) {}
    }

    public function down() {
        $db = Database::getInstance();
        try {
            $db->query("ALTER TABLE evangelism_reports DROP COLUMN converts_data, DROP COLUMN verified_at, DROP COLUMN verified_by, DROP COLUMN status, DROP COLUMN church_id");
        } catch (\Exception $e) {}
    }
}
