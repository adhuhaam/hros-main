<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'employee_id',
        'date',
        'scheduled_start',
        'scheduled_end',
        'check_in',
        'check_out',
        'total_hours',
        'overtime_hours',
        'break_hours',
        'status',
        'check_in_status',
        'check_out_status',
        'check_in_location',
        'check_out_location',
        'check_in_ip',
        'check_out_ip',
        'notes',
        'manager_notes',
        'is_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'scheduled_start' => 'datetime:H:i',
        'scheduled_end' => 'datetime:H:i',
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
        'total_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'break_hours' => 'decimal:2',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the employee that owns the attendance record.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'emp_no');
    }

    /**
     * Get the user who approved the attendance.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the employee's shift for this date.
     */
    public function shift()
    {
        return $this->hasOne(EmployeeShift::class, 'employee_id', 'employee_id')
            ->where('is_active', true)
            ->where('effective_from', '<=', $this->date)
            ->where(function($query) {
                $query->whereNull('effective_to')
                      ->orWhere('effective_to', '>=', $this->date);
            });
    }

    /**
     * Scope for today's attendance.
     */
    public function scopeToday($query)
    {
        return $query->where('date', today());
    }

    /**
     * Scope for attendance by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope for present employees.
     */
    public function scopePresent($query)
    {
        return $query->where('status', 'Present');
    }

    /**
     * Scope for absent employees.
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', 'Absent');
    }

    /**
     * Scope for late employees.
     */
    public function scopeLate($query)
    {
        return $query->where('status', 'Late');
    }

    /**
     * Scope for remote work.
     */
    public function scopeRemote($query)
    {
        return $query->where('status', 'Remote');
    }

    /**
     * Scope for approved attendance.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope for pending approval.
     */
    public function scopePendingApproval($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Scope for this week's attendance.
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope for this month's attendance.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
    }

    /**
     * Check if employee is present.
     */
    public function isPresent()
    {
        return $this->status === 'Present';
    }

    /**
     * Check if employee is absent.
     */
    public function isAbsent()
    {
        return $this->status === 'Absent';
    }

    /**
     * Check if employee is late.
     */
    public function isLate()
    {
        return $this->status === 'Late';
    }

    /**
     * Check if employee is working remotely.
     */
    public function isRemote()
    {
        return $this->status === 'Remote';
    }

    /**
     * Check if attendance is approved.
     */
    public function isApproved()
    {
        return $this->is_approved;
    }

    /**
     * Check if employee has checked in.
     */
    public function hasCheckedIn()
    {
        return !is_null($this->check_in);
    }

    /**
     * Check if employee has checked out.
     */
    public function hasCheckedOut()
    {
        return !is_null($this->check_out);
    }

    /**
     * Check if employee is currently at work.
     */
    public function isCurrentlyWorking()
    {
        return $this->hasCheckedIn() && !$this->hasCheckedOut();
    }

    /**
     * Calculate total hours worked.
     */
    public function calculateTotalHours()
    {
        if ($this->check_in && $this->check_out) {
            $checkIn = Carbon::parse($this->check_in);
            $checkOut = Carbon::parse($this->check_out);
            
            $totalMinutes = $checkIn->diffInMinutes($checkOut);
            $this->total_hours = round($totalMinutes / 60, 2);
            
            // Calculate overtime
            $this->calculateOvertimeHours();
            
            $this->save();
        }
    }

    /**
     * Calculate overtime hours.
     */
    public function calculateOvertimeHours()
    {
        if ($this->scheduled_end && $this->check_out) {
            $scheduledEnd = Carbon::parse($this->scheduled_end);
            $checkOut = Carbon::parse($this->check_out);
            
            if ($checkOut->gt($scheduledEnd)) {
                $overtimeMinutes = $scheduledEnd->diffInMinutes($checkOut);
                $this->overtime_hours = round($overtimeMinutes / 60, 2);
            } else {
                $this->overtime_hours = 0;
            }
        }
    }

    /**
     * Determine check-in status (On Time, Late, Early).
     */
    public function determineCheckInStatus()
    {
        if (!$this->check_in || !$this->scheduled_start) {
            return null;
        }

        $checkIn = Carbon::parse($this->check_in);
        $scheduledStart = Carbon::parse($this->scheduled_start);
        $gracePeriod = $this->shift?->grace_period_minutes ?? 15;
        
        $lateThreshold = $scheduledStart->copy()->addMinutes($gracePeriod);

        if ($checkIn->lte($lateThreshold)) {
            return 'On Time';
        } else {
            return 'Late';
        }
    }

    /**
     * Determine check-out status (On Time, Early, Late).
     */
    public function determineCheckOutStatus()
    {
        if (!$this->check_out || !$this->scheduled_end) {
            return null;
        }

        $checkOut = Carbon::parse($this->check_out);
        $scheduledEnd = Carbon::parse($this->scheduled_end);

        if ($checkOut->gte($scheduledEnd)) {
            return 'On Time';
        } else {
            return 'Early';
        }
    }

    /**
     * Get attendance summary for an employee.
     */
    public static function getEmployeeSummary($employeeId, $startDate, $endDate)
    {
        return self::where('employee_id', $employeeId)
            ->dateRange($startDate, $endDate)
            ->selectRaw('
                COUNT(*) as total_days,
                SUM(CASE WHEN status = "Present" THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = "Absent" THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN status = "Late" THEN 1 ELSE 0 END) as late_days,
                SUM(CASE WHEN status = "Leave" THEN 1 ELSE 0 END) as leave_days,
                SUM(total_hours) as total_hours,
                SUM(overtime_hours) as total_overtime
            ')
            ->first();
    }

    /**
     * Get department attendance summary.
     */
    public static function getDepartmentSummary($department, $date)
    {
        return self::whereHas('employee', function($query) use ($department) {
            $query->where('department', $department);
        })
        ->where('date', $date)
        ->selectRaw('
            COUNT(*) as total_employees,
            SUM(CASE WHEN status = "Present" THEN 1 ELSE 0 END) as present_count,
            SUM(CASE WHEN status = "Absent" THEN 1 ELSE 0 END) as absent_count,
            SUM(CASE WHEN status = "Late" THEN 1 ELSE 0 END) as late_count
        ')
        ->first();
    }

    /**
     * Boot method to set up model event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($attendance) {
            // Auto-calculate statuses
            if ($attendance->check_in) {
                $attendance->check_in_status = $attendance->determineCheckInStatus();
            }
            
            if ($attendance->check_out) {
                $attendance->check_out_status = $attendance->determineCheckOutStatus();
            }

            // Auto-calculate hours
            $attendance->calculateTotalHours();
        });
    }
}