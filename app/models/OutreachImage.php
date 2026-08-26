<?php
namespace App\Models;

class OutreachImage extends BaseModel {
    protected $table = 'outreach_images';
    protected $fillable = [
        'outreach_report_id',
        'file_path',
        'file_name',
        'caption',
        'file_size'
    ];

    /**
     * Get all images for a specific report
     */
    public function getByReportId($reportId) {
        return $this->findAll(['outreach_report_id' => $reportId], 'created_at ASC');
    }
}
