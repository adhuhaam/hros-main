<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'leave_records';

    protected $fillable = [
        'emp_no',
        'leave_type',
        'start_date',
        'end_date',
        'days_requested',
        'reason',
        'status',
        'applied_date',
        'approved_by',
        'approved_date',
        'rejected_reason',
        'actual_departure_date',
        'actual_arrival_date',
        'ticket_number',
        'destination',
        'contact_number',
        'emergency_contact',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'applied_date' => 'datetime',
        'approved_date' => 'datetime',
        'actual_departure_date' => 'datetime',
        'actual_arrival_date' => 'datetime',
        'days_requested' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'applied_date',
        'approved_date',
        'actual_departure_date',
        'actual_arrival_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Leave type constants
    const TYPE_ANNUAL = 'Annual Leave';
    const TYPE_MEDICAL = 'Medical Leave';
    const TYPE_EMERGENCY = 'Emergency Leave';
    const TYPE_MATERNITY = 'Maternity Leave';
    const TYPE_PATERNITY = 'Paternity Leave';
    const TYPE_NO_PAY = 'No Pay Leave';
    const TYPE_SPECIAL = 'Special Leave';
    const TYPE_UMRAH = 'Umrah Leave';

    // Status constants
    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';
    const STATUS_REJECTED = 'Rejected';
    const STATUS_DEPARTED = 'Departed';
    const STATUS_ARRIVED = 'Arrived';
    const STATUS_PENDING_ARRIVAL = 'Pending Leave Arrival';

    /**
     * Get the employee associated with this leave record
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_no', 'emp_no');
    }

    /**
     * Get the user who approved this leave
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the employee who approved this leave
     */
    public function approverEmployee()
    {
        return $this->belongsTo(Employee::class, 'approved_by', 'emp_no');
    }

    /**
     * Get attachments for this leave record
     */
    public function attachments()
    {
        return $this->hasMany(LeaveAttachment::class, 'leave_record_id');
    }

    /**
     * Get leave balance for this employee
     */
    public function leaveBalance()
    {
        return $this->hasOne(LeaveBalance::class, 'emp_no', 'emp_no');
    }

    /**
     * Scope for pending leaves
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved leaves
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for rejected leaves
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope for departed leaves
     */
    public function scopeDeparted($query)
    {
        return $query->where('status', self::STATUS_DEPARTED);
    }

    /**
     * Scope for arrived leaves
     */
    public function scopeArrived($query)
    {
        return $query->where('status', self::STATUS_ARRIVED);
    }

    /**
     * Scope for leaves on specific date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date)
                    ->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for leaves in date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });
    }

    /**
     * Get leave duration in days
     */
    public function getDurationAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date) + 1;
        }
        return $this->days_requested;
    }

    /**
     * Check if leave is currently active
     */
    public function isActive()
    {
        $today = now()->toDateString();
        return $this->status === self::STATUS_APPROVED &&
               $this->start_date <= $today &&
               $this->end_date >= $today;
    }

    /**
     * Check if leave is overdue
     */
    public function isOverdue()
    {
        return $this->status === self::STATUS_DEPARTED &&
               $this->end_date < now()->toDateString() &&
               !$this->actual_arrival_date;
    }

    /**
     * Get leave status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return 'badge-warning';
            case self::STATUS_APPROVED:
                return 'badge-success';
            case self::STATUS_REJECTED:
                return 'badge-danger';
            case self::STATUS_DEPARTED:
                return 'badge-info';
            case self::STATUS_ARRIVED:
                return 'badge-primary';
            case self::STATUS_PENDING_ARRIVAL:
                return 'badge-warning';
            default:
                return 'badge-secondary';
        }
    }

    /**
     * Get leave type badge class
     */
    public function getTypeBadgeClassAttribute()
    {
        switch ($this->leave_type) {
            case self::TYPE_ANNUAL:
                return 'badge-primary';
            case self::TYPE_MEDICAL:
                return 'badge-danger';
            case self::TYPE_EMERGENCY:
                return 'badge-warning';
            case self::TYPE_MATERNITY:
                return 'badge-info';
            case self::TYPE_PATERNITY:
                return 'badge-info';
            case self::TYPE_NO_PAY:
                return 'badge-secondary';
            case self::TYPE_SPECIAL:
                return 'badge-success';
            case self::TYPE_UMRAH:
                return 'badge-dark';
            default:
                return 'badge-secondary';
        }
    }

    /**
     * Approve leave
     */
    public function approve($approvedBy)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $approvedBy,
            'approved_date' => now()
        ]);
    }

    /**
     * Reject leave
     */
    public function reject($rejectedBy, $reason)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'approved_by' => $rejectedBy,
            'approved_date' => now(),
            'rejected_reason' => $reason
        ]);
    }

    /**
     * Mark as departed
     */
    public function markAsDeparted($actualDepartureDate = null)
    {
        $this->update([
            'status' => self::STATUS_DEPARTED,
            'actual_departure_date' => $actualDepartureDate ?? now()
        ]);
    }

    /**
     * Mark as arrived
     */
    public function markAsArrived($actualArrivalDate = null)
    {
        $this->update([
            'status' => self::STATUS_ARRIVED,
            'actual_arrival_date' => $actualArrivalDate ?? now()
        ]);
    }

    /**
     * Boot method to set timezone
     */
    protected static function boot()
    {
        parent::boot();
        
        // Set timezone to Maldivian time
        date_default_timezone_set('Indian/Maldives');
    }
}