<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\Attendance;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function adminDashboard()
    {
        $stats = $this->getAdminStats();
        $recentLeaves = Leave::with('employee')->latest()->take(5)->get();
        $recentEmployees = Employee::orderBy('date_of_join', 'desc')->take(5)->get();
        
        return view('dashboard.admin', compact('stats', 'recentLeaves', 'recentEmployees'));
    }

    /**
     * Show the information officer dashboard.
     */
    public function infoOfficerDashboard()
    {
        $stats = $this->getInfoOfficerStats();
        $recentLeaves = Leave::with('employee')->latest()->take(5)->get();
        
        return view('dashboard.info-officer', compact('stats', 'recentLeaves'));
    }

    /**
     * Show the xpat officer dashboard.
     */
    public function xpatOfficerDashboard()
    {
        $stats = $this->getXpatOfficerStats();
        $expiringDocuments = $this->getExpiringDocuments();
        
        return view('dashboard.xpat-officer', compact('stats', 'expiringDocuments'));
    }

    /**
     * Show the leave officer dashboard.
     */
    public function leaveOfficerDashboard()
    {
        $stats = $this->getLeaveOfficerStats();
        $pendingLeaves = Leave::with('employee')->pending()->latest()->take(10)->get();
        
        return view('dashboard.leave-officer', compact('stats', 'pendingLeaves'));
    }

    /**
     * Show the HR manager dashboard.
     */
    public function hrManagerDashboard()
    {
        $stats = $this->getHrManagerStats();
        $recentEmployees = Employee::orderBy('date_of_join', 'desc')->take(5)->get();
        $pendingLeaves = Leave::with('employee')->pending()->latest()->take(5)->get();
        
        return view('dashboard.hr-manager', compact('stats', 'recentEmployees', 'pendingLeaves'));
    }

    /**
     * Show the payroll officer dashboard.
     */
    public function payrollOfficerDashboard()
    {
        $stats = $this->getPayrollOfficerStats();
        $activeLoans = Loan::with('employee')->active()->latest()->take(5)->get();
        
        return view('dashboard.payroll-officer', compact('stats', 'activeLoans'));
    }

    /**
     * Show the supervisor dashboard.
     */
    public function supervisorDashboard()
    {
        $stats = $this->getSupervisorStats();
        $teamAttendance = $this->getTeamAttendance();
        
        return view('dashboard.supervisor', compact('stats', 'teamAttendance'));
    }

    /**
     * Show the other staff dashboard.
     */
    public function otherStaffDashboard()
    {
        $stats = $this->getOtherStaffStats();
        
        return view('dashboard.other-staff', compact('stats'));
    }

    /**
     * Show the reception dashboard.
     */
    public function receptionDashboard()
    {
        $stats = $this->getReceptionStats();
        $todayAttendance = Attendance::with('employee')->today()->get();
        
        return view('dashboard.reception', compact('stats', 'todayAttendance'));
    }

    /**
     * Get admin dashboard statistics.
     */
    private function getAdminStats()
    {
        return [
            'total_employees' => Employee::count(),
            'active_employees' => Employee::active()->count(),
            'pending_leaves' => Leave::pending()->count(),
            'active_loans' => Loan::active()->count(),
            'today_attendance' => Attendance::today()->present()->count(),
            'absent_today' => Attendance::today()->absent()->count(),
        ];
    }

    /**
     * Get information officer dashboard statistics.
     */
    private function getInfoOfficerStats()
    {
        return [
            'total_employees' => Employee::count(),
            'pending_leaves' => Leave::pending()->count(),
            'approved_leaves' => Leave::approved()->count(),
            'today_attendance' => Attendance::today()->present()->count(),
        ];
    }

    /**
     * Get xpat officer dashboard statistics.
     */
    private function getXpatOfficerStats()
    {
        return [
            'total_employees' => Employee::count(),
            'expiring_passports' => Employee::where('passport_expiry', '<=', now()->addMonths(3))->count(),
            'expiring_visas' => Employee::where('visa_expiry', '<=', now()->addMonths(3))->count(),
            'expiring_work_permits' => Employee::where('work_permit_expiry', '<=', now()->addMonths(3))->count(),
        ];
    }

    /**
     * Get leave officer dashboard statistics.
     */
    private function getLeaveOfficerStats()
    {
        return [
            'pending_leaves' => Leave::pending()->count(),
            'approved_leaves' => Leave::approved()->count(),
            'rejected_leaves' => Leave::rejected()->count(),
            'total_employees' => Employee::active()->count(),
        ];
    }

    /**
     * Get HR manager dashboard statistics.
     */
    private function getHrManagerStats()
    {
        return [
            'total_employees' => Employee::count(),
            'active_employees' => Employee::active()->count(),
            'pending_leaves' => Leave::pending()->count(),
            'active_loans' => Loan::active()->count(),
            'new_employees_this_month' => Employee::where('date_of_join', '>=', now()->startOfMonth())->count(),
        ];
    }

    /**
     * Get payroll officer dashboard statistics.
     */
    private function getPayrollOfficerStats()
    {
        return [
            'total_employees' => Employee::active()->count(),
            'active_loans' => Loan::active()->count(),
            'total_salary' => Employee::active()->sum('salary'),
            'overtime_hours_today' => Attendance::today()->sum('overtime_hours'),
        ];
    }

    /**
     * Get supervisor dashboard statistics.
     */
    private function getSupervisorStats()
    {
        return [
            'team_members' => Employee::active()->count(),
            'present_today' => Attendance::today()->present()->count(),
            'absent_today' => Attendance::today()->absent()->count(),
            'pending_leaves' => Leave::pending()->count(),
        ];
    }

    /**
     * Get other staff dashboard statistics.
     */
    private function getOtherStaffStats()
    {
        return [
            'total_employees' => Employee::count(),
            'my_leaves' => Leave::where('employee_id', Auth::user()->employee->id ?? 0)->count(),
            'my_loans' => Loan::where('employee_id', Auth::user()->employee->id ?? 0)->count(),
        ];
    }

    /**
     * Get reception dashboard statistics.
     */
    private function getReceptionStats()
    {
        return [
            'total_employees' => Employee::count(),
            'present_today' => Attendance::today()->present()->count(),
            'absent_today' => Attendance::today()->absent()->count(),
            'late_today' => Attendance::today()->late()->count(),
        ];
    }

    /**
     * Get expiring documents.
     */
    private function getExpiringDocuments()
    {
        return Employee::where(function($query) {
            $query->where('passport_expiry', '<=', now()->addMonths(3))
                  ->orWhere('visa_expiry', '<=', now()->addMonths(3))
                  ->orWhere('work_permit_expiry', '<=', now()->addMonths(3));
        })->get();
    }

    /**
     * Get team attendance.
     */
    private function getTeamAttendance()
    {
        return Attendance::with('employee')->today()->get();
    }
}