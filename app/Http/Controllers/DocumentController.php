<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Document::with(['employee']);

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by document type
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by employee name or document name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('employee', function ($emp) use ($search) {
                      $emp->where('name', 'like', "%{$search}%")
                          ->orWhere('emp_no', 'like', "%{$search}%");
                  });
            });
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(15);
        $employees = Employee::orderBy('name')->get();

        return view('documents.index', compact('documents', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        $documentTypes = ['Contract', 'ID Card', 'Certificate', 'Policy', 'Form', 'Report', 'Other'];
        
        return view('documents.create', compact('employees', 'documentTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,emp_no',
            'name' => 'required|string|max:255',
            'document_type' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,txt|max:5120',
            'expiry_date' => 'nullable|date|after:today',
            'status' => 'required|in:Active,Expired,Archived',
            'notes' => 'nullable|string|max:1000',
        ]);

        $document = Document::create([
            'employee_id' => $validated['employee_id'],
            'name' => $validated['name'],
            'document_type' => $validated['document_type'],
            'description' => $validated['description'],
            'expiry_date' => $validated['expiry_date'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = 'document_' . $document->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            
            $document->update([
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientMimeType(),
            ]);
        }

        return redirect()->route('documents.index')
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        $document->load(['employee']);
        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        $employees = Employee::orderBy('name')->get();
        $documentTypes = ['Contract', 'ID Card', 'Certificate', 'Policy', 'Form', 'Report', 'Other'];
        
        return view('documents.edit', compact('document', 'employees', 'documentTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,emp_no',
            'name' => 'required|string|max:255',
            'document_type' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,txt|max:5120',
            'expiry_date' => 'nullable|date',
            'status' => 'required|in:Active,Expired,Archived',
            'notes' => 'nullable|string|max:1000',
        ]);

        $document->update([
            'employee_id' => $validated['employee_id'],
            'name' => $validated['name'],
            'document_type' => $validated['document_type'],
            'description' => $validated['description'],
            'expiry_date' => $validated['expiry_date'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            $file = $request->file('file');
            $filename = 'document_' . $document->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            
            $document->update([
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientMimeType(),
            ]);
        }

        return redirect()->route('documents.index')
            ->with('success', 'Document updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        // Delete associated file
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        $document->delete();
        
        return redirect()->route('documents.index')
            ->with('success', 'Document deleted successfully.');
    }

    /**
     * Download document
     */
    public function download(Document $document)
    {
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Document not found.');
        }

        return Storage::disk('public')->download($document->file_path, $document->name);
    }

    /**
     * Verify document
     */
    public function verify(Document $document)
    {
        $document->update([
            'verified' => true,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        return redirect()->back()
            ->with('success', 'Document verified successfully.');
    }

    /**
     * Export documents to CSV
     */
    public function export(Request $request)
    {
        $query = Document::with(['employee']);

        // Apply filters
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $documents = $query->get();

        $filename = 'documents_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($documents) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Employee ID',
                'Employee Name',
                'Document Name',
                'Document Type',
                'Description',
                'File Size',
                'File Type',
                'Status',
                'Verified',
                'Expiry Date',
                'Created At'
            ]);

            // Add data
            foreach ($documents as $document) {
                fputcsv($file, [
                    $document->employee->emp_no ?? '',
                    $document->employee->name ?? '',
                    $document->name,
                    $document->document_type,
                    $document->description ?? '',
                    $document->file_size ? number_format($document->file_size / 1024, 2) . ' KB' : '',
                    $document->file_type ?? '',
                    $document->status,
                    $document->verified ? 'Yes' : 'No',
                    $document->expiry_date ? $document->expiry_date->format('Y-m-d') : '',
                    $document->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 