<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\Attendance;
use App\Models\Loan;
use App\Models\User;
use App\Models\Warning;
use App\Models\Document;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Redirect to appropriate dashboard based on user role.
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->name ?? 'Employee';
        
        return redirect()->route($this->getDashboardRoute($role));
    }

    /**
     * Get dashboard route based on role.
     */
    private function getDashboardRoute($role)
    {
        $routes = [
            'Admin' => 'admin.dashboard',
            'HR Manager' => 'hr-manager.dashboard',
            'HR Officer' => 'hr-officer.dashboard',
            'Finance Manager' => 'finance-manager.dashboard',
            'Finance Officer' => 'finance-officer.dashboard',
            'Project Manager' => 'project-manager.dashboard',
            'Team Leader' => 'team-leader.dashboard',
            'Employee' => 'employee.dashboard',
        ];

        return $routes[$role] ?? 'employee.dashboard';
    }

    /**
     * Admin Dashboard
     */
    public function adminDashboard()
    {
        $stats = $this->getAdminStats();
        $recentLeaves = $this->getRecentRecords(Leave::class, ['employee'], [], 5);
        $recentEmployees = Employee::orderBy('date_of_join', 'desc')->take(5)->get();
        $recentWarnings = $this->getRecentRecords(Warning::class, ['employee'], [], 5);
        $attendanceChart = $this->getAttendanceChartData();
        $leaveChart = $this->getLeaveChartData();
        
        return view('dashboard.admin', compact(
            'stats', 
            'recentLeaves', 
            'recentEmployees', 
            'recentWarnings',
            'attendanceChart',
            'leaveChart'
        ));
    }

    /**
     * HR Manager Dashboard
     */
    public function hrManagerDashboard()
    {
        $stats = $this->getHrManagerStats();
        $recentEmployees = Employee::orderBy('date_of_join', 'desc')->take(5)->get();
        $pendingLeaves = $this->getRecentRecords(Leave::class, ['employee'], [['method' => 'pending']], 5);
        $expiringContracts = $this->getExpiringContracts();
        $departmentStats = $this->getDepartmentStats();
        
        return view('dashboard.hr-manager', compact(
            'stats', 
            'recentEmployees', 
            'pendingLeaves',
            'expiringContracts',
            'departmentStats'
        ));
    }

    /**
     * HR Officer Dashboard
     */
    public function hrOfficerDashboard()
    {
        $stats = $this->getHrOfficerStats();
        $pendingLeaves = $this->getRecentRecords(Leave::class, ['employee'], [['method' => 'pending']], 10);
        $recentEmployees = Employee::orderBy('date_of_join', 'desc')->take(5)->get();
        $expiringDocuments = $this->getExpiringDocuments();
        
        return view('dashboard.hr-officer', compact(
            'stats', 
            'pendingLeaves', 
            'recentEmployees',
            'expiringDocuments'
        ));
    }

    /**
     * Finance Manager Dashboard
     */
    public function financeManagerDashboard()
    {
        $stats = $this->getFinanceManagerStats();
        $activeLoans = $this->getRecentRecords(Loan::class, ['employee'], [['method' => 'active']], 5);
        $salaryStats = $this->getSalaryStats();
        $loanChart = $this->getLoanChartData();
        
        return view('dashboard.finance-manager', compact(
            'stats', 
            'activeLoans',
            'salaryStats',
            'loanChart'
        ));
    }

    /**
     * Finance Officer Dashboard
     */
    public function financeOfficerDashboard()
    {
        $stats = $this->getFinanceOfficerStats();
        $activeLoans = $this->getRecentRecords(Loan::class, ['employee'], [['method' => 'active']], 5);
        $pendingLoans = $this->getRecentRecords(Loan::class, ['employee'], [['method' => 'pending']], 5);
        
        return view('dashboard.finance-officer', compact(
            'stats', 
            'activeLoans',
            'pendingLoans'
        ));
    }

    /**
     * Project Manager Dashboard
     */
    public function projectManagerDashboard()
    {
        $stats = $this->getProjectManagerStats();
        $teamMembers = $this->getTeamMembers();
        $projectStats = $this->getProjectStats();
        
        return view('dashboard.project-manager', compact(
            'stats', 
            'teamMembers',
            'projectStats'
        ));
    }

    /**
     * Team Leader Dashboard
     */
    public function teamLeaderDashboard()
    {
        $stats = $this->getTeamLeaderStats();
        $teamAttendance = $this->getTeamAttendance();
        $teamLeaves = $this->getTeamLeaves();
        
        return view('dashboard.team-leader', compact(
            'stats', 
            'teamAttendance',
            'teamLeaves'
        ));
    }

    /**
     * Employee Dashboard
     */
    public function employeeDashboard()
    {
        $user = Auth::user();
        $employee = $user->employee;
        
        if (!$employee) {
            return redirect()->route('profile')->with('error', 'Employee profile not found.');
        }
        
        $stats = $this->getEmployeeStats($employee);
        $myLeaves = Leave::where('employee_id', $employee->emp_no)->latest()->take(5)->get();
        $myAttendance = Attendance::where('employee_id', $employee->emp_no)->latest()->take(10)->get();
        $myLoans = Loan::where('employee_id', $employee->emp_no)->latest()->take(5)->get();
        
        return view('dashboard.employee', compact(
            'stats', 
            'myLeaves', 
            'myAttendance',
            'myLoans',
            'employee'
        ));
    }

    // Statistics Methods

    private function getAdminStats()
    {
        return [
            'total_employees' => Employee::count(),
            'active_employees' => Employee::where('employment_status', 'Active')->count(),
            'pending_leaves' => Leave::where('status', 'Pending')->count(),
            'active_loans' => Loan::where('status', 'Active')->count(),
            'today_attendance' => Attendance::whereDate('date', today())->where('status', 'Present')->count(),
            'absent_today' => Employee::where('employment_status', 'Active')->count() - 
                            Attendance::whereDate('date', today())->where('status', 'Present')->count(),
            'total_warnings' => Warning::count(),
            'expiring_documents' => $this->getExpiringDocumentsCount(),
        ];
    }

    private function getHrManagerStats()
    {
        return [
            'total_employees' => Employee::count(),
            'active_employees' => Employee::where('employment_status', 'Active')->count(),
            'pending_leaves' => Leave::where('status', 'Pending')->count(),
            'new_employees_this_month' => Employee::where('date_of_join', '>=', now()->startOfMonth())->count(),
            'expiring_contracts' => $this->getExpiringContractsCount(),
            'department_count' => Employee::distinct('department')->count(),
        ];
    }

    private function getHrOfficerStats()
    {
        return [
            'pending_leaves' => Leave::where('status', 'Pending')->count(),
            'approved_leaves' => Leave::where('status', 'Approved')->count(),
            'rejected_leaves' => Leave::where('status', 'Rejected')->count(),
            'total_employees' => Employee::where('employment_status', 'Active')->count(),
            'expiring_documents' => $this->getExpiringDocumentsCount(),
        ];
    }

    private function getFinanceManagerStats()
    {
        return [
            'total_employees' => Employee::where('employment_status', 'Active')->count(),
            'active_loans' => Loan::where('status', 'Active')->count(),
            'total_salary' => Employee::where('employment_status', 'Active')->sum('basic_salary'),
            'pending_loans' => Loan::where('status', 'Pending')->count(),
            'total_loan_amount' => Loan::where('status', 'Active')->sum('amount'),
        ];
    }

    private function getFinanceOfficerStats()
    {
        return [
            'total_employees' => Employee::where('employment_status', 'Active')->count(),
            'active_loans' => Loan::where('status', 'Active')->count(),
            'pending_loans' => Loan::where('status', 'Pending')->count(),
            'total_salary' => Employee::where('employment_status', 'Active')->sum('basic_salary'),
        ];
    }

    private function getProjectManagerStats()
    {
        return [
            'team_members' => Employee::where('employment_status', 'Active')->count(),
            'active_projects' => Project::where('status', 'Active')->count(),
            'completed_projects' => Project::where('status', 'Completed')->count(),
            'pending_tasks' => 0, // To be implemented with tasks table
        ];
    }

    private function getTeamLeaderStats()
    {
        return [
            'team_members' => Employee::where('employment_status', 'Active')->count(),
            'present_today' => Attendance::whereDate('date', today())->where('status', 'Present')->count(),
            'absent_today' => Employee::where('employment_status', 'Active')->count() - 
                            Attendance::whereDate('date', today())->where('status', 'Present')->count(),
            'pending_leaves' => Leave::where('status', 'Pending')->count(),
        ];
    }

    private function getEmployeeStats($employee)
    {
        return [
            'total_leaves' => Leave::where('employee_id', $employee->emp_no)->count(),
            'approved_leaves' => Leave::where('employee_id', $employee->emp_no)->where('status', 'Approved')->count(),
            'pending_leaves' => Leave::where('employee_id', $employee->emp_no)->where('status', 'Pending')->count(),
            'active_loans' => Loan::where('employee_id', $employee->emp_no)->where('status', 'Active')->count(),
            'attendance_this_month' => Attendance::where('employee_id', $employee->emp_no)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('status', 'Present')
                ->count(),
        ];
    }

    // Helper Methods

    private function getRecentRecords($model, $relations = [], $conditions = [], $limit = 5)
    {
        try {
            $query = $model::with($relations);
            
            foreach ($conditions as $condition) {
                $query = $query->{$condition['method']}($condition['value'] ?? null);
            }
            
            return $query->latest()->take($limit)->get();
        } catch (\Exception $e) {
            // Fallback if created_at column is not available
            $query = $model::with($relations);
            
            foreach ($conditions as $condition) {
                $query = $query->{$condition['method']}($condition['value'] ?? null);
            }
            
            return $query->orderBy('id', 'desc')->take($limit)->get();
        }
    }

    private function getExpiringDocuments()
    {
        return Employee::where(function($query) {
            $query->where('passport_nic_no_expires', '<=', now()->addMonths(3))
                  ->orWhere('wp_expiry', '<=', now()->addMonths(3));
        })->get();
    }

    private function getExpiringDocumentsCount()
    {
        return Employee::where(function($query) {
            $query->where('passport_nic_no_expires', '<=', now()->addMonths(3))
                  ->orWhere('wp_expiry', '<=', now()->addMonths(3));
        })->count();
    }

    private function getExpiringContracts()
    {
        return Employee::where('date_of_join', '<=', now()->subYears(2))
            ->where('employment_status', 'Active')
            ->get();
    }

    private function getExpiringContractsCount()
    {
        return Employee::where('date_of_join', '<=', now()->subYears(2))
            ->where('employment_status', 'Active')
            ->count();
    }

    private function getDepartmentStats()
    {
        return Employee::where('employment_status', 'Active')
            ->select('department', DB::raw('count(*) as count'))
            ->groupBy('department')
            ->get();
    }

    private function getSalaryStats()
    {
        return [
            'total_salary' => Employee::where('employment_status', 'Active')->sum('basic_salary'),
            'avg_salary' => Employee::where('employment_status', 'Active')->avg('basic_salary'),
            'min_salary' => Employee::where('employment_status', 'Active')->min('basic_salary'),
            'max_salary' => Employee::where('employment_status', 'Active')->max('basic_salary'),
        ];
    }

    private function getTeamMembers()
    {
        return Employee::where('employment_status', 'Active')->get();
    }

    private function getProjectStats()
    {
        return [
            'total_projects' => Project::count(),
            'active_projects' => Project::where('status', 'Active')->count(),
            'completed_projects' => Project::where('status', 'Completed')->count(),
        ];
    }

    private function getTeamAttendance()
    {
        return Attendance::with('employee')->whereDate('date', today())->get();
    }

    private function getTeamLeaves()
    {
        return Leave::with('employee')->where('status', 'Pending')->get();
    }

    private function getAttendanceChartData()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $data[] = [
                'date' => $date->format('M d'),
                'present' => Attendance::whereDate('date', $date)->where('status', 'Present')->count(),
                'absent' => Employee::where('employment_status', 'Active')->count() - 
                           Attendance::whereDate('date', $date)->where('status', 'Present')->count(),
            ];
        }
        return $data;
    }

    private function getLeaveChartData()
    {
        return [
            'pending' => Leave::where('status', 'Pending')->count(),
            'approved' => Leave::where('status', 'Approved')->count(),
            'rejected' => Leave::where('status', 'Rejected')->count(),
        ];
    }

    private function getLoanChartData()
    {
        return [
            'active' => Loan::where('status', 'Active')->count(),
            'pending' => Loan::where('status', 'Pending')->count(),
            'completed' => Loan::where('status', 'Completed')->count(),
        ];
    }
}