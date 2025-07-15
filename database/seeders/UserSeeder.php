<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist first
        $adminRole = Role::where('role_name', 'Admin')->first();
        $hrManagerRole = Role::where('role_name', 'HR Manager')->first();
        $hrOfficerRole = Role::where('role_name', 'HR Officer')->first();
        $financeManagerRole = Role::where('role_name', 'Finance Manager')->first();
        $employeeRole = Role::where('role_name', 'Employee')->first();

        // Create default admin user
        if (!User::where('username', 'admin')->exists()) {
            User::create([
                'id' => 1,
                'emp_no' => '001',
                'username' => 'admin',
                'staff_name' => 'System Administrator',
                'des' => 'Administrator',
                'email' => 'adhuham@rcc.com.mv',
                'password' => '123456', // Will be hashed automatically
                'role_id' => $adminRole->id,
            ]);
        }

        // Create HR Manager user
        if (!User::where('username', 'hrmanager')->exists()) {
            User::create([
                'id' => 2,
                'emp_no' => '002',
                'username' => 'hrmanager',
                'staff_name' => 'HR Manager',
                'des' => 'Human Resources Manager',
                'email' => 'hrmanager@rcc.com.mv',
                'password' => '123456',
                'role_id' => $hrManagerRole->id,
            ]);
        }

        // Create HR Officer user
        if (!User::where('username', 'hrofficer')->exists()) {
            User::create([
                'id' => 3,
                'emp_no' => '003',
                'username' => 'hrofficer',
                'staff_name' => 'HR Officer',
                'des' => 'Human Resources Officer',
                'email' => 'hrofficer@rcc.com.mv',
                'password' => '123456',
                'role_id' => $hrOfficerRole->id,
            ]);
        }

        // Create Finance Manager user
        if (!User::where('username', 'financemanager')->exists()) {
            User::create([
                'id' => 4,
                'emp_no' => '004',
                'username' => 'financemanager',
                'staff_name' => 'Finance Manager',
                'des' => 'Finance Manager',
                'email' => 'finance@rcc.com.mv',
                'password' => '123456',
                'role_id' => $financeManagerRole->id,
            ]);
        }

        // Create sample employee user
        if (!User::where('username', 'employee')->exists()) {
            User::create([
                'id' => 5,
                'emp_no' => '005',
                'username' => 'employee',
                'staff_name' => 'John Doe',
                'des' => 'Software Developer',
                'email' => 'employee@rcc.com.mv',
                'password' => '123456',
                'role_id' => $employeeRole->id,
            ]);
        }

        // Create additional test users if in development environment
        if (app()->environment('local')) {
            $this->createTestUsers();
        }
    }

    /**
     * Create additional test users for development.
     */
    private function createTestUsers()
    {
        $roles = Role::all();
        $testUsers = [
            [
                'emp_no' => '010',
                'username' => 'projectmanager',
                'staff_name' => 'Alice Johnson',
                'des' => 'Project Manager',
                'email' => 'alice@rcc.com.mv',
                'role_name' => 'Project Manager',
            ],
            [
                'emp_no' => '011',
                'username' => 'teamleader',
                'staff_name' => 'Bob Smith',
                'des' => 'Team Leader',
                'email' => 'bob@rcc.com.mv',
                'role_name' => 'Team Leader',
            ],
            [
                'emp_no' => '012',
                'username' => 'developer1',
                'staff_name' => 'Carol Williams',
                'des' => 'Senior Developer',
                'email' => 'carol@rcc.com.mv',
                'role_name' => 'Employee',
            ],
            [
                'emp_no' => '013',
                'username' => 'developer2',
                'staff_name' => 'David Brown',
                'des' => 'Junior Developer',
                'email' => 'david@rcc.com.mv',
                'role_name' => 'Employee',
            ],
            [
                'emp_no' => '014',
                'username' => 'financeofficer',
                'staff_name' => 'Emma Davis',
                'des' => 'Finance Officer',
                'email' => 'emma@rcc.com.mv',
                'role_name' => 'Finance Officer',
            ],
        ];

        foreach ($testUsers as $userData) {
            if (!User::where('username', $userData['username'])->exists()) {
                $role = $roles->where('role_name', $userData['role_name'])->first();
                
                if ($role) {
                    User::create([
                        'emp_no' => $userData['emp_no'],
                        'username' => $userData['username'],
                        'staff_name' => $userData['staff_name'],
                        'des' => $userData['des'],
                        'email' => $userData['email'],
                        'password' => '123456', // Default password for test users
                        'role_id' => $role->id,
                    ]);
                }
            }
        }
    }
} 