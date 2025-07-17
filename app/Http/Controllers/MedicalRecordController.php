<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MedicalRecord::with(['employee']);

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by record type
        if ($request->filled('record_type')) {
            $query->where('record_type', $request->record_type);
        }

        // Search by employee name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('emp_no', 'like', "%{$search}%");
            });
        }

        $medicalRecords = $query->orderBy('created_at', 'desc')->paginate(15);
        $employees = Employee::orderBy('name')->get();

        return view('medical-records.index', compact('medicalRecords', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        $recordTypes = ['Medical Examination', 'Health Checkup', 'Vaccination', 'Injury Report', 'Sick Leave', 'Other'];
        
        return view('medical-records.create', compact('employees', 'recordTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,emp_no',
            'record_type' => 'required|string|max:255',
            'examination_date' => 'required|date',
            'doctor_name' => 'required|string|max:255',
            'diagnosis' => 'nullable|string|max:1000',
            'treatment' => 'nullable|string|max:1000',
            'medications' => 'nullable|string|max:1000',
            'recommendations' => 'nullable|string|max:1000',
            'next_follow_up' => 'nullable|date|after:examination_date',
            'status' => 'required|in:Active,Resolved,Under Treatment',
            'notes' => 'nullable|string|max:1000',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $medicalRecord = MedicalRecord::create([
            'employee_id' => $validated['employee_id'],
            'record_type' => $validated['record_type'],
            'examination_date' => $validated['examination_date'],
            'doctor_name' => $validated['doctor_name'],
            'diagnosis' => $validated['diagnosis'],
            'treatment' => $validated['treatment'],
            'medications' => $validated['medications'],
            'recommendations' => $validated['recommendations'],
            'next_follow_up' => $validated['next_follow_up'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        // Handle file upload
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = 'medical_record_' . $medicalRecord->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('medical_records', $filename, 'public');
            
            $medicalRecord->update(['document_path' => $path]);
        }

        return redirect()->route('medical-records.index')
            ->with('success', 'Medical record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load(['employee']);
        return view('medical-records.show', compact('medicalRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalRecord $medicalRecord)
    {
        $employees = Employee::orderBy('name')->get();
        $recordTypes = ['Medical Examination', 'Health Checkup', 'Vaccination', 'Injury Report', 'Sick Leave', 'Other'];
        
        return view('medical-records.edit', compact('medicalRecord', 'employees', 'recordTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,emp_no',
            'record_type' => 'required|string|max:255',
            'examination_date' => 'required|date',
            'doctor_name' => 'required|string|max:255',
            'diagnosis' => 'nullable|string|max:1000',
            'treatment' => 'nullable|string|max:1000',
            'medications' => 'nullable|string|max:1000',
            'recommendations' => 'nullable|string|max:1000',
            'next_follow_up' => 'nullable|date|after:examination_date',
            'status' => 'required|in:Active,Resolved,Under Treatment',
            'notes' => 'nullable|string|max:1000',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $medicalRecord->update([
            'employee_id' => $validated['employee_id'],
            'record_type' => $validated['record_type'],
            'examination_date' => $validated['examination_date'],
            'doctor_name' => $validated['doctor_name'],
            'diagnosis' => $validated['diagnosis'],
            'treatment' => $validated['treatment'],
            'medications' => $validated['medications'],
            'recommendations' => $validated['recommendations'],
            'next_follow_up' => $validated['next_follow_up'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        // Handle file upload
        if ($request->hasFile('document')) {
            // Delete old file if exists
            if ($medicalRecord->document_path) {
                Storage::disk('public')->delete($medicalRecord->document_path);
            }
            
            $file = $request->file('document');
            $filename = 'medical_record_' . $medicalRecord->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('medical_records', $filename, 'public');
            
            $medicalRecord->update(['document_path' => $path]);
        }

        return redirect()->route('medical-records.index')
            ->with('success', 'Medical record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalRecord $medicalRecord)
    {
        // Delete associated file
        if ($medicalRecord->document_path) {
            Storage::disk('public')->delete($medicalRecord->document_path);
        }
        
        $medicalRecord->delete();
        
        return redirect()->route('medical-records.index')
            ->with('success', 'Medical record deleted successfully.');
    }

    /**
     * Download medical record document
     */
    public function download(MedicalRecord $medicalRecord)
    {
        if (!$medicalRecord->document_path || !Storage::disk('public')->exists($medicalRecord->document_path)) {
            abort(404, 'Document not found.');
        }

        return Storage::disk('public')->download($medicalRecord->document_path);
    }

    /**
     * Export medical records to CSV
     */
    public function export(Request $request)
    {
        $query = MedicalRecord::with(['employee']);

        // Apply filters
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('record_type')) {
            $query->where('record_type', $request->record_type);
        }

        $medicalRecords = $query->get();

        $filename = 'medical_records_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($medicalRecords) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Employee ID',
                'Employee Name',
                'Record Type',
                'Examination Date',
                'Doctor Name',
                'Diagnosis',
                'Treatment',
                'Medications',
                'Status',
                'Next Follow Up',
                'Created At'
            ]);

            // Add data
            foreach ($medicalRecords as $record) {
                fputcsv($file, [
                    $record->employee->emp_no ?? '',
                    $record->employee->name ?? '',
                    $record->record_type,
                    $record->examination_date->format('Y-m-d'),
                    $record->doctor_name,
                    $record->diagnosis ?? '',
                    $record->treatment ?? '',
                    $record->medications ?? '',
                    $record->status,
                    $record->next_follow_up ? $record->next_follow_up->format('Y-m-d') : '',
                    $record->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 