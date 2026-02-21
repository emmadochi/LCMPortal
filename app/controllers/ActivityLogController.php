<?php
namespace App\Controllers;

use App\Models\ActivityLog;
use App\Utilities\ExportHelper;

class ActivityLogController extends BaseController {
    private $activityLogModel;

    public function __construct() {
        parent::__construct();
        $this->activityLogModel = new ActivityLog();
        
        // Only admins can view activity logs
        $this->authorize('manage_users'); // Using manage_users as proxy for admin access
    }

    /**
     * List all activity logs (paginated)
     */
    public function index() {
        $search = $this->request->get('search', '');
        $action = $this->request->get('action', '');
        $modelType = $this->request->get('model_type', '');
        $userId = $this->request->get('user_id', '');
        $page = max(1, (int)$this->request->get('page', 1));
        $perPage = 25;
        
        // Build conditions
        $conditions = [];
        if ($action) {
            $conditions['action'] = $action;
        }
        if ($modelType) {
            $conditions['model_type'] = $modelType;
        }
        if ($userId) {
            $conditions['user_id'] = (int)$userId;
        }
        
        $searchParam = $search !== '' ? $search : null;
        $total = $this->activityLogModel->countWithFilters($conditions, $searchParam);
        $totalPages = $total > 0 ? (int)ceil($total / $perPage) : 1;
        $page = min($page, max(1, $totalPages));
        $offset = ($page - 1) * $perPage;
        
        $logs = $this->activityLogModel->getLogsWithDetails(
            $conditions,
            'al.created_at DESC',
            $perPage,
            $offset,
            $searchParam
        );
        
        // Get unique actions and model types for filter dropdowns
        $allLogs = $this->activityLogModel->getLogsWithDetails([], 'al.created_at DESC', 1000, 0, null);
        $actions = array_unique(array_column($allLogs, 'action'));
        $modelTypes = array_unique(array_filter(array_column($allLogs, 'model_type')));
        
        $pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total' => $total,
            'per_page' => $perPage,
            'query_params' => array_filter([
                'search' => $search,
                'action' => $action,
                'model_type' => $modelType,
                'user_id' => $userId ? (string)$userId : ''
            ])
        ];
        
        $this->render('activity-logs/index', [
            'title' => 'Activity Logs',
            'pageTitle' => 'Activity Logs',
            'logs' => $logs,
            'search' => $search,
            'action' => $action,
            'modelType' => $modelType,
            'userId' => $userId,
            'actions' => $actions,
            'modelTypes' => $modelTypes,
            'pagination' => $pagination
        ]);
    }

    /**
     * Export activity logs
     */
    public function export() {
        $format = $this->request->get('format', 'csv');
        $conditions = [];
        
        $action = $this->request->get('action', '');
        $modelType = $this->request->get('model_type', '');
        $userId = $this->request->get('user_id', '');
        
        if ($action) {
            $conditions['action'] = $action;
        }
        if ($modelType) {
            $conditions['model_type'] = $modelType;
        }
        if ($userId) {
            $conditions['user_id'] = (int)$userId;
        }
        
        $logs = $this->activityLogModel->getLogsWithDetails($conditions, 'created_at DESC', 10000);
        
        $headers = ['ID', 'User', 'Email', 'Action', 'Model Type', 'Model ID', 'Description', 'IP Address', 'Date'];
        $data = [];
        
        foreach ($logs as $log) {
            $data[] = [
                'id' => $log['id'],
                'user' => ($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? ''),
                'email' => $log['email'] ?? '',
                'action' => $log['action'],
                'model_type' => $log['model_type'] ?? '',
                'model_id' => $log['model_id'] ?? '',
                'description' => $log['description'] ?? '',
                'ip_address' => $log['ip_address'] ?? '',
                'date' => $log['created_at'] ?? ''
            ];
        }
        
        $filename = 'activity_logs_' . date('Y-m-d_His') . '.' . $format;
        
        if ($format === 'json') {
            ExportHelper::exportJSON($data, $filename);
        } else {
            ExportHelper::exportCSV($data, $headers, $filename);
        }
    }
}

