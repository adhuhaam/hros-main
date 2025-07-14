<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'emp_no',
        'leave_type_id',
        'start_date',
        'end_date',
        'actual_arrival_date',
        'num_days',
        'remarks',
        'status',
        'applied_date',
        'approved_by',
        'approval_date',
        'ticket_id',
        'departure_ticket_id',
        'arrival_ticket_id',
        'medical_doc',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'actual_arrival_date' => 'date',
        'applied_date' => 'datetime',
        'approval_date' => 'datetime',
    ];

    /**
     * Get the employee for this leave record.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_no', 'emp_no');
    }

    /**
     * Get the leave type for this leave record.
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Get the approver for this leave record.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
} 