<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'total_hours',
        'status',
        'notes',
        'overtime_hours',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'total_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
    ];

    /**
     * Get the employee that owns the attendance record.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
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
     * Calculate total hours worked.
     */
    public function calculateTotalHours()
    {
        if ($this->check_in && $this->check_out) {
            $this->total_hours = $this->check_in->diffInHours($this->check_out, true);
            $this->save();
        }
    }
}