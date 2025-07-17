<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\WarningController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\NoticeController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Role;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Temporary test route - remove in production
Route::get('/test-db', function () {
    $users = User::all();
    $roles = Role::all();
    
    return response()->json([
        'users' => $users,
        'roles' => $roles,
        'user_count' => $users->count(),
        'role_count' => $roles->count(),
    ]);
});

// Authentication Routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    
    // Profile Routes
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    
    // HR Manager Dashboard
    Route::get('/hr-manager/dashboard', [DashboardController::class, 'hrManagerDashboard'])->name('hr-manager.dashboard');
    
    // HR Officer Dashboard
    Route::get('/hr-officer/dashboard', [DashboardController::class, 'hrOfficerDashboard'])->name('hr-officer.dashboard');
    
    // Finance Manager Dashboard
    Route::get('/finance-manager/dashboard', [DashboardController::class, 'financeManagerDashboard'])->name('finance-manager.dashboard');
    
    // Finance Officer Dashboard
    Route::get('/finance-officer/dashboard', [DashboardController::class, 'financeOfficerDashboard'])->name('finance-officer.dashboard');
    
    // Project Manager Dashboard
    Route::get('/project-manager/dashboard', [DashboardController::class, 'projectManagerDashboard'])->name('project-manager.dashboard');
    
    // Team Leader Dashboard
    Route::get('/team-leader/dashboard', [DashboardController::class, 'teamLeaderDashboard'])->name('team-leader.dashboard');
    
    // Employee Dashboard
    Route::get('/employee/dashboard', [DashboardController::class, 'employeeDashboard'])->name('employee.dashboard');
    
    // Contractor Dashboard
    Route::get('/contractor/dashboard', [DashboardController::class, 'contractorDashboard'])->name('contractor.dashboard');
    
    // Intern Dashboard
    Route::get('/intern/dashboard', [DashboardController::class, 'internDashboard'])->name('intern.dashboard');
    
    // Temporary Dashboard
    Route::get('/temporary/dashboard', [DashboardController::class, 'temporaryDashboard'])->name('temporary.dashboard');
    
    // Consultant Dashboard
    Route::get('/consultant/dashboard', [DashboardController::class, 'consultantDashboard'])->name('consultant.dashboard');
    
    // Guest Dashboard
    Route::get('/guest/dashboard', [DashboardController::class, 'guestDashboard'])->name('guest.dashboard');

    // Employee Routes
    Route::middleware(['permission:employees.view'])->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export')->middleware('permission:employees.export');
        Route::post('/employees/import', [EmployeeController::class, 'import'])->name('employees.import')->middleware('permission:employees.import');
        
        // Employee Document Routes
        Route::post('/employees/{employee}/documents', [EmployeeController::class, 'uploadDocument'])->name('employees.documents.upload')->middleware('permission:documents.create');
        Route::delete('/employees/{employee}/documents/{document}', [EmployeeController::class, 'deleteDocument'])->name('employees.documents.delete')->middleware('permission:documents.delete');
        Route::post('/employees/{employee}/documents/{document}/verify', [EmployeeController::class, 'verifyDocument'])->name('employees.documents.verify')->middleware('permission:documents.verify');
        Route::get('/employees/{employee}/documents/{document}/download', [EmployeeController::class, 'downloadDocument'])->name('employees.documents.download')->middleware('permission:documents.view');
    });

    // Leave Routes
    Route::middleware(['permission:leaves.view'])->group(function () {
        Route::resource('leaves', LeaveController::class);
        Route::post('/leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve')->middleware('permission:leaves.approve');
        Route::post('/leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject')->middleware('permission:leaves.reject');
        Route::get('/leaves/export', [LeaveController::class, 'export'])->name('leaves.export')->middleware('permission:leaves.export');
    });

    // Attendance Routes
    Route::middleware(['permission:attendance.view,attendance.check_in'])->group(function () {
        Route::resource('attendance', AttendanceController::class);
        Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in')->middleware('permission:attendance.check_in');
        Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out')->middleware('permission:attendance.check_out');
        Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report')->middleware('permission:attendance.report');
        Route::post('/attendance/bulk-import', [AttendanceController::class, 'bulkImport'])->name('attendance.bulk-import')->middleware('permission:attendance.import');
        Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export')->middleware('permission:attendance.export');
        Route::post('/attendance/{attendance}/approve', [AttendanceController::class, 'approve'])->name('attendance.approve')->middleware('permission:attendance.approve');
        Route::post('/attendance/bulk-approve', [AttendanceController::class, 'bulkApprove'])->name('attendance.bulk-approve')->middleware('permission:attendance.bulk_approve');
        Route::get('/attendance/today-summary', [AttendanceController::class, 'todaySummary'])->name('attendance.today-summary');
        Route::get('/attendance/employee/{employeeId}/status', [AttendanceController::class, 'employeeStatus'])->name('attendance.employee-status');
        Route::get('/attendance/check-in', function() {
            return view('attendance.check-in');
        })->name('attendance.check-in-page')->middleware('permission:attendance.check_in');
    });

    // Loan Routes
    Route::middleware(['permission:loans.view'])->group(function () {
        Route::resource('loans', LoanController::class);
        Route::post('/loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve')->middleware('permission:loans.approve');
        Route::post('/loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject')->middleware('permission:loans.reject');
        Route::post('/loans/{loan}/pay-installment', [LoanController::class, 'payInstallment'])->name('loans.pay-installment')->middleware('permission:loans.pay_installment');
        Route::get('/loans/export', [LoanController::class, 'export'])->name('loans.export')->middleware('permission:loans.export');
    });

    // User Management Routes
    Route::middleware(['permission:users.view'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/users/{user}/reset-password', [UserController::class, 'showResetPasswordForm'])->name('users.reset-password')->middleware('permission:users.reset_password');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password.submit')->middleware('permission:users.reset_password');
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status')->middleware('permission:users.edit');
        Route::get('/users/export', [UserController::class, 'export'])->name('users.export')->middleware('permission:users.view');
        Route::get('/users/{user}/permissions', [UserController::class, 'permissions'])->name('users.permissions');
    });

    // Role Management Routes  
    Route::middleware(['permission:roles.view'])->group(function () {
        Route::resource('roles', RoleController::class);
        Route::get('/roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions')->middleware('permission:roles.assign_permissions');
        Route::post('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update')->middleware('permission:roles.assign_permissions');
        Route::post('/roles/{role}/duplicate', [RoleController::class, 'duplicate'])->name('roles.duplicate')->middleware('permission:roles.create');
        Route::post('/roles/{role}/reset-defaults', [RoleController::class, 'resetToDefaults'])->name('roles.reset-defaults')->middleware('permission:roles.edit');
        Route::post('/roles/bulk-update-permissions', [RoleController::class, 'bulkUpdatePermissions'])->name('roles.bulk-update-permissions')->middleware('permission:roles.assign_permissions');
        Route::get('/roles/export', [RoleController::class, 'export'])->name('roles.export')->middleware('permission:roles.view');
        Route::get('/api/roles/statistics', [RoleController::class, 'statistics'])->name('roles.statistics');
        Route::get('/api/roles/permissions', [RoleController::class, 'getAllPermissions'])->name('roles.all-permissions');
    });

    // Profile Routes (override the basic ones from AuthController)
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    // Impersonation Routes (Admin only)
    Route::middleware(['role:Admin'])->group(function () {
        Route::post('/users/{user}/impersonate', [AuthController::class, 'impersonate'])->name('users.impersonate');
    });

    // Medical Records Routes
    Route::middleware(['permission:medical_records.view'])->group(function () {
        Route::resource('medical-records', MedicalRecordController::class);
        Route::get('/medical-records/{medicalRecord}/download', [MedicalRecordController::class, 'download'])->name('medical-records.download')->middleware('permission:medical_records.download');
        Route::get('/medical-records/export', [MedicalRecordController::class, 'export'])->name('medical-records.export')->middleware('permission:medical_records.export');
    });

    // Warnings Routes
    Route::middleware(['permission:warnings.view'])->group(function () {
        Route::resource('warnings', WarningController::class);
        Route::get('/warnings/export', [WarningController::class, 'export'])->name('warnings.export')->middleware('permission:warnings.export');
    });

    // Documents Routes (Standalone)
    Route::middleware(['permission:documents.view'])->group(function () {
        Route::resource('documents', DocumentController::class);
        Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download')->middleware('permission:documents.download');
        Route::post('/documents/{document}/verify', [DocumentController::class, 'verify'])->name('documents.verify')->middleware('permission:documents.verify');
        Route::get('/documents/export', [DocumentController::class, 'export'])->name('documents.export')->middleware('permission:documents.export');
    });

    // Holidays Routes
    Route::middleware(['permission:holidays.view'])->group(function () {
        Route::resource('holidays', HolidayController::class);
        Route::get('/holidays/export', [HolidayController::class, 'export'])->name('holidays.export')->middleware('permission:holidays.export');
    });

    // Notices Routes
    Route::middleware(['permission:notices.view'])->group(function () {
        Route::resource('notices', NoticeController::class);
        Route::post('/notices/{notice}/toggle-featured', [NoticeController::class, 'toggleFeatured'])->name('notices.toggle-featured')->middleware('permission:notices.edit');
        Route::get('/notices/export', [NoticeController::class, 'export'])->name('notices.export')->middleware('permission:notices.export');
    });

    // Settings Routes
    Route::middleware(['permission:settings.view'])->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update')->middleware('permission:settings.edit');
    });

    // Reports Routes
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::prefix('reports')->group(function () {
        Route::get('/employee', [ReportController::class, 'employee'])->name('reports.employee');
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
        Route::get('/leave', [ReportController::class, 'leave'])->name('reports.leave');
        Route::get('/loan', [ReportController::class, 'loan'])->name('reports.loan');
        Route::get('/payroll', [ReportController::class, 'payroll'])->name('reports.payroll');
    });
});

// Impersonation control
Route::post('/stop-impersonating', [AuthController::class, 'stopImpersonating'])->name('stop-impersonating')->middleware('auth');

// API Routes for authentication
Route::prefix('api')->group(function () {
    Route::get('/auth/check', [AuthController::class, 'checkAuth'])->name('api.auth.check');
    Route::get('/auth/me', [AuthController::class, 'me'])->name('api.auth.me')->middleware('auth');
    Route::post('/auth/change-password', [AuthController::class, 'changePassword'])->name('api.auth.change-password')->middleware('auth');
});