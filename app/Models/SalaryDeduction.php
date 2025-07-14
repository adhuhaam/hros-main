<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryDeduction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'salary_deductions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'emp_no',
        'date',
        'other_deduction',
        'salary_advance',
        'loan',
        'pension',
        'medical_deduction',
        'no_pay',
        'late',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'other_deduction' => 'decimal:2',
        'salary_advance' => 'decimal:2',
        'loan' => 'decimal:2',
        'pension' => 'decimal:2',
        'medical_deduction' => 'decimal:2',
        'no_pay' => 'decimal:2',
        'late' => 'decimal:2',
    ];

    /**
     * Get the employee for this salary deduction.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_no', 'emp_no');
    }

    /**
     * Calculate total deductions.
     */
    public function getTotalDeductionsAttribute()
    {
        return $this->other_deduction + $this->salary_advance + $this->loan + 
               $this->pension + $this->medical_deduction + $this->no_pay + $this->late;
    }
} 