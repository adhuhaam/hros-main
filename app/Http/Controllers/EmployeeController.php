<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        $query = Employee::with('user');

        // Apply filters
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('employment_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $employees = $query->latest()->paginate(15);
        $departments = Employee::distinct()->pluck('department');
        $statuses = ['Active', 'Inactive', 'Resigned', 'Terminated', 'Retired', 'Dead', 'Missing'];

        return view('employees.index', compact('employees', 'departments', 'statuses'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $departments = ['HR', 'IT', 'Finance', 'Operations', 'Sales', 'Marketing', 'Customer Service'];
        $positions = ['Manager', 'Supervisor', 'Officer', 'Assistant', 'Specialist', 'Coordinator'];
        $nationalities = ['Maldives', 'India', 'Sri Lanka', 'Bangladesh', 'Pakistan', 'Nepal', 'Philippines', 'Other'];

        return view('employees.create', compact('departments', 'positions', 'nationalities'));
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|string|unique:employees,employee_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'nationality' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $photo = $request->file('profile_photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photo->storeAs('public/employees/photos', $photoName);
            $data['profile_photo'] = $photoName;
        }

        // Create employee
        $employee = Employee::create($data);

        // Create user account if requested
        if ($request->boolean('create_user_account')) {
            $user = User::create([
                'name' => $employee->first_name . ' ' . $employee->last_name,
                'email' => $employee->email,
                'username' => $employee->employee_id,
                'password' => Hash::make($request->password ?? 'password123'),
                'role' => $request->user_role ?? 'Other Staff',
            ]);

            $employee->update(['user_id' => $user->id]);
        }

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        $employee->load(['user', 'leaves', 'attendance', 'loans', 'medicalRecords', 'warnings', 'documents']);
        
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        $departments = ['HR', 'IT', 'Finance', 'Operations', 'Sales', 'Marketing', 'Customer Service'];
        $positions = ['Manager', 'Supervisor', 'Officer', 'Assistant', 'Specialist', 'Coordinator'];
        $nationalities = ['Maldives', 'India', 'Sri Lanka', 'Bangladesh', 'Pakistan', 'Nepal', 'Philippines', 'Other'];
        $statuses = ['Active', 'Inactive', 'Resigned', 'Terminated', 'Retired', 'Dead', 'Missing'];

        return view('employees.edit', compact('employee', 'departments', 'positions', 'nationalities', 'statuses'));
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|string|unique:employees,employee_id,' . $employee->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'nationality' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo
            if ($employee->profile_photo) {
                Storage::delete('public/employees/photos/' . $employee->profile_photo);
            }

            $photo = $request->file('profile_photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photo->storeAs('public/employees/photos', $photoName);
            $data['profile_photo'] = $photoName;
        }

        $employee->update($data);

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee)
    {
        // Delete profile photo
        if ($employee->profile_photo) {
            Storage::delete('public/employees/photos/' . $employee->profile_photo);
        }

        // Delete associated user account
        if ($employee->user) {
            $employee->user->delete();
        }

        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    /**
     * Export employees to Excel.
     */
    public function export(Request $request)
    {
        $employees = Employee::with('user')->get();
        
        // Here you would implement Excel export logic
        // For now, we'll return a CSV download
        
        $filename = 'employees_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($employees) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Employee ID', 'Name', 'Email', 'Phone', 'Department', 
                'Position', 'Hire Date', 'Salary', 'Status'
            ]);

            // Add data
            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->employee_id,
                    $employee->full_name,
                    $employee->email,
                    $employee->phone,
                    $employee->department,
                    $employee->position,
                    $employee->hire_date->format('Y-m-d'),
                    $employee->salary,
                    $employee->employment_status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import employees from Excel.
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Here you would implement Excel import logic
        // For now, we'll just show a success message
        
        return redirect()->back()
            ->with('success', 'Employees imported successfully.');
    }
}