<?php
use App\Core\Database;

class CreateEvangelismConvertsAndFollowupEcosystem {
    public function up() {
        $db = Database::getInstance();

        // 1. Table: evangelism_converts
        $sql1 = "CREATE TABLE IF NOT EXISTS evangelism_converts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            report_id INT NULL,
            soul_winner_id INT NOT NULL,
            church_id INT NULL,
            full_name VARCHAR(150) NOT NULL,
            phone VARCHAR(50) NULL,
            email VARCHAR(150) NULL,
            address TEXT NULL,
            gender ENUM('male', 'female', 'other') NULL,
            decision_type ENUM('salvation', 'rededication', 'healing_miracle', 'inquiry') DEFAULT 'salvation',
            prayer_requests TEXT NULL,
            status ENUM('new', 'in_followup', 'attending_service', 'baptized_holy_ghost', 'baptized_water', 'discipled', 'inactive') DEFAULT 'new',
            
            -- Spiritual Journey Milestones
            first_contact_done TINYINT(1) DEFAULT 0,
            attended_service TINYINT(1) DEFAULT 0,
            baptized_holy_ghost TINYINT(1) DEFAULT 0,
            baptized_water TINYINT(1) DEFAULT 0,
            foundation_class_enrolled TINYINT(1) DEFAULT 0,
            department_joined VARCHAR(100) NULL,
            
            next_followup_date DATE NULL,
            assigned_mentor_id INT NULL,
            pastoral_notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_soul_winner (soul_winner_id),
            INDEX idx_church (church_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $db->query($sql1);
        } catch (\Exception $e) {
            error_log("Migration 062 Error 1: " . $e->getMessage());
        }

        // 2. Table: evangelism_followup_logs
        $sql2 = "CREATE TABLE IF NOT EXISTS evangelism_followup_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            convert_id INT NOT NULL,
            user_id INT NOT NULL,
            contact_method ENUM('phone_call', 'whatsapp_sms', 'home_visit', 'church_meeting', 'prayer_session') DEFAULT 'phone_call',
            outcome ENUM('reached_receptive', 'reached_busy', 'not_answering', 'number_invalid', 'attended_service', 'prayer_answered') DEFAULT 'reached_receptive',
            notes TEXT NULL,
            milestone_updated VARCHAR(50) NULL,
            next_action_date DATE NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_convert (convert_id),
            INDEX idx_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        try {
            $db->query($sql2);
        } catch (\Exception $e) {
            error_log("Migration 062 Error 2: " . $e->getMessage());
        }

        // 3. Table: evangelism_pastoral_notes
        $sql3 = "CREATE TABLE IF NOT EXISTS evangelism_pastoral_notes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            pastor_id INT NOT NULL,
            church_id INT NULL,
            message TEXT NOT NULL,
            badge_type VARCHAR(50) DEFAULT 'commendation',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_pastor (user_id),
            INDEX idx_pastor (pastor_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        try {
            $db->query($sql3);
        } catch (\Exception $e) {
            error_log("Migration 062 Error 3: " . $e->getMessage());
        }
    }

    public function down() {
        $db = Database::getInstance();
        try {
            $db->query("DROP TABLE IF EXISTS evangelism_pastoral_notes");
            $db->query("DROP TABLE IF EXISTS evangelism_followup_logs");
            $db->query("DROP TABLE IF EXISTS evangelism_converts");
        } catch (\Exception $e) {}
    }
}
