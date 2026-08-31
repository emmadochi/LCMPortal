<?php

class CreateDynamicUnitReportingEngine {
    public function up($db) {
        // 1. unit_report_templates table
        $sql = "CREATE TABLE IF NOT EXISTS `unit_report_templates` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `unit_id` INT NOT NULL,
            `church_id` INT NULL,
            `created_by` INT NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `description` TEXT NULL,
            `category` VARCHAR(100) DEFAULT 'General',
            `frequency` ENUM('weekly', 'biweekly', 'monthly', 'per_service', 'on_demand') DEFAULT 'weekly',
            `deadline_day` VARCHAR(20) DEFAULT 'Sunday',
            `deadline_time` TIME DEFAULT '18:00:00',
            `is_global` TINYINT(1) DEFAULT 0,
            `is_active` TINYINT(1) DEFAULT 1,
            `version` INT DEFAULT 1,
            `allow_attachments` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX (`unit_id`),
            INDEX (`church_id`),
            INDEX (`created_by`),
            INDEX (`is_global`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $db->query($sql);

        // 2. unit_report_fields table
        $sql = "CREATE TABLE IF NOT EXISTS `unit_report_fields` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `template_id` INT NOT NULL,
            `field_label` VARCHAR(255) NOT NULL,
            `field_key` VARCHAR(100) NOT NULL,
            `field_type` ENUM('text', 'number', 'textarea', 'select', 'checkbox', 'date', 'time', 'file') DEFAULT 'text',
            `field_options` TEXT NULL,
            `placeholder` VARCHAR(255) NULL,
            `default_value` VARCHAR(255) NULL,
            `help_text` VARCHAR(255) NULL,
            `is_required` TINYINT(1) DEFAULT 0,
            `is_confidential` TINYINT(1) DEFAULT 0,
            `formula` VARCHAR(255) NULL,
            `sort_order` INT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (`template_id`),
            INDEX (`sort_order`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $db->query($sql);

        // 3. unit_report_assignments table
        $sql = "CREATE TABLE IF NOT EXISTS `unit_report_assignments` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `template_id` INT NOT NULL,
            `unit_id` INT NOT NULL,
            `church_id` INT NULL,
            `target_type` ENUM('all_unit_members', 'officers_only', 'specific_members') DEFAULT 'all_unit_members',
            `assigned_user_ids` TEXT NULL,
            `due_date` DATE NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (`template_id`),
            INDEX (`unit_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $db->query($sql);

        // 4. unit_report_submissions table
        $sql = "CREATE TABLE IF NOT EXISTS `unit_report_submissions` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `template_id` INT NOT NULL,
            `template_version` INT DEFAULT 1,
            `unit_id` INT NOT NULL,
            `church_id` INT NULL,
            `user_id` INT NOT NULL,
            `report_period` VARCHAR(50) NOT NULL,
            `status` ENUM('draft', 'submitted', 'under_review', 'approved', 'needs_revision') DEFAULT 'submitted',
            `submitted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `reviewed_by` INT NULL,
            `reviewed_at` DATETIME NULL,
            `director_feedback` TEXT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX (`template_id`),
            INDEX (`unit_id`),
            INDEX (`church_id`),
            INDEX (`user_id`),
            INDEX (`status`),
            INDEX (`report_period`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $db->query($sql);

        // 5. unit_report_submission_values table
        $sql = "CREATE TABLE IF NOT EXISTS `unit_report_submission_values` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `submission_id` INT NOT NULL,
            `field_id` INT NULL,
            `field_key` VARCHAR(100) NOT NULL,
            `field_label` VARCHAR(255) NOT NULL,
            `field_type` VARCHAR(50) NOT NULL,
            `field_value` LONGTEXT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (`submission_id`),
            INDEX (`field_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $db->query($sql);

        // 6. unit_report_submission_files table
        $sql = "CREATE TABLE IF NOT EXISTS `unit_report_submission_files` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `submission_id` INT NOT NULL,
            `field_key` VARCHAR(100) NULL,
            `file_name` VARCHAR(255) NOT NULL,
            `file_path` VARCHAR(500) NOT NULL,
            `file_type` VARCHAR(100) NULL,
            `file_size` INT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (`submission_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $db->query($sql);
    }

    public function down($db) {
        $db->query("DROP TABLE IF EXISTS `unit_report_submission_files`;");
        $db->query("DROP TABLE IF EXISTS `unit_report_submission_values`;");
        $db->query("DROP TABLE IF EXISTS `unit_report_submissions`;");
        $db->query("DROP TABLE IF EXISTS `unit_report_assignments`;");
        $db->query("DROP TABLE IF EXISTS `unit_report_fields`;");
        $db->query("DROP TABLE IF EXISTS `unit_report_templates`;");
    }
}
