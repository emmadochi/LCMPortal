<?php
namespace App\Models;

class OutreachCost extends BaseModel {
    protected $table = 'outreach_costs';
    protected $fillable = ['outreach_report_id', 'category', 'budgeted_amount', 'actual_amount', 'notes', 'sort_order'];
}
