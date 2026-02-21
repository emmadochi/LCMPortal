<?php
use App\Core\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\CSRFMiddleware;

// Router instance is passed from App::run()

// Public routes
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->get('/forgot-password', 'AuthController@showForgotPassword');
$router->post('/forgot-password', 'AuthController@requestPasswordReset');
$router->get('/reset-password/{token}', 'AuthController@showResetPassword');
$router->post('/reset-password/{token}', 'AuthController@resetPassword');

// Protected routes
$router->get('/', 'DashboardController@index', [AuthMiddleware::class]);

// Redirect to login if not authenticated
$router->get('/dashboard', function() {
    $request = new \App\Core\Request();
    $base = $request->basePath();
    header('Location: ' . $base . '/');
    exit;
}, [AuthMiddleware::class]);

// Unit Management Routes
$router->group('/units', function($router) {
    $router->get('', 'UnitController@index');
    $router->get('/create', 'UnitController@create');
    $router->post('', 'UnitController@store');
    $router->get('/export', 'UnitController@export');
    $router->get('/{id}', 'UnitController@show');
    $router->get('/{id}/edit', 'UnitController@edit');
    $router->put('/{id}', 'UnitController@update');
    $router->delete('/{id}', 'UnitController@delete');
    
    // AJAX endpoints for assignments
    $router->post('/assign-member', 'UnitController@assignMember');
    $router->post('/remove-member', 'UnitController@removeMember');
    $router->post('/assign-director', 'UnitController@assignDirector');
    $router->post('/remove-director', 'UnitController@removeDirector');
}, [AuthMiddleware::class]);

// Member Directory Routes (admins, directors, head pastors)
$router->group('/members', function($router) {
    $router->get('', 'MemberDirectoryController@index');
    $router->get('/export', 'MemberDirectoryController@export'); // before {id}
    $router->get('/{id}', 'MemberDirectoryController@show');
}, [AuthMiddleware::class]);

// Follow-up Management Routes
$router->group('/follow-ups', function($router) {
    $router->get('', 'FollowUpController@index');
    $router->get('/create', 'FollowUpController@create');
    $router->post('', 'FollowUpController@store');
    $router->get('/statistics', 'FollowUpController@statistics');
    $router->get('/table', 'FollowUpController@tableFragment');
    $router->post('/api-create', 'FollowUpController@apiCreate');
    $router->get('/{id}', 'FollowUpController@show');
    $router->get('/{id}/edit', 'FollowUpController@edit');
    $router->put('/{id}', 'FollowUpController@update');
    $router->post('/{id}/complete', 'FollowUpController@complete');
    $router->post('/{id}/delete', 'FollowUpController@delete');
}, [AuthMiddleware::class]);

// User Management Routes
$router->group('/users', function($router) {
    $router->get('', 'UserController@index');
    $router->get('/create', 'UserController@create');
    $router->post('', 'UserController@store');
    $router->get('/export', 'UserController@export');
    $router->get('/{id}/finance-records', 'UserController@financeRecords');
    $router->post('/{id}/profile-picture', 'UserController@updateProfilePicture');
    $router->get('/{id}', 'UserController@show');
    $router->get('/{id}/edit', 'UserController@edit');
    $router->put('/{id}', 'UserController@update');
    $router->delete('/{id}', 'UserController@delete');
    
    // AJAX endpoints for unit assignments
    $router->post('/assign-unit', 'UserController@assignUnit');
    $router->post('/remove-unit', 'UserController@removeUnit');
    $router->post('/assign-director-unit', 'UserController@assignDirectorUnit');
    $router->post('/remove-director-unit', 'UserController@removeDirectorUnit');
}, [AuthMiddleware::class]);

// Outreach / Event Reports (program outcome reporting: publicity, logistics, cost, attendance, challenges, targets)
$router->group('/outreach-reports', function($router) {
    $router->get('', 'OutreachReportController@index');
    $router->get('/create', 'OutreachReportController@create');
    $router->post('', 'OutreachReportController@store');
    $router->get('/export', 'OutreachReportController@export');
    $router->get('/{id}', 'OutreachReportController@show');
    $router->get('/{id}/edit', 'OutreachReportController@edit');
    $router->put('/{id}', 'OutreachReportController@update');
    $router->post('/{id}/delete', 'OutreachReportController@delete');
}, [AuthMiddleware::class]);

// Report Management Routes
$router->group('/reports', function($router) {
    $router->get('', 'ReportController@index');
    $router->get('/create', 'ReportController@create');
    $router->post('', 'ReportController@store');
    $router->get('/export', 'ReportController@export');
    $router->get('/{id}', 'ReportController@show');
}, [AuthMiddleware::class]);

// Attendance Routes
$router->group('/attendance', function($router) {
    $router->get('', 'AttendanceController@index');
    $router->get('/chart-data', 'AttendanceController@chartData');
    $router->get('/mark', 'AttendanceController@mark');
    $router->post('/mark', 'AttendanceController@markStore');
    $router->get('/service', 'AttendanceController@showService');
    $router->get('/create', 'AttendanceController@create');
    $router->post('', 'AttendanceController@store');
    $router->get('/export', 'AttendanceController@export');
    $router->get('/{id}', 'AttendanceController@show');
}, [AuthMiddleware::class]);

