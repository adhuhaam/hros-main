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
            RoleSeeder::class,
            UserSeeder::class,
            LeaveTypeSeeder::class,
            ProjectSeeder::class,
            TestDataSeeder::class, // Only runs in local/testing environments
        ]);
    }
}
