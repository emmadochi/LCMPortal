<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Base Model
 * 
 * Provides common database operations for all modules.
 * Extend this model for consistent CRUD functionality.
 * 
 * @author Professional Development Team
 * @version 1.0
 */
abstract class BaseModuleModel extends BaseModel
{
    /**
     * @var string Database table name
     */
    protected $table;
    
    /**
     * @var string Primary key column name
     */
    protected $primaryKey = 'id';
    
    /**
     * @var array Fillable fields for mass assignment
     */
    protected $fillable = [];
    
    /**
     * @var array Fields to exclude from mass assignment
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    /**
     * @var array Date fields that should be converted to DateTime objects
     */
    protected $dates = ['created_at', 'updated_at'];
    
    /**
     * @var array Validation rules
     */
    protected $rules = [];
    
    /**
     * Get all records for a specific church
     * 
     * @param int $churchId
     * @param array $filters
     * @param string $orderBy
     * @param string $orderDir
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getByChurch($churchId, $filters = [], $orderBy = 'created_at', $orderDir = 'DESC', $limit = null, $offset = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE church_id = :church_id";
        $params = ['church_id' => $churchId];
        
        // Apply filters
        $sql = $this->applyFilters($sql, $params, $filters);
        
        // Apply ordering
        $sql .= " ORDER BY {$orderBy} {$orderDir}";
        
        // Apply pagination
        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            $params['limit'] = $limit;
        }
        
        if ($offset !== null) {
            $sql .= " OFFSET :offset";
            $params['offset'] = $offset;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Convert date fields
        return $this->convertDates($results);
    }
    
    /**
     * Get count of records for a specific church
     * 
     * @param int $churchId
     * @param array $filters
     * @return int
     */
    public function getCountByChurch($churchId, $filters = [])
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE church_id = :church_id";
        $params = ['church_id' => $churchId];
        
        // Apply filters
        $sql = $this->applyFilters($sql, $params, $filters);
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Create new record
     * 
     * @param array $data
     * @return int|false
     */
    public function create($data)
    {
        // Filter data to only include fillable fields
        $filteredData = $this->filterFillable($data);
        
        // Add timestamps
        $filteredData['created_at'] = date('Y-m-d H:i:s');
        $filteredData['updated_at'] = date('Y-m-d H:i:s');
        
        $columns = array_keys($filteredData);
        $placeholders = ':' . implode(', :', $columns);
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES ({$placeholders})";
        
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute($filteredData)) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update existing record
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        // Filter data to only include fillable fields
        $filteredData = $this->filterFillable($data);
        
        // Add updated timestamp
        $filteredData['updated_at'] = date('Y-m-d H:i:s');
        
        $setParts = [];
        foreach (array_keys($filteredData) as $column) {
            $setParts[] = "{$column} = :{$column}";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE {$this->primaryKey} = :id";
        $filteredData['id'] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($filteredData);
    }
    
    /**
     * Delete record
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Find record by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return $this->convertDates([$result])[0];
        }
        
        return null;
    }
    
    /**
     * Find record by ID for specific church
     * 
     * @param int $id
     * @param int $churchId
     * @return array|null
     */
    public function findByChurch($id, $churchId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id AND church_id = :church_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':church_id', $churchId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return $this->convertDates([$result])[0];
        }
        
        return null;
    }
    
    /**
     * Apply filters to query
     * 
     * @param string $sql
     * @param array $params
     * @param array $filters
     * @return string
     */
    protected function applyFilters($sql, &$params, $filters)
    {
        $conditions = [];
        
        // Search filter
        if (!empty($filters['search'])) {
            $searchFields = $this->getSearchFields();
            if (!empty($searchFields)) {
                $searchConditions = [];
                foreach ($searchFields as $field) {
                    $searchConditions[] = "{$field} LIKE :search";
                }
                $conditions[] = '(' . implode(' OR ', $searchConditions) . ')';
                $params['search'] = '%' . $filters['search'] . '%';
            }
        }
        
        // Date range filters
        if (!empty($filters['date_from'])) {
            $conditions[] = "DATE(created_at) >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $conditions[] = "DATE(created_at) <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        
        // Status filter
        if (isset($filters['status']) && $filters['status'] !== '') {
            $conditions[] = "status = :status";
            $params['status'] = $filters['status'];
        }
        
        // Add conditions to SQL
        if (!empty($conditions)) {
            $sql .= ' AND ' . implode(' AND ', $conditions);
        }
        
        return $sql;
    }
    
    /**
     * Get fields that can be searched
     * Override in child classes
     * 
     * @return array
     */
    protected function getSearchFields()
    {
        return ['title', 'description', 'name'];
    }
    
    /**
     * Filter data to only include fillable fields
     * 
     * @param array $data
     * @return array
     */
    protected function filterFillable($data)
    {
        $filtered = [];
        
        foreach ($data as $key => $value) {
            // Skip guarded fields
            if (in_array($key, $this->guarded)) {
                continue;
            }
            
            // If fillable is defined, only allow fillable fields
            if (!empty($this->fillable) && !in_array($key, $this->fillable)) {
                continue;
            }
            
            $filtered[$key] = $value;
        }
        
        return $filtered;
    }
    
    /**
     * Convert date fields to DateTime objects
     * 
     * @param array $results
     * @return array
     */
    protected function convertDates($results)
    {
        foreach ($results as &$row) {
            foreach ($this->dates as $dateField) {
                if (isset($row[$dateField]) && $row[$dateField]) {
                    $row[$dateField] = new \DateTime($row[$dateField]);
                }
            }
        }
        
        return $results;
    }
    
    /**
     * Validate data against rules
     * 
     * @param array $data
     * @param int|null $id
     * @return array
     */
    public function validate($data, $id = null)
    {
        $errors = [];
        
        foreach ($this->rules as $field => $rules) {
            $rulesArray = is_array($rules) ? $rules : explode('|', $rules);
            
            foreach ($rulesArray as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleParam = isset($ruleParts[1]) ? $ruleParts[1] : null;
                
                switch ($ruleName) {
                    case 'required':
                        if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                            $errors[$field] = ucfirst($field) . ' is required.';
                        }
                        break;
                        
                    case 'email':
                        if (isset($data[$field]) && $data[$field] && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = 'Invalid email format.';
                        }
                        break;
                        
                    case 'min':
                        if (isset($data[$field]) && strlen($data[$field]) < $ruleParam) {
                            $errors[$field] = ucfirst($field) . " must be at least {$ruleParam} characters.";
                        }
                        break;
                        
                    case 'max':
                        if (isset($data[$field]) && strlen($data[$field]) > $ruleParam) {
                            $errors[$field] = ucfirst($field) . " must not exceed {$ruleParam} characters.";
                        }
                        break;
                        
                    case 'numeric':
                        if (isset($data[$field]) && $data[$field] && !is_numeric($data[$field])) {
                            $errors[$field] = ucfirst($field) . ' must be numeric.';
                        }
                        break;
                        
                    case 'unique':
                        if (isset($data[$field]) && $this->isDuplicate($field, $data[$field], $id)) {
                            $errors[$field] = ucfirst($field) . ' already exists.';
                        }
                        break;
                }
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Check if field value already exists (for unique validation)
     * 
     * @param string $field
     * @param mixed $value
     * @param int|null $excludeId
     * @return bool
     */
    protected function isDuplicate($field, $value, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE {$field} = :value";
        $params = ['value' => $value];
        
        if ($excludeId) {
            $sql .= " AND {$this->primaryKey} != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
}