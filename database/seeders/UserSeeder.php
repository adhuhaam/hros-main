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
                'emp_no' => 'EMP001',
                'username' => 'admin',
                'staff_name' => 'System Administrator',
                'des' => 'Administrator',
                'email' => 'admin@company.com',
                'password' => 'admin123', // Will be hashed automatically
                'role_id' => $adminRole->id,
            ]);
        }

        // Create HR Manager user
        if (!User::where('username', 'hrmanager')->exists()) {
            User::create([
                'id' => 2,
                'emp_no' => 'EMP002',
                'username' => 'hrmanager',
                'staff_name' => 'HR Manager',
                'des' => 'Human Resources Manager',
                'email' => 'hrmanager@company.com',
                'password' => 'hr123',
                'role_id' => $hrManagerRole->id,
            ]);
        }

        // Create HR Officer user
        if (!User::where('username', 'hrofficer')->exists()) {
            User::create([
                'id' => 3,
                'emp_no' => 'EMP003',
                'username' => 'hrofficer',
                'staff_name' => 'HR Officer',
                'des' => 'Human Resources Officer',
                'email' => 'hrofficer@company.com',
                'password' => 'hr123',
                'role_id' => $hrOfficerRole->id,
            ]);
        }

        // Create Finance Manager user
        if (!User::where('username', 'financemanager')->exists()) {
            User::create([
                'id' => 4,
                'emp_no' => 'EMP004',
                'username' => 'financemanager',
                'staff_name' => 'Finance Manager',
                'des' => 'Finance Manager',
                'email' => 'finance@company.com',
                'password' => 'finance123',
                'role_id' => $financeManagerRole->id,
            ]);
        }

        // Create sample employee user
        if (!User::where('username', 'employee')->exists()) {
            User::create([
                'id' => 5,
                'emp_no' => 'EMP005',
                'username' => 'employee',
                'staff_name' => 'John Doe',
                'des' => 'Software Developer',
                'email' => 'employee@company.com',
                'password' => 'employee123',
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
                'emp_no' => 'EMP010',
                'username' => 'projectmanager',
                'staff_name' => 'Alice Johnson',
                'des' => 'Project Manager',
                'email' => 'alice@company.com',
                'role_name' => 'Project Manager',
            ],
            [
                'emp_no' => 'EMP011',
                'username' => 'teamleader',
                'staff_name' => 'Bob Smith',
                'des' => 'Team Leader',
                'email' => 'bob@company.com',
                'role_name' => 'Team Leader',
            ],
            [
                'emp_no' => 'EMP012',
                'username' => 'developer1',
                'staff_name' => 'Carol Williams',
                'des' => 'Senior Developer',
                'email' => 'carol@company.com',
                'role_name' => 'Employee',
            ],
            [
                'emp_no' => 'EMP013',
                'username' => 'developer2',
                'staff_name' => 'David Brown',
                'des' => 'Junior Developer',
                'email' => 'david@company.com',
                'role_name' => 'Employee',
            ],
            [
                'emp_no' => 'EMP014',
                'username' => 'financeofficer',
                'staff_name' => 'Emma Davis',
                'des' => 'Finance Officer',
                'email' => 'emma@company.com',
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
                        'password' => 'password123', // Default password for test users
                        'role_id' => $role->id,
                    ]);
                }
            }
        }
    }
} 