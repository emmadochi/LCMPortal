<?php
namespace App\Models;

class ReportFile extends BaseModel {
    protected $table = 'report_files';
    protected $fillable = ['report_id', 'file_name', 'file_path', 'file_type', 'file_size'];
}

