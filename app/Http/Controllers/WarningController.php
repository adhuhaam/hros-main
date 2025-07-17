<?php

namespace App\Http\Controllers;

use App\Models\Warning;
use App\Models\Employee;
use Illuminate\Http\Request;

class WarningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Warning::with(['employee']);

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by warning type
        if ($request->filled('warning_type')) {
            $query->where('warning_type', $request->warning_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by employee name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('emp_no', 'like', "%{$search}%");
            });
        }

        $warnings = $query->orderBy('created_at', 'desc')->paginate(15);
        $employees = Employee::orderBy('name')->get();

        return view('warnings.index', compact('warnings', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        $warningTypes = ['Verbal Warning', 'Written Warning', 'Final Warning', 'Suspension', 'Termination'];
        
        return view('warnings.create', compact('employees', 'warningTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,emp_no',
            'warning_type' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'incident_date' => 'required|date',
            'warning_date' => 'required|date|after_or_equal:incident_date',
            'issued_by' => 'required|string|max:255',
            'status' => 'required|in:Active,Resolved,Expired',
            'action_required' => 'nullable|string|max:1000',
            'deadline' => 'nullable|date|after:warning_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $warning = Warning::create([
            'employee_id' => $validated['employee_id'],
            'warning_type' => $validated['warning_type'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'incident_date' => $validated['incident_date'],
            'warning_date' => $validated['warning_date'],
            'issued_by' => $validated['issued_by'],
            'status' => $validated['status'],
            'action_required' => $validated['action_required'],
            'deadline' => $validated['deadline'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('warnings.index')
            ->with('success', 'Warning created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Warning $warning)
    {
        $warning->load(['employee']);
        return view('warnings.show', compact('warning'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warning $warning)
    {
        $employees = Employee::orderBy('name')->get();
        $warningTypes = ['Verbal Warning', 'Written Warning', 'Final Warning', 'Suspension', 'Termination'];
        
        return view('warnings.edit', compact('warning', 'employees', 'warningTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warning $warning)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,emp_no',
            'warning_type' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'incident_date' => 'required|date',
            'warning_date' => 'required|date|after_or_equal:incident_date',
            'issued_by' => 'required|string|max:255',
            'status' => 'required|in:Active,Resolved,Expired',
            'action_required' => 'nullable|string|max:1000',
            'deadline' => 'nullable|date|after:warning_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $warning->update([
            'employee_id' => $validated['employee_id'],
            'warning_type' => $validated['warning_type'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'incident_date' => $validated['incident_date'],
            'warning_date' => $validated['warning_date'],
            'issued_by' => $validated['issued_by'],
            'status' => $validated['status'],
            'action_required' => $validated['action_required'],
            'deadline' => $validated['deadline'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('warnings.index')
            ->with('success', 'Warning updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warning $warning)
    {
        $warning->delete();
        
        return redirect()->route('warnings.index')
            ->with('success', 'Warning deleted successfully.');
    }

    /**
     * Export warnings to CSV
     */
    public function export(Request $request)
    {
        $query = Warning::with(['employee']);

        // Apply filters
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('warning_type')) {
            $query->where('warning_type', $request->warning_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $warnings = $query->get();

        $filename = 'warnings_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($warnings) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Employee ID',
                'Employee Name',
                'Warning Type',
                'Subject',
                'Description',
                'Incident Date',
                'Warning Date',
                'Issued By',
                'Status',
                'Action Required',
                'Deadline',
                'Created At'
            ]);

            // Add data
            foreach ($warnings as $warning) {
                fputcsv($file, [
                    $warning->employee->emp_no ?? '',
                    $warning->employee->name ?? '',
                    $warning->warning_type,
                    $warning->subject,
                    $warning->description,
                    $warning->incident_date->format('Y-m-d'),
                    $warning->warning_date->format('Y-m-d'),
                    $warning->issued_by,
                    $warning->status,
                    $warning->action_required ?? '',
                    $warning->deadline ? $warning->deadline->format('Y-m-d') : '',
                    $warning->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 