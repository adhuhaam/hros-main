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
            ['id' => 1, 'role_name' => 'Super Admin', 'description' => 'Super admin role', 'created_at' => now(), 'permissions' => null],
            ['id' => 2, 'role_name' => 'Admin', 'description' => 'Admin role', 'created_at' => now(), 'permissions' => null],
            ['id' => 3, 'role_name' => 'HR Manager', 'description' => 'HR Manager role', 'created_at' => now(), 'permissions' => null],
            ['id' => 4, 'role_name' => 'HR Officer', 'description' => 'HR Officer role', 'created_at' => now(), 'permissions' => null],
            ['id' => 5, 'role_name' => 'Finance Manager', 'description' => 'Finance Manager role', 'created_at' => now(), 'permissions' => null],
            ['id' => 6, 'role_name' => 'Finance Officer', 'description' => 'Finance Officer role', 'created_at' => now(), 'permissions' => null],
            ['id' => 7, 'role_name' => 'Project Manager', 'description' => 'Project Manager role', 'created_at' => now(), 'permissions' => null],
            ['id' => 8, 'role_name' => 'Team Leader', 'description' => 'Team Leader role', 'created_at' => now(), 'permissions' => null],
            ['id' => 9, 'role_name' => 'Employee', 'description' => 'Employee role', 'created_at' => now(), 'permissions' => null],
            ['id' => 10, 'role_name' => 'Contractor', 'description' => 'Contractor role', 'created_at' => now(), 'permissions' => null],
            ['id' => 11, 'role_name' => 'Intern', 'description' => 'Intern role', 'created_at' => now(), 'permissions' => null],
            ['id' => 12, 'role_name' => 'Temporary', 'description' => 'Temporary role', 'created_at' => now(), 'permissions' => null],
            ['id' => 13, 'role_name' => 'Consultant', 'description' => 'Consultant role', 'created_at' => now(), 'permissions' => null],
            ['id' => 14, 'role_name' => 'Guest', 'description' => 'Guest role', 'created_at' => now(), 'permissions' => null],
        ];

        DB::table('roles')->insert($roles);
    }
} 