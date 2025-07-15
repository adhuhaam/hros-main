<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'emp_no';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'emp_no',
        'name',
        'gender',
        'designation',
        'xpat_designation',
        'xpat_join_date',
        'department',
        'nationality',
        'passport_nic_no',
        'passport_expire_date',
        'dob',
        'wp_no',
        'date_of_join',
        'contact_number',
        'contact_number_foregn',
        'emergency_contact_number',
        'emergency_contact_name',
        'employment_status',
        'work_site',
        'insurance_provider',
        'recruiting_agency',
        'emp_email',
        'company_email',
        'permanent_address',
        'persent_address',
        'basic_salary',
        'salary_currency',
        'termination_date',
        'level',
        'company',
    ];

    protected $casts = [
        'xpat_join_date' => 'date',
        'passport_expire_date' => 'date',
        'dob' => 'date',
        'date_of_join' => 'date',
        'basic_salary' => 'decimal:2',
        'termination_date' => 'date',
    ];

    /**
     * Get the user associated with the employee.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'emp_no', 'emp_no');
    }

    /**
     * Get the employee's attendance records.
     */
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'emp_no', 'emp_no');
    }

    /**
     * Get the employee's leave records.
     */
    public function leaveRecords()
    {
        return $this->hasMany(LeaveRecord::class, 'emp_no', 'emp_no');
    }

    /**
     * Get the employee's salary income records.
     */
    public function salaryIncome()
    {
        return $this->hasMany(SalaryIncome::class, 'emp_no', 'emp_no');
    }

    /**
     * Get the employee's salary deduction records.
     */
    public function salaryDeductions()
    {
        return $this->hasMany(SalaryDeduction::class, 'emp_no', 'emp_no');
    }

    /**
     * Get the employee's warnings.
     */
    public function warnings()
    {
        return $this->hasMany(Warning::class, 'emp_no', 'emp_no');
    }

    /**
     * Get the employee's documents.
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'emp_no', 'emp_no');
    }

    /**
     * Scope for active employees.
     */
    public function scopeActive($query)
    {
        return $query->where('employment_status', 'Active');
    }

    /**
     * Scope for employees by department.
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Check if employee is active.
     */
    public function isActive()
    {
        return $this->employment_status === 'Active';
    }

    /**
     * Get employee's service years.
     */
    public function getServiceYearsAttribute()
    {
        if (!$this->date_of_join) {
            return 0;
        }
        
        return $this->date_of_join->diffInYears(now());
    }
}