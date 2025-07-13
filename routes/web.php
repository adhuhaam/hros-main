<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

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

// Authentication Routes
Route::get('/', function () {
    return redirect('/login');
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
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
    
    // Admin Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    
    // Information Officer Dashboard
    Route::get('/info-officer/dashboard', [DashboardController::class, 'infoOfficerDashboard'])->name('info-officer.dashboard');
    
    // Xpat Officer Dashboard
    Route::get('/xpat-officer/dashboard', [DashboardController::class, 'xpatOfficerDashboard'])->name('xpat-officer.dashboard');
    
    // Leave Officer Dashboard
    Route::get('/leave-officer/dashboard', [DashboardController::class, 'leaveOfficerDashboard'])->name('leave-officer.dashboard');
    
    // HR Manager Dashboard
    Route::get('/hr-manager/dashboard', [DashboardController::class, 'hrManagerDashboard'])->name('hr-manager.dashboard');
    
    // Payroll Officer Dashboard
    Route::get('/payroll-officer/dashboard', [DashboardController::class, 'payrollOfficerDashboard'])->name('payroll-officer.dashboard');
    
    // Supervisor Dashboard
    Route::get('/supervisor/dashboard', [DashboardController::class, 'supervisorDashboard'])->name('supervisor.dashboard');
    
    // Other Staff Dashboard
    Route::get('/other-staff/dashboard', [DashboardController::class, 'otherStaffDashboard'])->name('other-staff.dashboard');
    
    // Reception Dashboard
    Route::get('/reception/dashboard', [DashboardController::class, 'receptionDashboard'])->name('reception.dashboard');

    // Employee Routes
    Route::resource('employees', EmployeeController::class);
    Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::post('/employees/import', [EmployeeController::class, 'import'])->name('employees.import');

    // Leave Routes
    Route::resource('leaves', LeaveController::class);
    Route::post('/leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
    Route::post('/leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');

    // Attendance Routes
    Route::resource('attendance', AttendanceController::class);
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');

    // Loan Routes
    Route::resource('loans', LoanController::class);
    Route::post('/loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
    Route::post('/loans/{loan}/pay-installment', [LoanController::class, 'payInstallment'])->name('loans.pay-installment');

    // Additional HR Modules (to be implemented)
    Route::prefix('hr')->group(function () {
        // Medical Records
        Route::resource('medical-records', MedicalRecordController::class);
        
        // Warnings
        Route::resource('warnings', WarningController::class);
        
        // Documents
        Route::resource('documents', DocumentController::class);
        
        // Holidays
        Route::resource('holidays', HolidayController::class);
        
        // Notices
        Route::resource('notices', NoticeController::class);
        
        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // Reports Routes
    Route::prefix('reports')->group(function () {
        Route::get('/employee', [ReportController::class, 'employee'])->name('reports.employee');
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
        Route::get('/leave', [ReportController::class, 'leave'])->name('reports.leave');
        Route::get('/loan', [ReportController::class, 'loan'])->name('reports.loan');
        Route::get('/payroll', [ReportController::class, 'payroll'])->name('reports.payroll');
    });
});