<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance records.
     */
    public function index(Request $request)
    {
        $query = Attendance::with(['employee', 'shift']);

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        } elseif ($request->filled('date')) {
            $query->where('date', $request->date);
        } else {
            $query->thisMonth();
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        $attendance = $query->orderBy('date', 'desc')
                           ->orderBy('employee_id')
                           ->paginate(50);

        $employees = Employee::orderBy('name')->get();
        $departments = Employee::distinct()->pluck('department')->filter();

        return view('attendance.index', compact('attendance', 'employees', 'departments'));
    }

    /**
     * Show the form for creating a new attendance record.
     */
    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        return view('attendance.create', compact('employees'));
    }

    /**
     * Store a newly created attendance record.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,emp_no',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:Present,Absent,Late,Early Departure,Half Day,Leave,Holiday,Weekend,Remote,Business Trip',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check for duplicate attendance record
        $existing = Attendance::where('employee_id', $request->employee_id)
                             ->where('date', $request->date)
                             ->first();

        if ($existing) {
            return back()->withErrors(['date' => 'Attendance record already exists for this employee on this date.'])->withInput();
        }

        // Get employee's shift for the date
        $shift = EmployeeShift::where('employee_id', $request->employee_id)
                             ->where('is_active', true)
                             ->where('effective_from', '<=', $request->date)
                             ->where(function($query) use ($request) {
                                 $query->whereNull('effective_to')
                                       ->orWhere('effective_to', '>=', $request->date);
                             })
                             ->first();

        $attendance = Attendance::create([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'scheduled_start' => $shift?->start_time,
            'scheduled_end' => $shift?->end_time,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => $request->status,
            'notes' => $request->notes,
            'check_in_ip' => $request->ip(),
        ]);

        return redirect()->route('attendance.index')
                        ->with('success', 'Attendance record created successfully.');
    }

    /**
     * Display the specified attendance record.
     */
    public function show(Attendance $attendance)
    {
        $attendance->load(['employee', 'shift', 'approver']);
        return view('attendance.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified attendance record.
     */
    public function edit(Attendance $attendance)
    {
        $employees = Employee::orderBy('name')->get();
        return view('attendance.edit', compact('attendance', 'employees'));
    }

    /**
     * Update the specified attendance record.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $validator = Validator::make($request->all(), [
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:Present,Absent,Late,Early Departure,Half Day,Leave,Holiday,Weekend,Remote,Business Trip',
            'notes' => 'nullable|string|max:500',
            'manager_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $attendance->update([
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => $request->status,
            'notes' => $request->notes,
            'manager_notes' => $request->manager_notes,
        ]);

        return redirect()->route('attendance.index')
                        ->with('success', 'Attendance record updated successfully.');
    }

    /**
     * Remove the specified attendance record.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('attendance.index')
                        ->with('success', 'Attendance record deleted successfully.');
    }

    /**
     * Employee check-in functionality.
     */
    public function checkIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,emp_no',
            'location' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employeeId = $request->employee_id;
        $today = today();

        // Check if already checked in today
        $existing = Attendance::where('employee_id', $employeeId)
                             ->where('date', $today)
                             ->first();

        if ($existing && $existing->check_in) {
            return response()->json(['message' => 'Already checked in today.'], 400);
        }

        // Get employee's shift
        $shift = EmployeeShift::where('employee_id', $employeeId)
                             ->where('is_active', true)
                             ->where('effective_from', '<=', $today)
                             ->where(function($query) use ($today) {
                                 $query->whereNull('effective_to')
                                       ->orWhere('effective_to', '>=', $today);
                             })
                             ->first();

        $now = now();
        $checkInTime = $now->format('H:i');

        // Determine status based on shift
        $status = 'Present';
        if ($shift) {
            $scheduledStart = Carbon::parse($shift->start_time);
            $gracePeriod = $shift->grace_period_minutes;
            $lateThreshold = $scheduledStart->copy()->addMinutes($gracePeriod);

            if ($now->gt($lateThreshold)) {
                $status = 'Late';
            }
        }

        if ($existing) {
            $existing->update([
                'check_in' => $checkInTime,
                'check_in_location' => $request->location,
                'check_in_ip' => $request->ip(),
                'status' => $status,
                'notes' => $request->notes,
            ]);
            $attendance = $existing;
        } else {
            $attendance = Attendance::create([
                'employee_id' => $employeeId,
                'date' => $today,
                'scheduled_start' => $shift?->start_time,
                'scheduled_end' => $shift?->end_time,
                'check_in' => $checkInTime,
                'check_in_location' => $request->location,
                'check_in_ip' => $request->ip(),
                'status' => $status,
                'notes' => $request->notes,
            ]);
        }

        return response()->json([
            'message' => 'Check-in successful!',
            'attendance' => $attendance->load('employee'),
            'check_in_time' => $checkInTime,
            'status' => $status,
        ]);
    }

    /**
     * Employee check-out functionality.
     */
    public function checkOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,emp_no',
            'location' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employeeId = $request->employee_id;
        $today = today();

        $attendance = Attendance::where('employee_id', $employeeId)
                               ->where('date', $today)
                               ->first();

        if (!$attendance) {
            return response()->json(['message' => 'No check-in record found for today.'], 400);
        }

        if ($attendance->check_out) {
            return response()->json(['message' => 'Already checked out today.'], 400);
        }

        $now = now();
        $checkOutTime = $now->format('H:i');

        $attendance->update([
            'check_out' => $checkOutTime,
            'check_out_location' => $request->location,
            'check_out_ip' => $request->ip(),
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Check-out successful!',
            'attendance' => $attendance->load('employee'),
            'check_out_time' => $checkOutTime,
            'total_hours' => $attendance->total_hours,
            'overtime_hours' => $attendance->overtime_hours,
        ]);
    }

    /**
     * Display attendance report.
     */
    public function report(Request $request)
    {
        $query = Attendance::with(['employee']);

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        } else {
            $query->thisMonth();
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        $attendance = $query->get();

        // Calculate summary statistics
        $summary = [
            'total_days' => $attendance->count(),
            'present_days' => $attendance->where('status', 'Present')->count(),
            'absent_days' => $attendance->where('status', 'Absent')->count(),
            'late_days' => $attendance->where('status', 'Late')->count(),
            'leave_days' => $attendance->where('status', 'Leave')->count(),
            'total_hours' => $attendance->sum('total_hours'),
            'total_overtime' => $attendance->sum('overtime_hours'),
            'attendance_rate' => $attendance->count() > 0 ? 
                round(($attendance->where('status', 'Present')->count() / $attendance->count()) * 100, 2) : 0,
        ];

        // Group by employee
        $employeeSummary = $attendance->groupBy('employee_id')->map(function($records) {
            return [
                'employee' => $records->first()->employee,
                'total_days' => $records->count(),
                'present_days' => $records->where('status', 'Present')->count(),
                'absent_days' => $records->where('status', 'Absent')->count(),
                'late_days' => $records->where('status', 'Late')->count(),
                'total_hours' => $records->sum('total_hours'),
                'total_overtime' => $records->sum('overtime_hours'),
                'attendance_rate' => round(($records->where('status', 'Present')->count() / $records->count()) * 100, 2),
            ];
        });

        $employees = Employee::orderBy('name')->get();
        $departments = Employee::distinct()->pluck('department')->filter();

        return view('attendance.report', compact('attendance', 'summary', 'employeeSummary', 'employees', 'departments'));
    }

    /**
     * Bulk import attendance records.
     */
    public function bulkImport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $file = $request->file('file');
            $imported = 0;
            $skipped = 0;

            // Process file based on type
            if ($file->getClientOriginalExtension() === 'csv') {
                $handle = fopen($file->getPathname(), 'r');
                $headers = fgetcsv($handle);
                
                while (($data = fgetcsv($handle)) !== false) {
                    $row = array_combine($headers, $data);
                    
                    if ($this->processAttendanceRow($row)) {
                        $imported++;
                    } else {
                        $skipped++;
                    }
                }
                fclose($handle);
            } else {
                // Handle Excel files
                // Implementation for Excel processing would go here
                return back()->with('error', 'Excel import not yet implemented.');
            }

            return back()->with('success', "Import completed: $imported records imported, $skipped skipped.");
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Process a single attendance row from import.
     */
    private function processAttendanceRow($row)
    {
        try {
            // Validate required fields
            if (empty($row['employee_id']) || empty($row['date'])) {
                return false;
            }

            // Check for existing record
            $existing = Attendance::where('employee_id', $row['employee_id'])
                                 ->where('date', $row['date'])
                                 ->exists();

            if ($existing) {
                return false;
            }

            // Create attendance record
            Attendance::create([
                'employee_id' => $row['employee_id'],
                'date' => $row['date'],
                'check_in' => $row['check_in'] ?? null,
                'check_out' => $row['check_out'] ?? null,
                'status' => $row['status'] ?? 'Absent',
                'notes' => $row['notes'] ?? null,
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Export attendance data.
     */
    public function export(Request $request)
    {
        $query = Attendance::with(['employee']);

        // Apply filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        } else {
            $query->thisMonth();
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $attendance = $query->get();

        $filename = 'attendance_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($attendance) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Employee ID', 'Employee Name', 'Date', 'Check In', 'Check Out',
                'Total Hours', 'Overtime Hours', 'Status', 'Notes'
            ]);

            // Add data
            foreach ($attendance as $record) {
                fputcsv($file, [
                    $record->employee_id,
                    $record->employee->name,
                    $record->date->format('Y-m-d'),
                    $record->check_in,
                    $record->check_out,
                    $record->total_hours,
                    $record->overtime_hours,
                    $record->status,
                    $record->notes,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Approve attendance records.
     */
    public function approve(Request $request, Attendance $attendance)
    {
        $attendance->update([
            'is_approved' => true,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Attendance record approved successfully.');
    }

    /**
     * Bulk approve attendance records.
     */
    public function bulkApprove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attendance_ids' => 'required|array',
            'attendance_ids.*' => 'exists:attendance,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        Attendance::whereIn('id', $request->attendance_ids)->update([
            'is_approved' => true,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', count($request->attendance_ids) . ' attendance records approved successfully.');
    }

    /**
     * Get today's attendance summary for dashboard.
     */
    public function todaySummary()
    {
        $today = today();
        
        $summary = [
            'total_employees' => Employee::count(),
            'present_today' => Attendance::where('date', $today)
                                       ->where('status', 'Present')
                                       ->count(),
            'absent_today' => Attendance::where('date', $today)
                                      ->where('status', 'Absent')
                                      ->count(),
            'late_today' => Attendance::where('date', $today)
                                    ->where('status', 'Late')
                                    ->count(),
            'remote_today' => Attendance::where('date', $today)
                                      ->where('status', 'Remote')
                                      ->count(),
        ];

        $recentAttendance = Attendance::with('employee')
                                    ->where('date', $today)
                                    ->orderBy('check_in', 'desc')
                                    ->take(10)
                                    ->get();

        return response()->json([
            'summary' => $summary,
            'recent_attendance' => $recentAttendance,
        ]);
    }

    /**
     * Get employee's current attendance status.
     */
    public function employeeStatus($employeeId)
    {
        $today = today();
        
        $attendance = Attendance::where('employee_id', $employeeId)
                               ->where('date', $today)
                               ->first();

        $shift = EmployeeShift::where('employee_id', $employeeId)
                             ->where('is_active', true)
                             ->where('effective_from', '<=', $today)
                             ->where(function($query) use ($today) {
                                 $query->whereNull('effective_to')
                                       ->orWhere('effective_to', '>=', $today);
                             })
                             ->first();

        return response()->json([
            'attendance' => $attendance,
            'shift' => $shift,
            'can_check_in' => !$attendance || !$attendance->check_in,
            'can_check_out' => $attendance && $attendance->check_in && !$attendance->check_out,
        ]);
    }
} 