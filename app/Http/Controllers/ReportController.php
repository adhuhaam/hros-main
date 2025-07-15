<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\Attendance;
use App\Models\Warning;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $employeeCount = Employee::count();
        $leaveCount = Leave::count();
        $attendanceCount = Attendance::count();
        $warningCount = Warning::count();

        $recentLeaves = Leave::with('employee')->orderBy('created_at', 'desc')->take(10)->get();
        $recentWarnings = Warning::with('employee')->orderBy('created_at', 'desc')->take(10)->get();
        $recentAttendance = Attendance::with('employee')->orderBy('date', 'desc')->take(10)->get();

        return view('reports.index', compact(
            'employeeCount',
            'leaveCount',
            'attendanceCount',
            'warningCount',
            'recentLeaves',
            'recentWarnings',
            'recentAttendance',
        ));
    }
} 