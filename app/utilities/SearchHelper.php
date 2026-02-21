<?php
namespace App\Utilities;

/**
 * SearchHelper - Reusable search functionality
 */
class SearchHelper {
    /**
     * Build search conditions for database queries
     * 
     * @param array $searchFields Fields to search in
     * @param string $searchTerm Search term
     * @return array Search conditions
     */
    public static function buildSearchConditions($searchFields, $searchTerm) {
        if (empty($searchTerm) || empty($searchFields)) {
            return [];
        }
        
        $conditions = [];
        $searchTerm = trim($searchTerm);
        
        // Build LIKE conditions for each field
        foreach ($searchFields as $field) {
            $conditions[] = "{$field} LIKE ?";
        }
        
        return [
            'sql' => '(' . implode(' OR ', $conditions) . ')',
            'params' => array_fill(0, count($searchFields), "%{$searchTerm}%"),
            'types' => str_repeat('s', count($searchFields))
        ];
    }

    /**
     * Build filter conditions
     * 
     * @param array $filters Filter array (field => value)
     * @return array Filter conditions
     */
    public static function buildFilterConditions($filters) {
        if (empty($filters)) {
            return [];
        }
        
        $conditions = [];
        $params = [];
        $types = '';
        
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                $conditions[] = "{$field} = ?";
                $params[] = $value;
                $types .= is_int($value) ? 'i' : 's';
            }
        }
        
        return [
            'sql' => implode(' AND ', $conditions),
            'params' => $params,
            'types' => $types
        ];
    }

    /**
     * Sanitize search term
     * 
     * @param string $term Search term
     * @return string Sanitized term
     */
    public static function sanitize($term) {
        return trim(strip_tags($term));
    }
}

