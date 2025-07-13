<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'role_name',
        'description',
        'permissions',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Role constants based on existing system
    const ROLE_ADMIN = 'Admin';
    const ROLE_INFO_OFFICER = 'Information Officer';
    const ROLE_XPAT_OFFICER = 'Xpat Officer';
    const ROLE_LEAVE_OFFICER = 'Leave Officer';
    const ROLE_HR_MANAGER = 'HR Manager';
    const ROLE_HOD = 'hod';
    const ROLE_DIRECTOR = 'director';
    const ROLE_PAYROLL_OFFICER = 'Payroll Officer';
    const ROLE_OTHER_STAFF = 'Other Staff';
    const ROLE_SUPERVISOR = 'Supervisor';
    const ROLE_RECEPTION = 'reception';

    /**
     * Get all users with this role
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    /**
     * Get all employees with this role
     */
    public function employees()
    {
        return $this->hasManyThrough(Employee::class, User::class, 'role_id', 'emp_no', 'id', 'emp_no');
    }

    /**
     * Check if role is active
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Get role permissions as array
     */
    public function getPermissionsArrayAttribute()
    {
        return $this->permissions ?? [];
    }

    /**
     * Check if role has specific permission
     */
    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions_array);
    }

    /**
     * Add permission to role
     */
    public function addPermission($permission)
    {
        $permissions = $this->permissions_array;
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }
    }

    /**
     * Remove permission from role
     */
    public function removePermission($permission)
    {
        $permissions = $this->permissions_array;
        $permissions = array_diff($permissions, [$permission]);
        $this->update(['permissions' => array_values($permissions)]);
    }

    /**
     * Scope for active roles
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive roles
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Get dashboard route for this role
     */
    public function getDashboardRouteAttribute()
    {
        switch ($this->role_name) {
            case self::ROLE_ADMIN:
                return 'admin.dashboard';
            case self::ROLE_INFO_OFFICER:
                return 'info-officer.dashboard';
            case self::ROLE_XPAT_OFFICER:
                return 'xpat-officer.dashboard';
            case self::ROLE_LEAVE_OFFICER:
                return 'leave-officer.dashboard';
            case self::ROLE_HR_MANAGER:
                return 'hrm.dashboard';
            case self::ROLE_HOD:
                return 'hod.dashboard';
            case self::ROLE_DIRECTOR:
                return 'director.dashboard';
            case self::ROLE_PAYROLL_OFFICER:
                return 'payroll.dashboard';
            case self::ROLE_SUPERVISOR:
                return 'supervisor.dashboard';
            case self::ROLE_RECEPTION:
                return 'reception.dashboard';
            default:
                return 'other.dashboard';
        }
    }

    /**
     * Get role display name
     */
    public function getDisplayNameAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->role_name));
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