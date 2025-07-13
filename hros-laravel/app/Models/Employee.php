<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Employee extends Model
{
    use HasFactory, SoftDeletes, HasRoles;

    protected $table = 'employees';

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
        'passport_nic_no_expires',
        'dob',
        'wp_no',
        'date_of_join',
        'contact_number',
        'emergency_contact_number',
        'emergency_contact_name',
        'employment_status',
        'work_site',
        'insurance_provider',
        'recruiting_agency',
        'emp_email',
        'permanent_address',
        'basic_salary',
        'salary_currency',
        'termination_date',
        'player_id', // For push notifications
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'xpat_join_date' => 'date',
        'passport_nic_no_expires' => 'date',
        'dob' => 'date',
        'date_of_join' => 'date',
        'termination_date' => 'date',
        'basic_salary' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'xpat_join_date',
        'passport_nic_no_expires',
        'dob',
        'date_of_join',
        'termination_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Employment status constants
    const STATUS_ACTIVE = 'Active';
    const STATUS_TERMINATED = 'Terminated';
    const STATUS_RESIGNED = 'Resigned';
    const STATUS_REJOINED = 'Rejoined';
    const STATUS_DEAD = 'Dead';
    const STATUS_RETIRED = 'Retired';
    const STATUS_MISSING = 'Missing';

    // Gender constants
    const GENDER_MALE = 'Male';
    const GENDER_FEMALE = 'Female';

    // Salary currency constants
    const CURRENCY_MVR = 'MVR';
    const CURRENCY_USD = 'USD';

    /**
     * Get the user associated with this employee
     */
    public function user()
    {
        return $this->hasOne(User::class, 'emp_no', 'emp_no');
    }

    /**
     * Get all leave records for this employee
     */
    public function leaveRecords()
    {
        return $this->hasMany(LeaveRecord::class, 'emp_no', 'emp_no');
    }

    /**
     * Get leave balance for this employee
     */
    public function leaveBalance()
    {
        return $this->hasOne(LeaveBalance::class, 'emp_no', 'emp_no');
    }

    /**
     * Get payroll records for this employee
     */
    public function payrollRecords()
    {
        return $this->hasMany(PayrollRecord::class, 'emp_no', 'emp_no');
    }

    /**
     * Get salary setup for this employee
     */
    public function salarySetup()
    {
        return $this->hasOne(SalarySetup::class, 'emp_no', 'emp_no');
    }

    /**
     * Get visa records for this employee
     */
    public function visaRecords()
    {
        return $this->hasMany(VisaSticker::class, 'emp_no', 'emp_no');
    }

    /**
     * Get work permit records for this employee
     */
    public function workPermitRecords()
    {
        return $this->hasMany(WorkPermitFee::class, 'emp_no', 'emp_no');
    }

    /**
     * Get passport renewal records for this employee
     */
    public function passportRenewals()
    {
        return $this->hasMany(PassportRenewal::class, 'emp_no', 'emp_no');
    }

    /**
     * Get medical examination records for this employee
     */
    public function medicalExaminations()
    {
        return $this->hasMany(MedicalExamination::class, 'emp_no', 'emp_no');
    }

    /**
     * Get bank account records for this employee
     */
    public function bankAccountRecords()
    {
        return $this->hasMany(BankAccountRecord::class, 'emp_no', 'emp_no');
    }

    /**
     * Get employee tickets for this employee
     */
    public function tickets()
    {
        return $this->hasMany(EmployeeTicket::class, 'emp_no', 'emp_no');
    }

    /**
     * Get documents for this employee
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'emp_no', 'emp_no');
    }

    /**
     * Get warnings for this employee
     */
    public function warnings()
    {
        return $this->hasMany(Warning::class, 'emp_no', 'emp_no');
    }

    /**
     * Get resignations for this employee
     */
    public function resignations()
    {
        return $this->hasMany(Resignation::class, 'emp_no', 'emp_no');
    }

    /**
     * Get overtime records for this employee
     */
    public function overtimeRecords()
    {
        return $this->hasMany(OvertimeRecord::class, 'emp_no', 'emp_no');
    }

    /**
     * Get attendance records for this employee
     */
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'emp_no', 'emp_no');
    }

    /**
     * Scope for active employees
     */
    public function scopeActive($query)
    {
        return $query->where('employment_status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for terminated employees
     */
    public function scopeTerminated($query)
    {
        return $query->where('employment_status', self::STATUS_TERMINATED);
    }

    /**
     * Scope for resigned employees
     */
    public function scopeResigned($query)
    {
        return $query->where('employment_status', self::STATUS_RESIGNED);
    }

    /**
     * Scope for expatriate employees
     */
    public function scopeExpatriate($query)
    {
        return $query->where('nationality', '!=', 'MALDIVIAN');
    }

    /**
     * Scope for local employees
     */
    public function scopeLocal($query)
    {
        return $query->where('nationality', 'MALDIVIAN');
    }

    /**
     * Get employee's age
     */
    public function getAgeAttribute()
    {
        if ($this->dob) {
            return $this->dob->age;
        }
        return null;
    }

    /**
     * Get employee's service years
     */
    public function getServiceYearsAttribute()
    {
        if ($this->date_of_join) {
            return $this->date_of_join->diffInYears(now());
        }
        return null;
    }

    /**
     * Check if passport is expiring soon (within 6 months)
     */
    public function isPassportExpiringSoon()
    {
        if ($this->passport_nic_no_expires) {
            return $this->passport_nic_no_expires->diffInMonths(now()) <= 6;
        }
        return false;
    }

    /**
     * Check if employee is on leave today
     */
    public function isOnLeaveToday()
    {
        return $this->leaveRecords()
            ->where('status', 'Approved')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->exists();
    }

    /**
     * Get employee's full name with designation
     */
    public function getFullNameWithDesignationAttribute()
    {
        return $this->name . ' (' . $this->designation . ')';
    }

    /**
     * Get employee's department and designation
     */
    public function getDepartmentDesignationAttribute()
    {
        return $this->department . ' - ' . $this->designation;
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