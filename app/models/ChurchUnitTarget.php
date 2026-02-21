<?php
namespace App\Models;

class ChurchUnitTarget extends BaseModel {
    protected $table = 'church_unit_targets';
    protected $fillable = [
        'church_id', 'unit_id', 'target_type', 'target_value', 'period_type', 'period_value',
        'unit_label', 'notes'
    ];

    /** Target type options for dropdowns */
    public static function getTargetTypes() {
        return [
            'attendance' => 'Attendance',
            'new_members' => 'New Members',
            'offering' => 'Offering',
            'events' => 'Events / Programs',
            'first_timers' => 'First Timers',
        ];
    }

    /** Period type options */
    public static function getPeriodTypes() {
        return [
            'month' => 'Month',
            'quarter' => 'Quarter',
            'year' => 'Year',
        ];
    }

    /**
     * Get targets with church and unit names, with optional filters.
     *
     * @param array $filters ['church_id' => int, 'unit_id' => int, 'period_type' => string, 'period_value' => string]
     * @param string|null $orderBy
     * @param int|null $limit
     * @return array
     */
    public function getTargetsWithDetails(array $filters = [], $orderBy = null, $limit = null) {
        $sql = "SELECT t.*, c.name as church_name, u.name as unit_name
                FROM {$this->table} t
                INNER JOIN churches c ON t.church_id = c.id
                LEFT JOIN units u ON t.unit_id = u.id
                WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($filters['church_id'])) {
            $sql .= " AND t.church_id = ?";
            $params[] = (int)$filters['church_id'];
            $types .= 'i';
        }
        if (isset($filters['unit_id']) && $filters['unit_id'] !== '' && $filters['unit_id'] !== null) {
            $sql .= " AND t.unit_id = ?";
            $params[] = (int)$filters['unit_id'];
            $types .= 'i';
        }
        if (!empty($filters['period_type'])) {
            $sql .= " AND t.period_type = ?";
            $params[] = $filters['period_type'];
            $types .= 's';
        }
        if (!empty($filters['period_value'])) {
            $sql .= " AND t.period_value = ?";
            $params[] = $filters['period_value'];
            $types .= 's';
        }

        $sql .= " ORDER BY " . ($orderBy ?: 't.period_value DESC, c.name ASC, t.target_type ASC');
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Check that the given unit_id is assigned to the given church (church_units).
     * If unit_id is null, returns true (church-wide).
     */
    public function isUnitInChurch($churchId, $unitId) {
        if ($unitId === null || $unitId === '') {
            return true;
        }
        $churchModel = new Church();
        $unitIds = $churchModel->getChurchUnitIds((int)$churchId);
        return in_array((int)$unitId, $unitIds, true);
    }

    /**
     * Get targets for a specific church (for church show page).
     */
    public function getTargetsByChurch($churchId, $orderBy = 'period_value DESC, target_type ASC') {
        return $this->getTargetsWithDetails(['church_id' => $churchId], $orderBy);
    }
}
