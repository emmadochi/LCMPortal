<?php
use App\Core\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\CSRFMiddleware;
use App\Middleware\HeadPastorMiddleware;

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
}, [AuthMiddleware::class, HeadPastorMiddleware::class]);

// ── Admin Reporting Routes (admin-only) ───────────────────────────────────────
$router->group('/admin', function($router) {
    // Financial Report
    $router->get('/finance-report',      'AdminReportController@financeReport');
    $router->get('/finance-report/data', 'AdminReportController@financeReportData');

    // Attendance Overview
    $router->get('/attendance-overview',      'AdminReportController@attendanceOverview');
    $router->get('/attendance-overview/data', 'AdminReportController@attendanceOverviewData');
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

// Unit Head Workspace Routes
$router->group('/my-unit', function($router) {
    $router->get('/dashboard', 'UnitHeadController@dashboard');
    $router->get('/members', 'UnitHeadController@members');
    $router->post('/members/assign', 'UnitHeadController@assignMember');
    $router->post('/members/remove', 'UnitHeadController@removeMember');
    $router->get('/attendance', 'UnitHeadController@attendance');
    $router->post('/attendance', 'UnitHeadController@markAttendance');
    $router->get('/reports', 'UnitHeadController@reports');
    $router->post('/reports/store', 'UnitHeadController@storeReport');
    $router->get('/finance', 'UnitHeadController@finance');
    $router->post('/finance/store', 'UnitHeadController@storeFinance');
}, [AuthMiddleware::class]);

// Standard Member Unit Routes
$router->group('/my-units', function($router) {
    $router->get('', 'UnitController@myUnits');
    $router->post('/announcements/{id}/acknowledge', 'UnitController@acknowledgeAnnouncement');
}, [AuthMiddleware::class]);

// Member Directory Routes (admins, directors, head pastors)
$router->group('/members', function($router) {
    $router->get('', 'MemberDirectoryController@index');
    $router->get('/create', 'MemberDirectoryController@create');
    $router->post('', 'MemberDirectoryController@store');
    $router->get('/export', 'MemberDirectoryController@export'); // before {id}
    $router->get('/{id}', 'MemberDirectoryController@show');
    $router->get('/{id}/edit', 'MemberDirectoryController@edit');
    $router->put('/{id}', 'MemberDirectoryController@update');
}, [AuthMiddleware::class]);

// Evangelism Reporting Routes
$router->group('/evangelism', function($router) {
    $router->get('', 'EvangelismController@index');
    $router->get('/leaderboard', 'EvangelismController@leaderboard');
    $router->get('/leaderboard/export', 'EvangelismController@exportLeaderboard');
    $router->get('/create', 'EvangelismController@create');
    $router->post('', 'EvangelismController@store');
    $router->get('/export', 'EvangelismController@export');
    $router->get('/{id}', 'EvangelismController@show');
    $router->get('/{id}/edit', 'EvangelismController@edit');
    $router->put('/{id}', 'EvangelismController@update');
    $router->post('/{id}/delete', 'EvangelismController@delete');
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

// Personal Profile & Personal Data Routes
$router->group('/profile', function($router) {
    $router->get('', 'MyProfileController@index');
    $router->get('/edit', 'MyProfileController@edit');
    $router->post('/update', 'MyProfileController@update');
    $router->post('/update-details', 'MyProfileController@updateDetails');
    $router->post('/update-picture', 'MyProfileController@updateProfilePicture');
}, [AuthMiddleware::class]);

// Outreach / Event Reports (program outcome reporting: publicity, logistics, cost, attendance, challenges, targets)
$router->group('/outreach-reports', function($router) {
    // Church ID-based routing for Head Pastors
    $router->get('/{church_id}', 'OutreachReportController@index', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/create', 'OutreachReportController@create', [HeadPastorMiddleware::class]);
    $router->post('/{church_id}', 'OutreachReportController@store', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/export', 'OutreachReportController@export', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/{id}', 'OutreachReportController@show', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/{id}/edit', 'OutreachReportController@edit', [HeadPastorMiddleware::class]);
    $router->put('/{church_id}/{id}', 'OutreachReportController@update', [HeadPastorMiddleware::class]);
    $router->post('/{church_id}/{id}/delete', 'OutreachReportController@delete', [HeadPastorMiddleware::class]);

    // Global Admin/Legacy routes
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
    // Handle /reports/create FIRST (before dynamic routes to avoid routing conflicts)
    $router->get('/create', function() {
        $request = new \App\Core\Request();
        $session = \App\Core\Session::getInstance();
        
        // Head pastors should use church-scoped routes via head-pastor menu
        if ($session->isHeadPastor()) {
            $headPastorChurchId = $session->getHeadPastorChurchId();
            header('Location: ' . $request->basePath() . "/reports/{$headPastorChurchId}/create");
            exit;
        }
        
        $churchId = $session->get('church_id');
        if ($churchId) {
            header('Location: ' . $request->basePath() . "/reports/{$churchId}/create");
            exit;
        }
        
        $session->setFlash('error', 'Please select a church first.');
        header('Location: ' . $request->basePath() . '/');
        exit;
    }, [AuthMiddleware::class]);
    
    // Church ID-based routing (after specific routes to avoid conflicts)
    $router->get('/{church_id}', 'ReportController@index', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/create', 'ReportController@create', [HeadPastorMiddleware::class]);
    $router->post('/{church_id}', 'ReportController@store', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/export', 'ReportController@export', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/{id}', 'ReportController@show', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/{id}/edit', 'ReportController@edit', [HeadPastorMiddleware::class]);
    $router->put('/{church_id}/{id}', 'ReportController@update', [HeadPastorMiddleware::class]);
    $router->post('/{church_id}/{id}/delete', 'ReportController@delete', [HeadPastorMiddleware::class]);
    
    // Backward compatibility routes
    $router->get('', function() {
        $request = new \App\Core\Request();
        $session = \App\Core\Session::getInstance();
        
        // Head pastors should use church-scoped routes via head-pastor menu
        if ($session->isHeadPastor()) {
            $headPastorChurchId = $session->getHeadPastorChurchId();
            header('Location: ' . $request->basePath() . "/reports/{$headPastorChurchId}");
            exit;
        }
        
        $churchId = $session->get('church_id');
        if ($churchId) {
            header('Location: ' . $request->basePath() . "/reports/{$churchId}");
            exit;
        }
        
        $session->setFlash('error', 'Please select a church first.');
        header('Location: ' . $request->basePath() . '/');
        exit;
    });
}, [AuthMiddleware::class]);

// Attendance Routes
$router->group('/attendance', function($router) {
    $router->get('/my-history', 'AttendanceController@myHistory');
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
    // Handle /finance/create FIRST (before dynamic routes to avoid routing conflicts)
    $router->get('/create', function() {
        $request = new \App\Core\Request();
        $session = \App\Core\Session::getInstance();
        
        // For others, try to get church_id from session
        $churchId = $session->get('church_id');
        if ($churchId) {
            header('Location: ' . $request->basePath() . "/churches/{$churchId}/finance/create");
            exit;
        }
        
        // No church_id found - redirect to dashboard with error
        $session->setFlash('error', 'Please select a church first or use the Head Pastor finance menu.');
        header('Location: ' . $request->basePath() . '/');
        exit;
    }, [AuthMiddleware::class]);
    
    // Cashflow & Audit Trail Routes
    $router->get('/cashflow', 'FinanceController@cashflow');
    $router->get('/audit-trail', 'FinanceController@auditTrail');
    $router->get('/{church_id}/cashflow', 'FinanceController@cashflow', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/audit-trail', 'FinanceController@auditTrail', [HeadPastorMiddleware::class]);
    
    // Church ID-based routing (after specific routes to avoid conflicts)
    $router->get('/{church_id}', 'FinanceController@index', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/create', 'FinanceController@create', [HeadPastorMiddleware::class]);
    $router->post('/{church_id}', 'FinanceController@store', [HeadPastorMiddleware::class]);
    
    // Export routes with format support (CSV, Excel, PDF)
    $router->get('/{church_id}/export/{format}', 'FinanceController@export', [HeadPastorMiddleware::class]);
    $router->get('/export/{format}', 'FinanceController@export');
    
    $router->get('/{church_id}/{id}', 'FinanceController@show', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/{id}/edit', 'FinanceController@edit', [HeadPastorMiddleware::class]);
    $router->put('/{church_id}/{id}', 'FinanceController@update', [HeadPastorMiddleware::class]);
    $router->post('/{church_id}/{id}/delete', 'FinanceController@delete', [HeadPastorMiddleware::class]);
    
    // Handle /finance - Smart routing based on church_id parameter
    $router->get('', function() {
        $request = new \App\Core\Request();
        $session = \App\Core\Session::getInstance();
        
        // Check if church_id is provided as query parameter
        $churchIdParam = $request->get('church_id');
        
        // Admins can view all churches OR filter by specific church
        if ($session->get('user_role') === 'admin') {
            require_once __DIR__ . '/../app/controllers/FinanceController.php';
            $controller = new \App\Controllers\FinanceController();
            
            // Route based on parameter presence
            return $controller->index($churchIdParam ?: null);
        }
        
        // Users with session church_id
        $churchId = $session->get('church_id');
        if ($churchId) {
            header('Location: ' . $request->basePath() . "/churches/{$churchId}/finance");
            exit;
        }
        
        // No church_id - redirect with error
        $session->setFlash('error', 'Please select a church first.');
        header('Location: ' . $request->basePath() . '/');
        exit;
    });
}, [AuthMiddleware::class]);

// Budget Routes
$router->group('/budgets', function($router) {
    $router->get('', 'BudgetController@index');
    $router->get('/create', 'BudgetController@create');
    $router->post('', 'BudgetController@store');
    $router->get('/export', 'BudgetController@export');
    $router->get('/{id}/edit', 'BudgetController@edit');
    $router->post('/{id}/update', 'BudgetController@update');
    $router->post('/{id}/delete', 'BudgetController@delete');
}, [AuthMiddleware::class]);

// Pledge Routes
$router->group('/pledges', function($router) {
    $router->get('', 'PledgeController@index');
    $router->get('/create', 'PledgeController@create');
    $router->post('', 'PledgeController@store');
    $router->get('/export', 'PledgeController@export');
    $router->get('/receipt/{payment_id}', 'PledgeController@receipt');
    $router->get('/{id}', 'PledgeController@show');
    $router->post('/{id}/payment', 'PledgeController@recordPayment');
}, [AuthMiddleware::class]);

// Giving / personal finance history for standard members
$router->group('/giving', function($router) {
    $router->get('/my-records', 'FinanceController@myRecords');
    $router->get('/my-records/export/{format}', 'FinanceController@exportMyRecords');
    $router->get('/my-pledges', 'PledgeController@myPledges');
}, [AuthMiddleware::class]);


// Media Routes
$router->group('/media', function($router) {
    // Handle /media/create FIRST (before dynamic routes to avoid routing conflicts)
    $router->get('/create', function() {
        $request = new \App\Core\Request();
        $session = \App\Core\Session::getInstance();
        
        if ($session->isHeadPastor()) {
            $headPastorChurchId = $session->getHeadPastorChurchId();
            header('Location: ' . $request->basePath() . "/media/{$headPastorChurchId}/create");
            exit;
        }
        
        $churchId = $session->get('church_id');
        if ($churchId) {
            header('Location: ' . $request->basePath() . "/media/{$churchId}/create");
            exit;
        }
        
        $session->setFlash('error', 'Please select a church first.');
        header('Location: ' . $request->basePath() . '/');
        exit;
    }, [AuthMiddleware::class]);
    
    // Church ID-based routing (after specific routes to avoid conflicts)
    $router->get('/{church_id}', 'MediaController@index', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/create', 'MediaController@create', [HeadPastorMiddleware::class]);
    $router->post('/{church_id}', 'MediaController@store', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/export', 'MediaController@export', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/{id}', 'MediaController@show', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/{id}/edit', 'MediaController@edit', [HeadPastorMiddleware::class]);
    $router->put('/{church_id}/{id}', 'MediaController@update', [HeadPastorMiddleware::class]);
    $router->post('/{church_id}/{id}/delete', 'MediaController@delete', [HeadPastorMiddleware::class]);
    
    // Backward compatibility routes
    $router->get('', function() {
        $request = new \App\Core\Request();
        $session = \App\Core\Session::getInstance();
        
        if ($session->isHeadPastor()) {
            $headPastorChurchId = $session->getHeadPastorChurchId();
            header('Location: ' . $request->basePath() . "/media/{$headPastorChurchId}");
            exit;
        }
        
        $churchId = $session->get('church_id');
        if ($churchId) {
            header('Location: ' . $request->basePath() . "/media/{$churchId}");
            exit;
        }
        
        $session->setFlash('error', 'Please select a church first.');
        header('Location: ' . $request->basePath() . '/');
        exit;
    });
}, [AuthMiddleware::class]);

// Project Routes
$router->group('/projects', function($router) {
    // Handle /projects/create FIRST (before dynamic routes to avoid routing conflicts)
    $router->get('/create', function() {
        $request = new \App\Core\Request();
        $session = \App\Core\Session::getInstance();
        
        if ($session->isHeadPastor()) {
            $headPastorChurchId = $session->getHeadPastorChurchId();
            header('Location: ' . $request->basePath() . "/projects/{$headPastorChurchId}/create");
            exit;
        }
        
        $churchId = $session->get('church_id');
        if ($churchId) {
            header('Location: ' . $request->basePath() . "/projects/{$churchId}/create");
            exit;
        }
        
        $session->setFlash('error', 'Please select a church first.');
        header('Location: ' . $request->basePath() . '/');
        exit;
    }, [AuthMiddleware::class]);
    
    // Church ID-based routing (after specific routes to avoid conflicts)
    $router->get('/{church_id}', 'ProjectController@index', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/create', 'ProjectController@create', [HeadPastorMiddleware::class]);
    $router->post('/{church_id}', 'ProjectController@store', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/export', 'ProjectController@export', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/{id}', 'ProjectController@show', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/{id}/edit', 'ProjectController@edit', [HeadPastorMiddleware::class]);
    $router->put('/{church_id}/{id}', 'ProjectController@update', [HeadPastorMiddleware::class]);
    $router->post('/{church_id}/{id}/delete', 'ProjectController@delete', [HeadPastorMiddleware::class]);
    
    // Backward compatibility routes
    $router->get('', function() {
        $request = new \App\Core\Request();
        $session = \App\Core\Session::getInstance();
        
        if ($session->isHeadPastor()) {
            $headPastorChurchId = $session->getHeadPastorChurchId();
            header('Location: ' . $request->basePath() . "/projects/{$headPastorChurchId}");
            exit;
        }
        
        $churchId = $session->get('church_id');
        if ($churchId) {
            header('Location: ' . $request->basePath() . "/projects/{$churchId}");
            exit;
        }
        
        $session->setFlash('error', 'Please select a church first.');
        header('Location: ' . $request->basePath() . '/');
        exit;
    });
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

// Church Management Routes - Universal church-scoped routes
// Middleware handles role-based access control per route
$router->group('/churches', function($router) {
    // Admin-only routes
    $router->get('', 'ChurchController@index');
    $router->get('/create', 'ChurchController@create');
    $router->post('', 'ChurchController@store');
    $router->post('/{id}/assign-head-pastor', 'ChurchController@assignHeadPastor');
    $router->post('/{id}/remove-head-pastor', 'ChurchController@removeHeadPastor');
    
    // Church-scoped routes (accessible by authorized users for their church)
    $router->get('/{id}/membership', 'ChurchController@membershipDashboard', [HeadPastorMiddleware::class]);
    $router->get('/{id}', 'ChurchController@show', [HeadPastorMiddleware::class]);
    $router->get('/{id}/edit', 'ChurchController@edit', [HeadPastorMiddleware::class]);
    $router->put('/{id}', 'ChurchController@update', [HeadPastorMiddleware::class]);
    $router->delete('/{id}', 'ChurchController@delete', [HeadPastorMiddleware::class]);
    $router->post('/{id}/assign-unit-head', 'ChurchController@assignUnitHead', [HeadPastorMiddleware::class]);
    $router->post('/{id}/remove-unit-head', 'ChurchController@removeUnitHead', [HeadPastorMiddleware::class]);
    $router->post('/{id}/assign-unit', 'ChurchController@assignUnit', [HeadPastorMiddleware::class]);
    $router->post('/{id}/remove-unit', 'ChurchController@removeUnit', [HeadPastorMiddleware::class]);

    // Head Pastor specific routes
    $router->group('/{id}/finance', function($router) {
        $router->get('', 'HeadPastorFinanceController@index');
        $router->get('/records', 'HeadPastorFinanceController@records');
        $router->get('/create', 'HeadPastorFinanceController@create');
        $router->post('', 'HeadPastorFinanceController@store');
        $router->get('/export', 'HeadPastorFinanceController@export');
        $router->get('/report', 'HeadPastorFinanceController@report');
        $router->get('/cashflow', 'HeadPastorFinanceController@cashflow');
        $router->get('/audit-trail', 'HeadPastorFinanceController@auditTrail');
        $router->get('/{transaction_id}', 'HeadPastorFinanceController@show');
    }, [HeadPastorMiddleware::class]);

    $router->group('/{id}/budgets', function($router) {
        $router->get('', 'BudgetController@index');
        $router->get('/create', 'BudgetController@create');
        $router->post('', 'BudgetController@store');
    }, [HeadPastorMiddleware::class]);

    $router->group('/{id}/pledges', function($router) {
        $router->get('', 'PledgeController@index');
        $router->get('/create', 'PledgeController@create');
        $router->post('', 'PledgeController@store');
    }, [HeadPastorMiddleware::class]);


    $router->group('/{id}/property', function($router) {
        $router->get('', 'HeadPastorPropertyController@index');
        $router->get('/records', 'HeadPastorPropertyController@records');
        $router->get('/create', 'HeadPastorPropertyController@create');
        $router->post('', 'HeadPastorPropertyController@store');
        $router->get('/export', 'HeadPastorPropertyController@export');
        $router->get('/{property_id}', 'HeadPastorPropertyController@show');
        $router->get('/{property_id}/edit', 'HeadPastorPropertyController@edit');
        $router->put('/{property_id}', 'HeadPastorPropertyController@update');
        $router->post('/{property_id}/status', 'HeadPastorPropertyController@updateStatus');
        $router->post('/{property_id}/assign', 'HeadPastorPropertyController@assign');
    }, [HeadPastorMiddleware::class]);

    $router->group('/{id}/attendance', function($router) {
        $router->get('', 'HeadPastorAttendanceController@index');
        $router->get('/records', 'HeadPastorAttendanceController@records');
        $router->get('/mark', 'HeadPastorAttendanceController@mark');
        $router->post('/mark', 'HeadPastorAttendanceController@storeMark');
        $router->get('/chart-data', 'HeadPastorAttendanceController@chartData');
        $router->get('/report', 'HeadPastorAttendanceController@report');
        $router->get('/service', 'HeadPastorAttendanceController@showService');
        $router->get('/export', 'HeadPastorAttendanceController@export');
    }, [HeadPastorMiddleware::class]);

    $router->group('/{id}/projects', function($router) {
        $router->get('', 'HeadPastorProjectController@index');
        $router->get('/records', 'HeadPastorProjectController@records');
        $router->get('/create', 'HeadPastorProjectController@create');
        $router->post('', 'HeadPastorProjectController@store');
        $router->get('/export', 'HeadPastorProjectController@export');
        $router->get('/{project_id}', 'HeadPastorProjectController@show');
        $router->get('/{project_id}/edit', 'HeadPastorProjectController@edit');
        $router->put('/{project_id}', 'HeadPastorProjectController@update');
        $router->post('/{project_id}/delete', 'HeadPastorProjectController@delete');
    }, [HeadPastorMiddleware::class]);

    // Performance Dashboard
    $router->group('/{id}/performance', function($router) {
        $router->get('', 'HeadPastorUnitPerformanceController@index');
        $router->get('/{unit_id}', 'HeadPastorUnitPerformanceController@show');
    }, [HeadPastorMiddleware::class]);

    // Unit Reports
    $router->group('/{id}/unit-reports', function($router) {
        $router->get('', 'HeadPastorReportController@index');
        $router->get('/{report_id}', 'HeadPastorReportController@show');
    }, [HeadPastorMiddleware::class]);

    $router->group('/{id}/outreach', function($router) {
        $router->get('', 'HeadPastorOutreachController@index');
        $router->get('/records', 'HeadPastorOutreachController@records');
        $router->get('/create', 'HeadPastorOutreachController@create');
        $router->post('', 'HeadPastorOutreachController@store');
        $router->get('/export', 'HeadPastorOutreachController@export');
        $router->get('/{report_id}', 'HeadPastorOutreachController@show');
        $router->get('/{report_id}/edit', 'HeadPastorOutreachController@edit');
        $router->put('/{report_id}', 'HeadPastorOutreachController@update');
        $router->delete('/{report_id}', 'HeadPastorOutreachController@delete');
        $router->get('/{report_id}/images/{image_id}/delete', 'HeadPastorOutreachController@deleteImage');
    }, [HeadPastorMiddleware::class]);
});

// Activity Logs Routes (Admin only)
$router->group('/activity-logs', function($router) {
    $router->get('', 'ActivityLogController@index');
    $router->get('/export', 'ActivityLogController@export');
}, [AuthMiddleware::class]);

// Notification Routes
$router->group('/notifications', function($router) {
    // Handle /notifications/create FIRST (before dynamic routes to avoid routing conflicts)
    $router->get('/create', function() {
        $request = new \App\Core\Request();
        $session = \App\Core\Session::getInstance();
        
        if ($session->isHeadPastor()) {
            $headPastorChurchId = $session->getHeadPastorChurchId();
            header('Location: ' . $request->basePath() . "/notifications/{$headPastorChurchId}/create");
            exit;
        }
        
        $churchId = $session->get('church_id');
        if ($churchId) {
            header('Location: ' . $request->basePath() . "/notifications/{$churchId}/create");
            exit;
        }
        
        $session->setFlash('error', 'Please select a church first.');
        header('Location: ' . $request->basePath() . '/');
        exit;
    }, [AuthMiddleware::class]);
    
    // Church ID-based routing (after specific routes to avoid conflicts)
    $router->get('/{church_id}', 'NotificationController@index', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/create', 'NotificationController@create', [HeadPastorMiddleware::class]);
    $router->post('/{church_id}', 'NotificationController@store', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/export', 'NotificationController@export', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/{id}', 'NotificationController@show', [HeadPastorMiddleware::class]);
    $router->get('/{church_id}/{id}/edit', 'NotificationController@edit', [HeadPastorMiddleware::class]);
    $router->put('/{church_id}/{id}', 'NotificationController@update', [HeadPastorMiddleware::class]);
    $router->post('/{church_id}/{id}/delete', 'NotificationController@delete', [HeadPastorMiddleware::class]);
    $router->post('/{church_id}/{id}/read', 'NotificationController@markAsRead', [HeadPastorMiddleware::class]);
    $router->post('/{church_id}/read-all', 'NotificationController@markAllAsRead', [HeadPastorMiddleware::class]);
    
    // Backward compatibility routes
    $router->get('', function() {
        $request = new \App\Core\Request();
        $session = \App\Core\Session::getInstance();
        
        if ($session->isHeadPastor()) {
            $headPastorChurchId = $session->getHeadPastorChurchId();
            header('Location: ' . $request->basePath() . "/notifications/{$headPastorChurchId}");
            exit;
        }
        
        $churchId = $session->get('church_id');
        if ($churchId) {
            header('Location: ' . $request->basePath() . "/notifications/{$churchId}");
            exit;
        }
        
        $session->setFlash('error', 'Please select a church first.');
        header('Location: ' . $request->basePath() . '/');
        exit;
    });
    $router->get('/show', 'NotificationController@show');
    $router->get('/api', 'NotificationController@apiList');
    $router->post('/send', 'NotificationController@send');
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
}, [AuthMiddleware::class, new \App\Middleware\RoleMiddleware('admin')]);

// Unauthorized page
$router->get('/unauthorized', function() {
    http_response_code(403);
    echo "Unauthorized access";
    exit;
});
