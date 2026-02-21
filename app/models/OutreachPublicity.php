<?php
namespace App\Models;

class OutreachPublicity extends BaseModel {
    protected $table = 'outreach_publicity';
    protected $fillable = ['outreach_report_id', 'channel', 'details', 'estimated_reach', 'cost', 'sort_order'];
}
