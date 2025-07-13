<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ReportController;

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
    return redirect()->route('login');
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

    // Dashboard Routes (Role-based)
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/info-officer/dashboard', [DashboardController::class, 'infoOfficer'])->name('info-officer.dashboard');
    Route::get('/xpat-officer/dashboard', [DashboardController::class, 'xpatOfficer'])->name('xpat-officer.dashboard');
    Route::get('/leave-officer/dashboard', [DashboardController::class, 'leaveOfficer'])->name('leave-officer.dashboard');
    Route::get('/hrm/dashboard', [DashboardController::class, 'hrm'])->name('hrm.dashboard');
    Route::get('/hod/dashboard', [DashboardController::class, 'hod'])->name('hod.dashboard');
    Route::get('/director/dashboard', [DashboardController::class, 'director'])->name('director.dashboard');
    Route::get('/payroll/dashboard', [DashboardController::class, 'payroll'])->name('payroll.dashboard');
    Route::get('/supervisor/dashboard', [DashboardController::class, 'supervisor'])->name('supervisor.dashboard');
    Route::get('/reception/dashboard', [DashboardController::class, 'reception'])->name('reception.dashboard');
    Route::get('/other/dashboard', [DashboardController::class, 'other'])->name('other.dashboard');

    // Employee Management Routes
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
        
        // Employee specific routes
        Route::get('/{employee}/documents', [EmployeeController::class, 'documents'])->name('documents');
        Route::get('/{employee}/leave-history', [EmployeeController::class, 'leaveHistory'])->name('leave-history');
        Route::get('/{employee}/payroll-history', [EmployeeController::class, 'payrollHistory'])->name('payroll-history');
    });

    // Leave Management Routes
    Route::prefix('leaves')->name('leaves.')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::get('/create', [LeaveController::class, 'create'])->name('create');
        Route::post('/', [LeaveController::class, 'store'])->name('store');
        Route::get('/{leaveRecord}', [LeaveController::class, 'show'])->name('show');
        Route::get('/{leaveRecord}/edit', [LeaveController::class, 'edit'])->name('edit');
        Route::put('/{leaveRecord}', [LeaveController::class, 'update'])->name('update');
        Route::delete('/{leaveRecord}', [LeaveController::class, 'destroy'])->name('destroy');
        
        // Leave approval routes
        Route::post('/{leaveRecord}/approve', [LeaveController::class, 'approve'])->name('approve');
        Route::post('/{leaveRecord}/reject', [LeaveController::class, 'reject'])->name('reject');
        Route::post('/{leaveRecord}/depart', [LeaveController::class, 'markAsDeparted'])->name('depart');
        Route::post('/{leaveRecord}/arrive', [LeaveController::class, 'markAsArrived'])->name('arrive');
        
        // Leave balance routes
        Route::get('/balance', [LeaveController::class, 'balance'])->name('balance');
        Route::get('/balance/{employee}', [LeaveController::class, 'employeeBalance'])->name('employee-balance');
    });

    // Payroll Routes
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('index');
        Route::get('/process', [PayrollController::class, 'process'])->name('process');
        Route::post('/process', [PayrollController::class, 'processPayroll'])->name('process-payroll');
        Route::get('/reports', [PayrollController::class, 'reports'])->name('reports');
        Route::get('/salary-setup', [PayrollController::class, 'salarySetup'])->name('salary-setup');
        Route::get('/pay-slips', [PayrollController::class, 'paySlips'])->name('pay-slips');
        Route::get('/pay-slips/{payrollRecord}', [PayrollController::class, 'generatePaySlip'])->name('generate-pay-slip');
    });

    // Settings Routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/users', [SettingsController::class, 'users'])->name('users');
        Route::get('/roles', [SettingsController::class, 'roles'])->name('roles');
        Route::get('/holidays', [SettingsController::class, 'holidays'])->name('holidays');
        Route::get('/notices', [SettingsController::class, 'notices'])->name('notices');
    });

    // Reports Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/hr-summary', [ReportController::class, 'hrSummary'])->name('hr-summary');
        Route::get('/leave-summary', [ReportController::class, 'leaveSummary'])->name('leave-summary');
        Route::get('/payroll-summary', [ReportController::class, 'payrollSummary'])->name('payroll-summary');
        Route::get('/employee-list', [ReportController::class, 'employeeList'])->name('employee-list');
        Route::get('/export/{type}', [ReportController::class, 'export'])->name('export');
    });

    // API Routes for AJAX requests
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/dashboard-stats', [DashboardController::class, 'getStats'])->name('dashboard-stats');
        Route::get('/employees/search', [EmployeeController::class, 'search'])->name('employees-search');
        Route::get('/leaves/calendar', [LeaveController::class, 'calendar'])->name('leaves-calendar');
        Route::get('/birthdays', [EmployeeController::class, 'birthdays'])->name('birthdays');
        Route::get('/holidays', [SettingsController::class, 'getHolidays'])->name('holidays');
    });
});

// Fallback route
Route::fallback(function () {
    return view('errors.404');
});