<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'role_name',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get users with this role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get permissions for this role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission($permission)
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    /**
     * Add a permission to this role.
     */
    public function addPermission($permission)
    {
        $permissionModel = Permission::findByName($permission);
        if ($permissionModel && !$this->hasPermission($permission)) {
            $this->permissions()->attach($permissionModel->id);
        }
        return $this;
    }

    /**
     * Remove a permission from this role.
     */
    public function removePermission($permission)
    {
        $permissionModel = Permission::findByName($permission);
        if ($permissionModel) {
            $this->permissions()->detach($permissionModel->id);
        }
        return $this;
    }

    /**
     * Set multiple permissions for this role.
     */
    public function setPermissions(array $permissionNames)
    {
        // Get permission IDs
        $permissionIds = Permission::whereIn('name', $permissionNames)->pluck('id')->toArray();
        
        // Sync permissions (this will remove old ones and add new ones)
        $this->permissions()->sync($permissionIds);
        
        return $this;
    }

    /**
     * Get all permission names for this role.
     */
    public function getPermissionNames()
    {
        return $this->permissions()->pluck('name')->toArray();
    }

    /**
     * Get all available permissions in the system.
     */
    public static function getAllPermissions()
    {
        return Permission::getAllNames();
    }

    /**
     * Get permissions grouped by category.
     */
    public static function getGroupedPermissions()
    {
        return Permission::getGroupedByModule();
    }

    /**
     * Get all available permissions as array.
     */
    public static function getAllPermissionsArray()
    {
        return Permission::active()->get()->mapWithKeys(function ($permission) {
            return [$permission->name => $permission->display_name];
        })->toArray();
    }

    /**
     * Get permissions for a specific module.
     */
    public function getPermissionsByModule($module)
    {
        return $this->permissions()->where('module', $module)->get();
    }

    /**
     * Check if role has any permission from a list.
     */
    public function hasAnyPermission(array $permissions)
    {
        return $this->permissions()->whereIn('name', $permissions)->exists();
    }

    /**
     * Check if role has all permissions from a list.
     */
    public function hasAllPermissions(array $permissions)
    {
        $rolePermissions = $this->permissions()->pluck('name')->toArray();
        return count(array_intersect($permissions, $rolePermissions)) === count($permissions);
    }

    /**
     * Get roles with a specific permission.
     */
    public static function withPermission($permission)
    {
        return self::whereHas('permissions', function ($query) use ($permission) {
            $query->where('name', $permission);
        });
    }

    /**
     * Get roles with any of the given permissions.
     */
    public static function withAnyPermission(array $permissions)
    {
        return self::whereHas('permissions', function ($query) use ($permissions) {
            $query->whereIn('name', $permissions);
        });
    }

    /**
     * Get roles with all of the given permissions.
     */
    public static function withAllPermissions(array $permissions)
    {
        return self::whereHas('permissions', function ($query) use ($permissions) {
            $query->whereIn('name', $permissions);
        }, '>=', count($permissions));
    }

    /**
     * Create default roles with permissions.
     */
    public static function createDefaultRoles()
    {
        $modules = config('modules');
        
        // Create Admin Role with all permissions
        $adminRole = self::updateOrCreate(
            ['role_name' => 'Admin'],
            ['description' => 'System Administrator with full access to all modules and features']
        );

        // Get all permission IDs for admin
        $allPermissionIds = Permission::active()->pluck('id')->toArray();
        $adminRole->permissions()->sync($allPermissionIds);

        // Create other roles with specific permissions
        $rolePermissions = [
            'HR Manager' => [
                'employees.view', 'employees.create', 'employees.edit', 'employees.export',
                'leaves.view', 'leaves.create', 'leaves.edit', 'leaves.approve', 'leaves.reject',
                'attendance.view', 'attendance.edit', 'attendance.approve', 'attendance.report', 'attendance.export',
                'warnings.view', 'warnings.create', 'warnings.edit',
                'documents.view', 'documents.create', 'documents.edit',
                'holidays.view', 'holidays.create', 'holidays.edit',
                'notices.view', 'notices.create', 'notices.edit',
                'reports.employee', 'reports.attendance', 'reports.leave',
            ],
            'HR Officer' => [
                'employees.view', 'employees.create', 'employees.edit',
                'leaves.view', 'leaves.create', 'leaves.edit',
                'attendance.view', 'attendance.edit', 'attendance.report',
                'warnings.view', 'warnings.create',
                'documents.view', 'documents.create',
                'holidays.view',
                'notices.view',
            ],
            'Finance Manager' => [
                'employees.view',
                'loans.view', 'loans.create', 'loans.edit', 'loans.approve', 'loans.reject',
                'attendance.view', 'attendance.report',
                'reports.employee', 'reports.loan', 'reports.payroll',
            ],
            'Finance Officer' => [
                'employees.view',
                'loans.view', 'loans.create', 'loans.edit',
                'attendance.view',
                'reports.loan',
            ],
            'Project Manager' => [
                'employees.view',
                'leaves.view', 'leaves.approve', 'leaves.reject',
                'attendance.view', 'attendance.approve',
                'reports.employee', 'reports.attendance', 'reports.leave',
            ],
            'Team Leader' => [
                'employees.view',
                'leaves.view', 'leaves.approve',
                'attendance.view', 'attendance.approve',
                'reports.attendance', 'reports.leave',
            ],
            'Employee' => [
                'attendance.check_in', 'attendance.check_out',
                'leaves.create',
                'loans.create',
            ],
            'Contractor' => [
                'attendance.check_in', 'attendance.check_out',
                'leaves.create',
            ],
            'Intern' => [
                'attendance.check_in', 'attendance.check_out',
                'leaves.create',
            ],
            'Temporary' => [
                'attendance.check_in', 'attendance.check_out',
            ],
            'Consultant' => [
                'attendance.check_in', 'attendance.check_out',
                'leaves.create',
            ],
            'Guest' => [],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = self::updateOrCreate(
                ['role_name' => $roleName],
                ['description' => self::getDefaultDescription($roleName)]
            );

            if (!empty($permissions)) {
                $permissionIds = Permission::whereIn('name', $permissions)->pluck('id')->toArray();
                $role->permissions()->sync($permissionIds);
            }
        }
    }

    /**
     * Get default description for a role.
     */
    private static function getDefaultDescription($roleName)
    {
        $descriptions = [
            'HR Manager' => 'Human Resources Manager with access to employee management and HR functions',
            'HR Officer' => 'Human Resources Officer with limited access to HR functions',
            'Finance Manager' => 'Finance Manager with access to financial functions and loan management',
            'Finance Officer' => 'Finance Officer with limited access to financial functions',
            'Project Manager' => 'Project Manager with access to team management functions',
            'Team Leader' => 'Team Leader with access to team management functions',
            'Employee' => 'Regular employee with basic access to personal functions',
            'Contractor' => 'Contractor with limited access to basic functions',
            'Intern' => 'Intern with basic access to attendance and leave functions',
            'Temporary' => 'Temporary staff with minimal access',
            'Consultant' => 'Consultant with basic access to attendance and leave functions',
            'Guest' => 'Guest user with no access to system functions',
        ];

        return $descriptions[$roleName] ?? 'No description available';
    }
} 