<?php
namespace App\Models;

class OutreachChallenge extends BaseModel {
    protected $table = 'outreach_challenges';
    protected $fillable = ['outreach_report_id', 'description', 'category', 'severity', 'sort_order'];
}
