<?php
use App\Core\Database;

class CreateEvangelismReportsTable {
    public function up() {
        $db = Database::getInstance();
        $sql = "CREATE TABLE evangelism_reports (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            report_date DATE NOT NULL,
            souls_won INT NOT NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )";
        $db->query($sql);
    }

    public function down() {
        $db = Database::getInstance();
        $sql = "DROP TABLE evangelism_reports";
        $db->query($sql);
    }
}
