<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = config('modules');
        $sortOrder = 1;

        foreach ($modules as $moduleKey => $module) {
            foreach ($module['permissions'] as $permissionName => $description) {
                Permission::updateOrCreate(
                    ['name' => $permissionName],
                    [
                        'display_name' => $description,
                        'description' => "Permission to {$description}",
                        'module' => $moduleKey,
                        'action' => $this->extractAction($permissionName),
                        'is_active' => true,
                        'sort_order' => $sortOrder++,
                    ]
                );
            }
        }

        // Add system permissions
        $systemPermissions = [
            'system.admin' => [
                'display_name' => 'System Administrator',
                'description' => 'Full system access and administration',
                'module' => 'system',
                'action' => 'admin',
            ],
            'system.settings' => [
                'display_name' => 'System Settings',
                'description' => 'Access to system settings and configuration',
                'module' => 'system',
                'action' => 'settings',
            ],
            'system.logs' => [
                'display_name' => 'System Logs',
                'description' => 'Access to system logs and audit trails',
                'module' => 'system',
                'action' => 'logs',
            ],
        ];

        foreach ($systemPermissions as $permissionName => $permissionData) {
            Permission::updateOrCreate(
                ['name' => $permissionName],
                array_merge($permissionData, [
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ])
            );
        }

        $this->command->info('Permissions seeded successfully!');
        $this->command->info('Total permissions created: ' . Permission::count());
    }

    /**
     * Extract action from permission name.
     */
    private function extractAction($permissionName)
    {
        $parts = explode('.', $permissionName);
        return end($parts);
    }
}
