<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $table = 'users';

    protected $fillable = [
        'username',
        'email',
        'password',
        'emp_no',
        'role_id',
        'is_active',
        'last_login_at',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the employee associated with this user
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_no', 'emp_no');
    }

    /**
     * Get the role associated with this user
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get all login attempts for this user
     */
    public function loginAttempts()
    {
        return $this->hasMany(LoginAttempt::class, 'user_id');
    }

    /**
     * Get all security events for this user
     */
    public function securityEvents()
    {
        return $this->hasMany(SecurityEvent::class, 'user_id');
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Get user's full name from employee record
     */
    public function getFullNameAttribute()
    {
        return $this->employee ? $this->employee->name : $this->username;
    }

    /**
     * Get user's department from employee record
     */
    public function getDepartmentAttribute()
    {
        return $this->employee ? $this->employee->department : null;
    }

    /**
     * Get user's designation from employee record
     */
    public function getDesignationAttribute()
    {
        return $this->employee ? $this->employee->designation : null;
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Check if user has specific permission
     */
    public function hasPermission($permission)
    {
        return $this->hasPermissionTo($permission);
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission($permissions)
    {
        return $this->hasAnyPermission($permissions);
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions($permissions)
    {
        return $this->hasAllPermissions($permissions);
    }

    /**
     * Get user's role name
     */
    public function getRoleNameAttribute()
    {
        return $this->role ? $this->role->role_name : null;
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
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