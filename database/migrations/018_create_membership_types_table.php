<?php
/**
 * Migration: Create membership_types table
 */

function up_018_create_membership_types_table() {
    $db = \App\Core\Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS membership_types (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL UNIQUE,
        description TEXT,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_name (name),
        INDEX idx_is_active (is_active)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $result = $db->query($sql);
    
    // Seed default membership types
    if ($result) {
        $membershipTypeModel = new \App\Models\MembershipType();
        $membershipTypeModel->seedDefaultTypes();
    }
    
    return $result;
}

function down_018_create_membership_types_table() {
    $db = \App\Core\Database::getInstance();
    return $db->query("DROP TABLE IF EXISTS membership_types");
}

// Update memberships table to use foreign key relationship
function up_019_update_memberships_foreign_key() {
    $db = \App\Core\Database::getInstance();
    
    // Add membership_type_id column
    $sql = "ALTER TABLE memberships 
            ADD COLUMN membership_type_id INT NULL AFTER membership_type,
            ADD FOREIGN KEY (membership_type_id) REFERENCES membership_types(id) ON DELETE SET NULL,
            ADD INDEX idx_membership_type_id (membership_type_id)";
    
    return $db->query($sql);
}

function down_019_update_memberships_foreign_key() {
    $db = \App\Core\Database::getInstance();
    
    $sql = "ALTER TABLE memberships 
            DROP FOREIGN KEY memberships_ibfk_3,
            DROP INDEX idx_membership_type_id,
            DROP COLUMN membership_type_id";
    
    return $db->query($sql);
}

// Migrate existing membership_type data to use membership_type_id
function up_020_migrate_membership_types() {
    $db = \App\Core\Database::getInstance();
    
    try {
        // Get all membership types and their IDs
        $stmt = $db->prepare("SELECT id, name FROM membership_types");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $typeMapping = [];
        while ($row = $result->fetch_assoc()) {
            $typeMapping[strtolower($row['name'])] = $row['id'];
        }
        
        // Update memberships table to set membership_type_id based on membership_type
        foreach ($typeMapping as $typeName => $typeId) {
            $updateStmt = $db->prepare("UPDATE memberships SET membership_type_id = ? WHERE LOWER(membership_type) = ?");
            $updateStmt->bind_param("is", $typeId, $typeName);
            $updateStmt->execute();
        }
        
        return true;
        
    } catch (\Exception $e) {
        error_log('Error migrating membership types: ' . $e->getMessage());
        return false;
    }
}

function down_020_migrate_membership_types() {
    $db = \App\Core\Database::getInstance();
    
    // This migration doesn't need a down function as it's data migration
    return true;
}