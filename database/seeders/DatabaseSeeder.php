<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call seeders in order (dependencies first)
        $this->call([
            PermissionSeeder::class, // Create permissions first
            RoleSeeder::class, // Then create roles with permissions
            UserSeeder::class, // Create users with roles
            BasicDataSeeder::class, // Create real data without factories
            LeaveTypeSeeder::class,
            ProjectSeeder::class,
            AttendanceSeeder::class, // Create employee shifts and attendance records
        ]);
    }
}
