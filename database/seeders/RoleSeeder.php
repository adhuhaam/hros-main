<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Role with all permissions
        $adminRole = Role::updateOrCreate(
            ['role_name' => 'Admin'],
            [
                'description' => 'System Administrator with full access to all modules and features',
            ]
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
            $role = Role::updateOrCreate(
                ['role_name' => $roleName],
                ['description' => $this->getDefaultDescription($roleName)]
            );

            if (!empty($permissions)) {
                $permissionIds = Permission::whereIn('name', $permissions)->pluck('id')->toArray();
                $role->permissions()->sync($permissionIds);
            }
        }

        $this->command->info('Roles seeded successfully!');
    }

    /**
     * Get default description for a role.
     */
    private function getDefaultDescription($roleName)
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