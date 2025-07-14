<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'name' => 'Super Admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'HR Manager', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'HR Officer', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Finance Manager', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Finance Officer', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'name' => 'Project Manager', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'name' => 'Team Leader', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'name' => 'Employee', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'name' => 'Contractor', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'name' => 'Intern', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'name' => 'Temporary', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'name' => 'Consultant', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'name' => 'Guest', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('roles')->insert($roles);
    }
} 