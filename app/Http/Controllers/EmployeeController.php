<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\Document;
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
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('emp_no', 'like', "%{$search}%")
                  ->orWhere('emp_email', 'like', "%{$search}%")
                  ->orWhere('company_email', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('date_of_join', 'desc')->paginate(15);
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
            'emp_no' => 'required|string|unique:employees,emp_no',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'dob' => 'required|date',
            'nationality' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'date_of_join' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'contact_number' => 'required|string|max:20',
            'employment_status' => 'required|string|max:255',
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
                'name' => $employee->name,
                'email' => $employee->emp_email ?? $employee->company_email,
                'username' => $employee->emp_no,
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
        $employee->load(['user', 'leaveRecords', 'attendanceRecords', 'warnings', 'documents']);
        
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
            'name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'dob' => 'required|date',
            'nationality' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'date_of_join' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'contact_number' => 'required|string|max:20',
            'employment_status' => 'required|string|max:255',
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

        // Update the employee record (emp_no cannot be changed by users)
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

    /**
     * Upload document for an employee.
     */
    public function uploadDocument(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240', // 10MB max
            'expiry_date' => 'nullable|date|after:today',
            'is_required' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $file = $request->file('document_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = 'documents/' . $employee->emp_no . '/' . $fileName;
        
        // Store the file
        $file->storeAs('public/' . $filePath);

        // Create document record
        $document = Document::create([
            'emp_no' => $employee->emp_no,
            'document_type' => $request->document_type,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $file->getSize(),
            'file_type' => $file->getClientMimeType(),
            'uploaded_by' => auth()->id(),
            'expiry_date' => $request->expiry_date,
            'status' => 'Active',
            'is_required' => $request->boolean('is_required'),
            'is_verified' => false,
            'notes' => $request->notes,
        ]);

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Delete document for an employee.
     */
    public function deleteDocument(Request $request, Employee $employee, Document $document)
    {
        // Check if the document belongs to the employee
        if ($document->emp_no !== $employee->emp_no) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        // Delete the file from storage
        if (Storage::exists('public/' . $document->file_path)) {
            Storage::delete('public/' . $document->file_path);
        }

        // Delete the document record
        $document->delete();

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Document deleted successfully.');
    }

    /**
     * Verify document for an employee.
     */
    public function verifyDocument(Request $request, Employee $employee, Document $document)
    {
        // Check if the document belongs to the employee
        if ($document->emp_no !== $employee->emp_no) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        $document->verify(auth()->id());

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Document verified successfully.');
    }

    /**
     * Download document for an employee.
     */
    public function downloadDocument(Request $request, Employee $employee, Document $document)
    {
        // Check if the document belongs to the employee
        if ($document->emp_no !== $employee->emp_no) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        $filePath = storage_path('app/public/' . $document->file_path);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return response()->download($filePath, $document->file_name);
    }
}