<?php
namespace App\Models;

class PropertyAssignmentLog extends BaseModel {
    protected $table = 'property_assignment_logs';
    protected $fillable = [
        'property_id',
        'from_user_id',
        'to_user_id',
        'assigned_by_user_id',
        'notes',
    ];
}

