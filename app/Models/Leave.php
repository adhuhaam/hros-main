<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'days_requested',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'destination',
        'emergency_contact',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'days_requested' => 'integer',
    ];

    /**
     * Get the employee that owns the leave.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'emp_no');
    }

    /**
     * Get the user who approved the leave.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope for pending leaves.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope for approved leaves.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    /**
     * Scope for rejected leaves.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }

    /**
     * Check if leave is pending.
     */
    public function isPending()
    {
        return $this->status === 'Pending';
    }

    /**
     * Check if leave is approved.
     */
    public function isApproved()
    {
        return $this->status === 'Approved';
    }

    /**
     * Check if leave is rejected.
     */
    public function isRejected()
    {
        return $this->status === 'Rejected';
    }
}