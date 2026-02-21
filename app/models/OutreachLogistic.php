<?php
namespace App\Models;

class OutreachLogistic extends BaseModel {
    protected $table = 'outreach_logistics';
    protected $fillable = ['outreach_report_id', 'category', 'description', 'notes', 'sort_order'];
}
