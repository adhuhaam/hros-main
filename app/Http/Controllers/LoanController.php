<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Loan::with(['employee', 'approver']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by loan type
        if ($request->filled('loan_type')) {
            $query->where('loan_type', $request->loan_type);
        }

        // Search by employee name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('emp_no', 'like', "%{$search}%");
            });
        }

        $loans = $query->orderBy('created_at', 'desc')->paginate(15);
        $employees = Employee::orderBy('name')->get();

        return view('loans.index', compact('loans', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        $loanTypes = ['Personal Loan', 'Home Loan', 'Vehicle Loan', 'Education Loan', 'Medical Loan', 'Other'];
        
        return view('loans.create', compact('employees', 'loanTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,emp_no',
            'loan_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'total_installments' => 'required|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'purpose' => 'required|string|max:1000',
            'guarantor_name' => 'nullable|string|max:255',
            'guarantor_phone' => 'nullable|string|max:255',
        ]);

        // Calculate loan details
        $amount = $validated['amount'];
        $interestRate = $validated['interest_rate'];
        $totalInstallments = $validated['total_installments'];
        
        $totalAmount = $amount + ($amount * $interestRate / 100);
        $installmentAmount = $totalAmount / $totalInstallments;
        $remainingAmount = $totalAmount;

        $loan = Loan::create([
            'employee_id' => $validated['employee_id'],
            'loan_type' => $validated['loan_type'],
            'amount' => $amount,
            'interest_rate' => $interestRate,
            'total_amount' => $totalAmount,
            'installment_amount' => $installmentAmount,
            'total_installments' => $totalInstallments,
            'paid_installments' => 0,
            'remaining_amount' => $remainingAmount,
            'start_date' => $validated['start_date'],
            'end_date' => null, // Will be calculated when approved
            'status' => 'Pending',
            'purpose' => $validated['purpose'],
            'guarantor_name' => $validated['guarantor_name'],
            'guarantor_phone' => $validated['guarantor_phone'],
        ]);

        return redirect()->route('loans.index')
            ->with('success', 'Loan request created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Loan $loan)
    {
        $loan->load(['employee', 'approver', 'installments']);
        return view('loans.show', compact('loan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Loan $loan)
    {
        $employees = Employee::orderBy('name')->get();
        $loanTypes = ['Personal Loan', 'Home Loan', 'Vehicle Loan', 'Education Loan', 'Medical Loan', 'Other'];
        
        return view('loans.edit', compact('loan', 'employees', 'loanTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,emp_no',
            'loan_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'total_installments' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'purpose' => 'required|string|max:1000',
            'guarantor_name' => 'nullable|string|max:255',
            'guarantor_phone' => 'nullable|string|max:255',
        ]);

        // Recalculate loan details if amount or terms changed
        $amount = $validated['amount'];
        $interestRate = $validated['interest_rate'];
        $totalInstallments = $validated['total_installments'];
        
        $totalAmount = $amount + ($amount * $interestRate / 100);
        $installmentAmount = $totalAmount / $totalInstallments;
        $remainingAmount = $totalAmount - ($loan->paid_installments * $installmentAmount);

        $loan->update([
            'employee_id' => $validated['employee_id'],
            'loan_type' => $validated['loan_type'],
            'amount' => $amount,
            'interest_rate' => $interestRate,
            'total_amount' => $totalAmount,
            'installment_amount' => $installmentAmount,
            'total_installments' => $totalInstallments,
            'remaining_amount' => $remainingAmount,
            'start_date' => $validated['start_date'],
            'purpose' => $validated['purpose'],
            'guarantor_name' => $validated['guarantor_name'],
            'guarantor_phone' => $validated['guarantor_phone'],
        ]);

        return redirect()->route('loans.index')
            ->with('success', 'Loan request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loan $loan)
    {
        $loan->delete();
        
        return redirect()->route('loans.index')
            ->with('success', 'Loan request deleted successfully.');
    }

    /**
     * Approve a loan request.
     */
    public function approve(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'approval_notes' => 'nullable|string|max:500',
        ]);

        // Calculate end date based on installments
        $startDate = \Carbon\Carbon::parse($loan->start_date);
        $endDate = $startDate->copy()->addMonths($loan->total_installments);

        $loan->update([
            'status' => 'Active',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'end_date' => $endDate,
        ]);

        return redirect()->back()
            ->with('success', 'Loan request approved successfully.');
    }

    /**
     * Reject a loan request.
     */
    public function reject(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $loan->update([
            'status' => 'Rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Loan request rejected successfully.');
    }

    /**
     * Pay an installment for a loan.
     */
    public function payInstallment(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_notes' => 'nullable|string|max:500',
        ]);

        $paymentAmount = $validated['payment_amount'];
        $installmentAmount = $loan->installment_amount;

        // Check if this is a full installment payment
        if ($paymentAmount >= $installmentAmount) {
            $loan->update([
                'paid_installments' => $loan->paid_installments + 1,
                'remaining_amount' => $loan->remaining_amount - $installmentAmount,
            ]);

            // Check if loan is completed
            if ($loan->paid_installments >= $loan->total_installments) {
                $loan->update(['status' => 'Completed']);
            }
        } else {
            // Partial payment
            $loan->update([
                'remaining_amount' => $loan->remaining_amount - $paymentAmount,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Export loans to CSV.
     */
    public function export(Request $request)
    {
        $query = Loan::with(['employee', 'approver']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('loan_type')) {
            $query->where('loan_type', $request->loan_type);
        }

        $loans = $query->get();

        $filename = 'loans_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($loans) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Employee ID',
                'Employee Name',
                'Loan Type',
                'Amount',
                'Interest Rate',
                'Total Amount',
                'Installment Amount',
                'Total Installments',
                'Paid Installments',
                'Remaining Amount',
                'Status',
                'Start Date',
                'End Date',
                'Purpose',
                'Approved By',
                'Approved At',
                'Created At'
            ]);

            // Add data
            foreach ($loans as $loan) {
                fputcsv($file, [
                    $loan->employee->emp_no ?? '',
                    $loan->employee->name ?? '',
                    $loan->loan_type,
                    $loan->amount,
                    $loan->interest_rate,
                    $loan->total_amount,
                    $loan->installment_amount,
                    $loan->total_installments,
                    $loan->paid_installments,
                    $loan->remaining_amount,
                    $loan->status,
                    $loan->start_date->format('Y-m-d'),
                    $loan->end_date ? $loan->end_date->format('Y-m-d') : '',
                    $loan->purpose,
                    $loan->approver->name ?? '',
                    $loan->approved_at ? $loan->approved_at->format('Y-m-d H:i:s') : '',
                    $loan->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 