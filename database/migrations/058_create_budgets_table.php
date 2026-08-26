<?php
/**
 * Migration: Create budgets table
 */

function up_058_create_budgets_table() {
    $db = \App\Core\Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS budgets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        church_id INT NOT NULL,
        unit_id INT NULL,
        title VARCHAR(255) NOT NULL,
        fiscal_year INT NOT NULL,
        period_type ENUM('annual', 'quarterly', 'monthly', 'custom') DEFAULT 'annual',
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        total_budget_amount DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
        category VARCHAR(100) NULL,
        description TEXT NULL,
        status ENUM('draft', 'active', 'closed', 'archived') DEFAULT 'active',
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (church_id) REFERENCES churches(id) ON DELETE CASCADE,
        FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE SET NULL,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_budget_church (church_id),
        INDEX idx_budget_unit (unit_id),
        INDEX idx_budget_year (fiscal_year),
        INDEX idx_budget_category (category),
        INDEX idx_budget_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    return $db->query($sql);
}

function down_058_create_budgets_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS budgets");
}
