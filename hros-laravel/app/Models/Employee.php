<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'nationality',
        'passport_number',
        'passport_expiry',
        'work_permit_number',
        'work_permit_expiry',
        'visa_number',
        'visa_expiry',
        'department',
        'position',
        'employment_status',
        'hire_date',
        'salary',
        'bank_name',
        'bank_account',
        'emergency_contact_name',
        'emergency_contact_phone',
        'address',
        'accommodation_status',
        'medical_status',
        'user_id',
        'profile_photo',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'passport_expiry' => 'date',
        'work_permit_expiry' => 'date',
        'visa_expiry' => 'date',
        'hire_date' => 'date',
        'salary' => 'decimal:2',
    ];

    /**
     * Get the user associated with the employee.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the employee's full name.
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the employee's leaves.
     */
    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    /**
     * Get the employee's attendance records.
     */
    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the employee's loans.
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Get the employee's medical records.
     */
    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    /**
     * Get the employee's warnings.
     */
    public function warnings()
    {
        return $this->hasMany(Warning::class);
    }

    /**
     * Get the employee's documents.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
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
        if (!$this->hire_date) {
            return 0;
        }
        
        return $this->hire_date->diffInYears(now());
    }
}