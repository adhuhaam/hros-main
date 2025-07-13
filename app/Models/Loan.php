<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'loan_type',
        'amount',
        'interest_rate',
        'total_amount',
        'installment_amount',
        'total_installments',
        'paid_installments',
        'remaining_amount',
        'start_date',
        'end_date',
        'status',
        'approved_by',
        'approved_at',
        'purpose',
        'guarantor_name',
        'guarantor_phone',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'total_installments' => 'integer',
        'paid_installments' => 'integer',
    ];

    /**
     * Get the employee that owns the loan.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who approved the loan.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the loan installments.
     */
    public function installments()
    {
        return $this->hasMany(LoanInstallment::class);
    }

    /**
     * Scope for active loans.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope for pending loans.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope for completed loans.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    /**
     * Check if loan is active.
     */
    public function isActive()
    {
        return $this->status === 'Active';
    }

    /**
     * Check if loan is pending.
     */
    public function isPending()
    {
        return $this->status === 'Pending';
    }

    /**
     * Check if loan is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'Completed';
    }

    /**
     * Calculate remaining installments.
     */
    public function getRemainingInstallmentsAttribute()
    {
        return $this->total_installments - $this->paid_installments;
    }

    /**
     * Calculate progress percentage.
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->total_installments > 0) {
            return round(($this->paid_installments / $this->total_installments) * 100, 2);
        }
        return 0;
    }
}