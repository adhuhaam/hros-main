<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryIncome extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'salary_income';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'emp_no',
        'date',
        'basic_salary',
        'service_allowance',
        'island_allowance',
        'attendance_allowance',
        'salary_arrear_other',
        'safety_allowance',
        'pump_brick_batching',
        'food_and_tea',
        'long_term_service_allowance',
        'living_allowance',
        'ot',
        'ot_arrears',
        'phone_allowance',
        'petrol_allowance',
        'pension',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'basic_salary' => 'decimal:2',
        'service_allowance' => 'decimal:2',
        'island_allowance' => 'decimal:2',
        'attendance_allowance' => 'decimal:2',
        'salary_arrear_other' => 'decimal:2',
        'safety_allowance' => 'decimal:2',
        'pump_brick_batching' => 'decimal:2',
        'food_and_tea' => 'decimal:2',
        'long_term_service_allowance' => 'decimal:2',
        'living_allowance' => 'decimal:2',
        'ot' => 'decimal:2',
        'ot_arrears' => 'decimal:2',
        'phone_allowance' => 'decimal:2',
        'petrol_allowance' => 'decimal:2',
        'pension' => 'decimal:2',
    ];

    /**
     * Get the employee for this salary income.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_no', 'emp_no');
    }

    /**
     * Calculate total income.
     */
    public function getTotalIncomeAttribute()
    {
        return $this->basic_salary + $this->service_allowance + $this->island_allowance + 
               $this->attendance_allowance + $this->salary_arrear_other + $this->safety_allowance + 
               $this->pump_brick_batching + $this->food_and_tea + $this->long_term_service_allowance + 
               $this->living_allowance + $this->ot + $this->ot_arrears + $this->phone_allowance + 
               $this->petrol_allowance + $this->pension;
    }
} 