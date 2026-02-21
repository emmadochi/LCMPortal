<?php
namespace App\Models;

class OutreachTarget extends BaseModel {
    protected $table = 'outreach_targets';
    protected $fillable = ['outreach_report_id', 'target_name', 'target_value', 'actual_value', 'unit', 'notes', 'sort_order'];
}
