<?php
namespace App\Models;

class PropertyTransfer extends BaseModel {
    protected $table = 'property_transfers';
    protected $fillable = [
        'property_id',
        'from_church_id',
        'to_church_id',
        'from_location',
        'to_location',
        'user_id',
        'notes',
    ];
}

