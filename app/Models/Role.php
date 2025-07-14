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
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
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
     * Check if role has a specific permission.
     */
    public function hasPermission($permission)
    {
        if (!$this->permissions) {
            return false;
        }

        return in_array($permission, $this->permissions);
    }

    /**
     * Add a permission to this role.
     */
    public function addPermission($permission)
    {
        $permissions = $this->permissions ?? [];
        
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->permissions = $permissions;
            $this->save();
        }
        
        return $this;
    }

    /**
     * Remove a permission from this role.
     */
    public function removePermission($permission)
    {
        $permissions = $this->permissions ?? [];
        
        if (($key = array_search($permission, $permissions)) !== false) {
            unset($permissions[$key]);
            $this->permissions = array_values($permissions);
            $this->save();
        }
        
        return $this;
    }

    /**
     * Set multiple permissions for this role.
     */
    public function setPermissions(array $permissions)
    {
        $this->permissions = $permissions;
        $this->save();
        
        return $this;
    }

    /**
     * Get all available permissions in the system.
     */
    public static function getAllPermissions()
    {
        return [
            // Dashboard permissions
            'dashboard.admin',
            'dashboard.hr_manager',
            'dashboard.hr_officer',
            'dashboard.finance_manager',
            'dashboard.finance_officer',
            'dashboard.project_manager',
            'dashboard.team_leader',
            'dashboard.employee',
            'dashboard.contractor',
            'dashboard.intern',
            'dashboard.temporary',
            'dashboard.consultant',
            'dashboard.guest',

            // Employee management
            'employees.view',
            'employees.create',
            'employees.edit',
            'employees.delete',
            'employees.export',
            'employees.import',

            // Attendance management
            'attendance.view',
            'attendance.create',
            'attendance.edit',
            'attendance.delete',
            'attendance.approve',
            'attendance.bulk_approve',
            'attendance.export',
            'attendance.import',
            'attendance.report',
            'attendance.check_in',
            'attendance.check_out',

            // Leave management
            'leaves.view',
            'leaves.create',
            'leaves.edit',
            'leaves.delete',
            'leaves.approve',
            'leaves.reject',
            'leaves.export',
            'leaves.report',

            // Loan management
            'loans.view',
            'loans.create',
            'loans.edit',
            'loans.delete',
            'loans.approve',
            'loans.reject',
            'loans.pay_installment',
            'loans.export',
            'loans.report',

            // Medical records
            'medical.view',
            'medical.create',
            'medical.edit',
            'medical.delete',
            'medical.export',

            // Warning management
            'warnings.view',
            'warnings.create',
            'warnings.edit',
            'warnings.delete',
            'warnings.acknowledge',

            // User management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.reset_password',

            // Role management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.assign_permissions',

            // Reports
            'reports.view',
            'reports.employee',
            'reports.attendance',
            'reports.leave',
            'reports.loan',
            'reports.payroll',

            // Settings
            'settings.view',
            'settings.edit',
            'settings.system',

            // Special permissions
            'system.admin',
            'system.backup',
            'system.maintenance',
        ];
    }

    /**
     * Get permissions grouped by category.
     */
    public static function getGroupedPermissions()
    {
        return [
            'Dashboard' => [
                'dashboard.admin' => 'Admin Dashboard',
                'dashboard.hr_manager' => 'HR Manager Dashboard',
                'dashboard.hr_officer' => 'HR Officer Dashboard',
                'dashboard.finance_manager' => 'Finance Manager Dashboard',
                'dashboard.finance_officer' => 'Finance Officer Dashboard',
                'dashboard.project_manager' => 'Project Manager Dashboard',
                'dashboard.team_leader' => 'Team Leader Dashboard',
                'dashboard.employee' => 'Employee Dashboard',
                'dashboard.contractor' => 'Contractor Dashboard',
                'dashboard.intern' => 'Intern Dashboard',
                'dashboard.temporary' => 'Temporary Dashboard',
                'dashboard.consultant' => 'Consultant Dashboard',
                'dashboard.guest' => 'Guest Dashboard',
            ],
            'Employee Management' => [
                'employees.view' => 'View Employees',
                'employees.create' => 'Create Employees',
                'employees.edit' => 'Edit Employees',
                'employees.delete' => 'Delete Employees',
                'employees.export' => 'Export Employees',
                'employees.import' => 'Import Employees',
            ],
            'Attendance Management' => [
                'attendance.view' => 'View Attendance',
                'attendance.create' => 'Create Attendance',
                'attendance.edit' => 'Edit Attendance',
                'attendance.delete' => 'Delete Attendance',
                'attendance.approve' => 'Approve Attendance',
                'attendance.bulk_approve' => 'Bulk Approve Attendance',
                'attendance.export' => 'Export Attendance',
                'attendance.import' => 'Import Attendance',
                'attendance.report' => 'View Attendance Reports',
                'attendance.check_in' => 'Check In',
                'attendance.check_out' => 'Check Out',
            ],
            'Leave Management' => [
                'leaves.view' => 'View Leaves',
                'leaves.create' => 'Create Leaves',
                'leaves.edit' => 'Edit Leaves',
                'leaves.delete' => 'Delete Leaves',
                'leaves.approve' => 'Approve Leaves',
                'leaves.reject' => 'Reject Leaves',
                'leaves.export' => 'Export Leaves',
                'leaves.report' => 'View Leave Reports',
            ],
            'Loan Management' => [
                'loans.view' => 'View Loans',
                'loans.create' => 'Create Loans',
                'loans.edit' => 'Edit Loans',
                'loans.delete' => 'Delete Loans',
                'loans.approve' => 'Approve Loans',
                'loans.reject' => 'Reject Loans',
                'loans.pay_installment' => 'Pay Installments',
                'loans.export' => 'Export Loans',
                'loans.report' => 'View Loan Reports',
            ],
            'Medical Records' => [
                'medical.view' => 'View Medical Records',
                'medical.create' => 'Create Medical Records',
                'medical.edit' => 'Edit Medical Records',
                'medical.delete' => 'Delete Medical Records',
                'medical.export' => 'Export Medical Records',
            ],
            'Warning Management' => [
                'warnings.view' => 'View Warnings',
                'warnings.create' => 'Create Warnings',
                'warnings.edit' => 'Edit Warnings',
                'warnings.delete' => 'Delete Warnings',
                'warnings.acknowledge' => 'Acknowledge Warnings',
            ],
            'User Management' => [
                'users.view' => 'View Users',
                'users.create' => 'Create Users',
                'users.edit' => 'Edit Users',
                'users.delete' => 'Delete Users',
                'users.reset_password' => 'Reset Passwords',
            ],
            'Role Management' => [
                'roles.view' => 'View Roles',
                'roles.create' => 'Create Roles',
                'roles.edit' => 'Edit Roles',
                'roles.delete' => 'Delete Roles',
                'roles.assign_permissions' => 'Assign Permissions',
            ],
            'Reports' => [
                'reports.view' => 'View Reports',
                'reports.employee' => 'Employee Reports',
                'reports.attendance' => 'Attendance Reports',
                'reports.leave' => 'Leave Reports',
                'reports.loan' => 'Loan Reports',
                'reports.payroll' => 'Payroll Reports',
            ],
            'Settings' => [
                'settings.view' => 'View Settings',
                'settings.edit' => 'Edit Settings',
                'settings.system' => 'System Settings',
            ],
            'System Administration' => [
                'system.admin' => 'System Administrator',
                'system.backup' => 'System Backup',
                'system.maintenance' => 'System Maintenance',
            ],
        ];
    }

    /**
     * Create default roles with permissions.
     */
    public static function createDefaultRoles()
    {
        $defaultRoles = [
            'Admin' => [
                'description' => 'System Administrator with full access',
                'permissions' => self::getAllPermissions(),
            ],
            'HR Manager' => [
                'description' => 'HR Manager with comprehensive HR access',
                'permissions' => [
                    'dashboard.hr_manager',
                    'employees.view', 'employees.create', 'employees.edit', 'employees.export', 'employees.import',
                    'attendance.view', 'attendance.create', 'attendance.edit', 'attendance.approve', 'attendance.bulk_approve', 'attendance.export', 'attendance.report',
                    'leaves.view', 'leaves.create', 'leaves.edit', 'leaves.approve', 'leaves.reject', 'leaves.export', 'leaves.report',
                    'loans.view', 'loans.create', 'loans.edit', 'loans.approve', 'loans.reject', 'loans.export', 'loans.report',
                    'medical.view', 'medical.create', 'medical.edit', 'medical.export',
                    'warnings.view', 'warnings.create', 'warnings.edit',
                    'users.view', 'users.create', 'users.edit', 'users.reset_password',
                    'reports.view', 'reports.employee', 'reports.attendance', 'reports.leave', 'reports.loan',
                ],
            ],
            'HR Officer' => [
                'description' => 'HR Officer with employee and leave management',
                'permissions' => [
                    'dashboard.hr_officer',
                    'employees.view', 'employees.create', 'employees.edit', 'employees.export',
                    'attendance.view', 'attendance.create', 'attendance.edit', 'attendance.export', 'attendance.report',
                    'leaves.view', 'leaves.create', 'leaves.edit', 'leaves.export', 'leaves.report',
                    'medical.view', 'medical.create', 'medical.edit',
                    'warnings.view', 'warnings.create', 'warnings.edit',
                    'reports.view', 'reports.employee', 'reports.attendance', 'reports.leave',
                ],
            ],
            'Finance Manager' => [
                'description' => 'Finance Manager with financial operations access',
                'permissions' => [
                    'dashboard.finance_manager',
                    'employees.view',
                    'attendance.view', 'attendance.report',
                    'loans.view', 'loans.create', 'loans.edit', 'loans.approve', 'loans.reject', 'loans.pay_installment', 'loans.export', 'loans.report',
                    'reports.view', 'reports.loan', 'reports.payroll',
                ],
            ],
            'Finance Officer' => [
                'description' => 'Finance Officer with limited financial access',
                'permissions' => [
                    'dashboard.finance_officer',
                    'employees.view',
                    'loans.view', 'loans.create', 'loans.edit', 'loans.export', 'loans.report',
                    'reports.view', 'reports.loan', 'reports.payroll',
                ],
            ],
            'Project Manager' => [
                'description' => 'Project Manager with team management access',
                'permissions' => [
                    'dashboard.project_manager',
                    'employees.view',
                    'attendance.view', 'attendance.approve', 'attendance.export', 'attendance.report',
                    'leaves.view', 'leaves.approve', 'leaves.reject', 'leaves.export', 'leaves.report',
                    'warnings.view', 'warnings.create', 'warnings.edit',
                    'reports.view', 'reports.employee', 'reports.attendance', 'reports.leave',
                ],
            ],
            'Team Leader' => [
                'description' => 'Team Leader with limited team access',
                'permissions' => [
                    'dashboard.team_leader',
                    'employees.view',
                    'attendance.view', 'attendance.export', 'attendance.report',
                    'leaves.view', 'leaves.export', 'leaves.report',
                    'reports.view', 'reports.attendance', 'reports.leave',
                ],
            ],
            'Employee' => [
                'description' => 'Regular Employee with self-service access',
                'permissions' => [
                    'dashboard.employee',
                    'attendance.check_in', 'attendance.check_out',
                    'leaves.view', 'leaves.create',
                    'loans.view', 'loans.create',
                ],
            ],
        ];

        foreach ($defaultRoles as $roleName => $roleData) {
            self::updateOrCreate(
                ['role_name' => $roleName],
                [
                    'description' => $roleData['description'],
                    'permissions' => $roleData['permissions'],
                ]
            );
        }
    }
} 