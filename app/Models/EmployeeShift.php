<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmployeeShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'shift_name',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'grace_period_minutes',
        'is_active',
        'working_days',
        'effective_from',
        'effective_to',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'break_start' => 'datetime:H:i',
        'break_end' => 'datetime:H:i',
        'grace_period_minutes' => 'integer',
        'is_active' => 'boolean',
        'working_days' => 'array',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    /**
     * Get the employee that owns the shift.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'emp_no');
    }

    /**
     * Get attendance records for this shift.
     */
    public function attendanceRecords()
    {
        return $this->hasMany(Attendance::class, 'employee_id', 'employee_id');
    }

    /**
     * Scope for active shifts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for current shifts (effective today).
     */
    public function scopeCurrent($query)
    {
        $today = today();
        return $query->where('effective_from', '<=', $today)
                    ->where(function($q) use ($today) {
                        $q->whereNull('effective_to')
                          ->orWhere('effective_to', '>=', $today);
                    });
    }

    /**
     * Check if shift is active for a specific date.
     */
    public function isActiveForDate($date)
    {
        $date = Carbon::parse($date);
        return $this->is_active &&
               $this->effective_from <= $date &&
               ($this->effective_to === null || $this->effective_to >= $date);
    }

    /**
     * Check if employee should work on a specific day of week.
     */
    public function shouldWorkOnDay($dayOfWeek)
    {
        if (empty($this->working_days)) {
            return true; // Default to all days if not specified
        }
        
        return in_array($dayOfWeek, $this->working_days);
    }

    /**
     * Get shift duration in hours.
     */
    public function getDurationHours()
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        
        return $start->diffInHours($end);
    }

    /**
     * Get break duration in hours.
     */
    public function getBreakDurationHours()
    {
        if (!$this->break_start || !$this->break_end) {
            return 0;
        }
        
        $breakStart = Carbon::parse($this->break_start);
        $breakEnd = Carbon::parse($this->break_end);
        
        return $breakStart->diffInHours($breakEnd);
    }

    /**
     * Get net working hours (excluding breaks).
     */
    public function getNetWorkingHours()
    {
        return $this->getDurationHours() - $this->getBreakDurationHours();
    }

    /**
     * Check if a time is within working hours.
     */
    public function isWithinWorkingHours($time)
    {
        $time = Carbon::parse($time);
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        
        return $time->between($start, $end);
    }

    /**
     * Check if a time is during break.
     */
    public function isDuringBreak($time)
    {
        if (!$this->break_start || !$this->break_end) {
            return false;
        }
        
        $time = Carbon::parse($time);
        $breakStart = Carbon::parse($this->break_start);
        $breakEnd = Carbon::parse($this->break_end);
        
        return $time->between($breakStart, $breakEnd);
    }

    /**
     * Get next working day from a given date.
     */
    public function getNextWorkingDay($date = null)
    {
        $date = $date ? Carbon::parse($date) : today();
        
        for ($i = 1; $i <= 7; $i++) {
            $nextDay = $date->copy()->addDays($i);
            if ($this->shouldWorkOnDay($nextDay->dayOfWeek)) {
                return $nextDay;
            }
        }
        
        return null;
    }

    /**
     * Get previous working day from a given date.
     */
    public function getPreviousWorkingDay($date = null)
    {
        $date = $date ? Carbon::parse($date) : today();
        
        for ($i = 1; $i <= 7; $i++) {
            $prevDay = $date->copy()->subDays($i);
            if ($this->shouldWorkOnDay($prevDay->dayOfWeek)) {
                return $prevDay;
            }
        }
        
        return null;
    }

    /**
     * Create a default shift for an employee.
     */
    public static function createDefaultShift($employeeId)
    {
        return self::create([
            'employee_id' => $employeeId,
            'shift_name' => 'Standard Shift',
            'start_time' => '09:00',
            'end_time' => '17:00',
            'break_start' => '12:00',
            'break_end' => '13:00',
            'grace_period_minutes' => 15,
            'is_active' => true,
            'working_days' => [1, 2, 3, 4, 5], // Monday to Friday
            'effective_from' => today(),
        ]);
    }
} 