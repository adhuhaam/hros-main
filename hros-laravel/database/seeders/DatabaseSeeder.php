<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@hros.com',
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'role' => 'Admin',
            'status' => 'active',
        ]);

        // Create HR Manager
        $hrManager = User::create([
            'name' => 'HR Manager',
            'email' => 'hr@hros.com',
            'username' => 'hrmanager',
            'password' => Hash::make('password123'),
            'role' => 'HR Manager',
            'status' => 'active',
        ]);

        // Create Information Officer
        $infoOfficer = User::create([
            'name' => 'Information Officer',
            'email' => 'info@hros.com',
            'username' => 'infoofficer',
            'password' => Hash::make('password123'),
            'role' => 'Information Officer',
            'status' => 'active',
        ]);

        // Create Leave Officer
        $leaveOfficer = User::create([
            'name' => 'Leave Officer',
            'email' => 'leave@hros.com',
            'username' => 'leaveofficer',
            'password' => Hash::make('password123'),
            'role' => 'Leave Officer',
            'status' => 'active',
        ]);

        // Create Payroll Officer
        $payrollOfficer = User::create([
            'name' => 'Payroll Officer',
            'email' => 'payroll@hros.com',
            'username' => 'payrollofficer',
            'password' => Hash::make('password123'),
            'role' => 'Payroll Officer',
            'status' => 'active',
        ]);

        // Create sample employees
        $employees = [
            [
                'employee_id' => 'EMP001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@company.com',
                'phone' => '+960 1234567',
                'date_of_birth' => '1990-05-15',
                'gender' => 'Male',
                'nationality' => 'Maldives',
                'department' => 'IT',
                'position' => 'Software Developer',
                'employment_status' => 'Active',
                'hire_date' => '2023-01-15',
                'salary' => 25000.00,
                'user_id' => null,
            ],
            [
                'employee_id' => 'EMP002',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@company.com',
                'phone' => '+960 2345678',
                'date_of_birth' => '1988-08-22',
                'gender' => 'Female',
                'nationality' => 'India',
                'department' => 'HR',
                'position' => 'HR Officer',
                'employment_status' => 'Active',
                'hire_date' => '2023-02-01',
                'salary' => 22000.00,
                'user_id' => null,
            ],
            [
                'employee_id' => 'EMP003',
                'first_name' => 'Ahmed',
                'last_name' => 'Hassan',
                'email' => 'ahmed.hassan@company.com',
                'phone' => '+960 3456789',
                'date_of_birth' => '1992-03-10',
                'gender' => 'Male',
                'nationality' => 'Maldives',
                'department' => 'Finance',
                'position' => 'Accountant',
                'employment_status' => 'Active',
                'hire_date' => '2023-03-10',
                'salary' => 20000.00,
                'user_id' => null,
            ],
            [
                'employee_id' => 'EMP004',
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@company.com',
                'phone' => '+960 4567890',
                'date_of_birth' => '1995-11-05',
                'gender' => 'Female',
                'nationality' => 'Sri Lanka',
                'department' => 'Marketing',
                'position' => 'Marketing Specialist',
                'employment_status' => 'Active',
                'hire_date' => '2023-04-15',
                'salary' => 18000.00,
                'user_id' => null,
            ],
            [
                'employee_id' => 'EMP005',
                'first_name' => 'Mohammed',
                'last_name' => 'Ali',
                'email' => 'mohammed.ali@company.com',
                'phone' => '+960 5678901',
                'date_of_birth' => '1987-12-20',
                'gender' => 'Male',
                'nationality' => 'Bangladesh',
                'department' => 'Operations',
                'position' => 'Operations Manager',
                'employment_status' => 'Active',
                'hire_date' => '2023-05-01',
                'salary' => 30000.00,
                'user_id' => null,
            ],
        ];

        foreach ($employees as $employeeData) {
            Employee::create($employeeData);
        }

        // Create a regular staff user with employee record
        $staffUser = User::create([
            'name' => 'Regular Staff',
            'email' => 'staff@hros.com',
            'username' => 'staff',
            'password' => Hash::make('password123'),
            'role' => 'Other Staff',
            'status' => 'active',
        ]);

        $staffEmployee = Employee::create([
            'employee_id' => 'EMP006',
            'first_name' => 'Regular',
            'last_name' => 'Staff',
            'email' => 'staff@hros.com',
            'phone' => '+960 6789012',
            'date_of_birth' => '1993-07-12',
            'gender' => 'Male',
            'nationality' => 'Maldives',
            'department' => 'Customer Service',
            'position' => 'Customer Service Representative',
            'employment_status' => 'Active',
            'hire_date' => '2023-06-01',
            'salary' => 15000.00,
            'user_id' => $staffUser->id,
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin credentials: admin / password123');
        $this->command->info('HR Manager credentials: hrmanager / password123');
        $this->command->info('Information Officer credentials: infoofficer / password123');
        $this->command->info('Leave Officer credentials: leaveofficer / password123');
        $this->command->info('Payroll Officer credentials: payrollofficer / password123');
        $this->command->info('Staff credentials: staff / password123');
    }
}