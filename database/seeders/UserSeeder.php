<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => 1,
                'emp_no' => '1001',
                'username' => 'admin',
                'staff_name' => 'System Administrator',
                'des' => 'System Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'emp_no' => '1002',
                'username' => 'hr_manager',
                'staff_name' => 'HR Manager',
                'des' => 'Human Resources Manager',
                'email' => 'hr@example.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'emp_no' => '1003',
                'username' => 'finance',
                'staff_name' => 'Finance Manager',
                'des' => 'Finance Manager',
                'email' => 'finance@example.com',
                'password' => Hash::make('password'),
                'role_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
} 