// Finance Routes
$router->group('/finance', function($router) {
    $router->get('', 'FinanceController@index');
    $router->get('/create', 'FinanceController@create');
    $router->post('', 'FinanceController@store');
    $router->get('/export', 'FinanceController@export');
    $router->get('/{id}', 'FinanceController@show');
}, [AuthMiddleware::class]);

// Media Routes
$router->group('/media', function($router) {
    $router->get('', 'MediaController@index');
    $router->get('/create', 'MediaController@create');
    $router->post('', 'MediaController@store');
    $router->get('/{id}', 'MediaController@show');
}, [AuthMiddleware::class]);

// Project Routes
$router->group('/projects', function($router) {
    $router->get('', 'ProjectController@index');
    $router->get('/create', 'ProjectController@create');
    $router->post('', 'ProjectController@store');
    $router->get('/export', 'ProjectController@export');
    $router->get('/{id}', 'ProjectController@show');
}, [AuthMiddleware::class]);

// Church & Unit Targets (Admin only)
$router->group('/targets', function($router) {
    $router->get('', 'ChurchUnitTargetController@index');
    $router->get('/create', 'ChurchUnitTargetController@create');
    $router->post('', 'ChurchUnitTargetController@store');
    $router->get('/{id}/edit', 'ChurchUnitTargetController@edit');
    $router->put('/{id}', 'ChurchUnitTargetController@update');
    $router->delete('/{id}', 'ChurchUnitTargetController@delete');
}, [AuthMiddleware::class]);

// Church Management Routes (Admin only)
$router->group('/churches', function($router) {
    $router->get('', 'ChurchController@index');
    $router->get('/create', 'ChurchController@create');
    $router->post('', 'ChurchController@store');
    $router->get('/{id}/membership', 'ChurchController@membershipDashboard');
    $router->get('/{id}', 'ChurchController@show');
    $router->get('/{id}/edit', 'ChurchController@edit');
    $router->put('/{id}', 'ChurchController@update');
    $router->delete('/{id}', 'ChurchController@delete');
    $router->get('/{id}/report', 'ChurchController@generateReport');
    $router->post('/{id}/assign-unit', 'ChurchController@assignUnit');
    $router->post('/{id}/remove-unit', 'ChurchController@removeUnit');
    $router->post('/{id}/assign-head-pastor', 'ChurchController@assignHeadPastor');
    $router->post('/{id}/remove-head-pastor', 'ChurchController@removeHeadPastor');
}, [AuthMiddleware::class]);

// Activity Logs Routes (Admin only)
$router->group('/activity-logs', function($router) {
    $router->get('', 'ActivityLogController@index');
    $router->get('/export', 'ActivityLogController@export');
}, [AuthMiddleware::class]);

// Notification Routes (read-all must be before {id}/read to avoid "read-all" being captured as id)
$router->group('/notifications', function($router) {
    $router->get('', 'NotificationController@index');
    $router->get('/show', 'NotificationController@show');
    $router->get('/create', 'NotificationController@create');
    $router->post('/send', 'NotificationController@send');
    $router->post('/read-all', 'NotificationController@markAllAsRead');
    $router->post('/{id}/read', 'NotificationController@markAsRead');
}, [AuthMiddleware::class]);

// Property Category Routes
$router->group('/property-categories', function($router) {
    $router->get('', 'PropertyCategoryController@index');
    $router->get('/create', 'PropertyCategoryController@create');
    $router->post('', 'PropertyCategoryController@store');
    $router->get('/{id}/edit', 'PropertyCategoryController@edit');
    $router->put('/{id}', 'PropertyCategoryController@update');
    $router->delete('/{id}', 'PropertyCategoryController@delete');
}, [AuthMiddleware::class]);

// Property Routes
$router->group('/properties', function($router) {
    $router->get('', 'PropertyController@index');
    $router->get('/create', 'PropertyController@create');
    $router->post('', 'PropertyController@store');
    $router->get('/{id}', 'PropertyController@show');
    $router->get('/{id}/edit', 'PropertyController@edit');
    $router->put('/{id}', 'PropertyController@update');
    $router->post('/{id}/status', 'PropertyController@updateStatus');
    $router->post('/{id}/assign', 'PropertyController@assign');
    $router->post('/{id}/transfer', 'PropertyController@transfer');
    $router->delete('/{id}', 'PropertyController@delete');
}, [AuthMiddleware::class]);

// Admin Password Reset Requests Routes
$router->group('/admin/password-reset-requests', function($router) {
    $router->get('', 'AuthController@showAdminResetRequests');
    $router->post('/{id}/approve', 'AuthController@approvePasswordReset');
    $router->post('/{id}/reject', 'AuthController@rejectPasswordReset');
}, [AuthMiddleware::class, RoleMiddleware::class]);

// Unauthorized page
$router->get('/unauthorized', function() {
    http_response_code(403);
    echo "Unauthorized access";
    exit;
});

