<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'attendance_records';

    protected $fillable = [
        'employee_id',
        'date',
        'time_in',
        'time_out',
        'total_hours',
        'status',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'total_hours' => 'decimal:2'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_no', 'emp_no');
    }
} 