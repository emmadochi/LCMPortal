<?php
namespace App\Models;

class EvangelismReport extends BaseModel {
    protected $table = 'evangelism_reports';

    public function getReportsByUserId($userId) {
        return $this->findAll(['user_id' => $userId], 'report_date DESC');
    }
}
