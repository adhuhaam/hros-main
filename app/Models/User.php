<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
// use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'emp_no',
        'username',
        'staff_name',
        'des',
        'email',
        'password',
        'role_id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the employee associated with the user.
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'emp_no', 'emp_no');
    }

    /**
     * Get the role associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if user has specific permission.
     */
    public function hasPermission($permission)
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->hasPermission($permission);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($roleName)
    {
        return $this->role && $this->role->role_name === $roleName;
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roles)
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get user's role name.
     */
    public function getRoleNameAttribute()
    {
        return $this->role ? $this->role->role_name : 'Guest';
    }

    /**
     * Get user's permissions.
     */
    public function getPermissions()
    {
        return $this->role ? $this->role->permissions : [];
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->hasRole('Admin') || $this->hasPermission('system.admin');
    }

    /**
     * Check if user is HR Manager.
     */
    public function isHRManager()
    {
        return $this->hasRole('HR Manager');
    }

    /**
     * Check if user is HR Officer.
     */
    public function isHROfficer()
    {
        return $this->hasRole('HR Officer');
    }

    /**
     * Check if user is Finance Manager.
     */
    public function isFinanceManager()
    {
        return $this->hasRole('Finance Manager');
    }

    /**
     * Check if user is Finance Officer.
     */
    public function isFinanceOfficer()
    {
        return $this->hasRole('Finance Officer');
    }

    /**
     * Check if user is Project Manager.
     */
    public function isProjectManager()
    {
        return $this->hasRole('Project Manager');
    }

    /**
     * Check if user is Team Leader.
     */
    public function isTeamLeader()
    {
        return $this->hasRole('Team Leader');
    }

    /**
     * Check if user is regular Employee.
     */
    public function isEmployee()
    {
        return $this->hasRole('Employee');
    }

    /**
     * Get the appropriate dashboard route for the user.
     */
    public function getDashboardRoute()
    {
        if ($this->isAdmin()) {
            return 'admin.dashboard';
        } elseif ($this->isHRManager()) {
            return 'hr-manager.dashboard';
        } elseif ($this->isHROfficer()) {
            return 'hr-officer.dashboard';
        } elseif ($this->isFinanceManager()) {
            return 'finance-manager.dashboard';
        } elseif ($this->isFinanceOfficer()) {
            return 'finance-officer.dashboard';
        } elseif ($this->isProjectManager()) {
            return 'project-manager.dashboard';
        } elseif ($this->isTeamLeader()) {
            return 'team-leader.dashboard';
        } elseif ($this->isEmployee()) {
            return 'employee.dashboard';
        } else {
            return 'dashboard';
        }
    }

    /**
     * Get user's full name.
     */
    public function getFullNameAttribute()
    {
        return $this->staff_name ?: $this->username;
    }

    /**
     * Scope to filter users by role.
     */
    public function scopeWithRole($query, $roleName)
    {
        return $query->whereHas('role', function($q) use ($roleName) {
            $q->where('role_name', $roleName);
        });
    }

    /**
     * Scope to filter users by permission.
     */
    public function scopeWithPermission($query, $permission)
    {
        return $query->whereHas('role', function($q) use ($permission) {
            $q->whereJsonContains('permissions', $permission);
        });
    }

    /**
     * Set password with proper hashing.
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Get initials for avatar.
     */
    public function getInitialsAttribute()
    {
        $name = $this->full_name;
        $words = explode(' ', $name);
        
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            return strtoupper(substr($name, 0, 2));
        }
    }

    /**
     * Create a new user with default settings.
     */
    public static function createUser($data)
    {
        // Ensure required fields
        $userData = array_merge([
            'password' => 'password123', // Default password
            'role_id' => Role::where('role_name', 'Employee')->first()?->id ?? 1,
        ], $data);

        return self::create($userData);
    }

    /**
     * Update user password.
     */
    public function updatePassword($newPassword)
    {
        $this->password = $newPassword;
        $this->save();
    }

    /**
     * Check if user can access a specific module.
     */
    public function canAccess($module)
    {
        $modulePermissions = [
            'employees' => ['employees.view'],
            'attendance' => ['attendance.view'],
            'leaves' => ['leaves.view'],
            'loans' => ['loans.view'],
            'medical' => ['medical.view'],
            'warnings' => ['warnings.view'],
            'users' => ['users.view'],
            'roles' => ['roles.view'],
            'reports' => ['reports.view'],
            'settings' => ['settings.view'],
        ];

        if (!isset($modulePermissions[$module])) {
            return false;
        }

        return $this->hasAnyPermission($modulePermissions[$module]);
    }

    /**
     * Get user's accessible modules.
     */
    public function getAccessibleModules()
    {
        $modules = [];
        $allModules = [
            'employees' => 'Employee Management',
            'attendance' => 'Attendance Management', 
            'leaves' => 'Leave Management',
            'loans' => 'Loan Management',
            'medical' => 'Medical Records',
            'warnings' => 'Warning Management',
            'users' => 'User Management',
            'roles' => 'Role Management',
            'reports' => 'Reports',
            'settings' => 'Settings',
        ];

        foreach ($allModules as $module => $title) {
            if ($this->canAccess($module)) {
                $modules[$module] = $title;
            }
        }

        return $modules;
    }
}