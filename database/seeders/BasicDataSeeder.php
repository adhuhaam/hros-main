<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\Project;
use Carbon\Carbon;

class BasicDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding basic data...');

        // Create basic users
        $this->createUsers();
        
        // Create basic employees
        $this->createEmployees();
        
        // Create leave types
        $this->createLeaveTypes();
        
        // Create projects
        $this->createProjects();

        $this->command->info('Basic data seeding completed!');
    }

    /**
     * Create basic users
     */
    private function createUsers(): void
    {
        $users = [
            [
                'emp_no' => '0001',
                'username' => 'admin',
                'staff_name' => 'System Administrator',
                'des' => 'System Administrator',
                'email' => 'admin@rcc.com.mv',
                'password' => bcrypt('password'),
                'role_id' => 1,
            ],
            [
                'emp_no' => '0002',
                'username' => 'hr.manager',
                'staff_name' => 'HR Manager',
                'des' => 'HR Manager',
                'email' => 'hr@rcc.com.mv',
                'password' => bcrypt('password'),
                'role_id' => 2,
            ],
            [
                'emp_no' => '0003',
                'username' => 'project.manager',
                'staff_name' => 'Project Manager',
                'des' => 'Project Manager',
                'email' => 'pm@rcc.com.mv',
                'password' => bcrypt('password'),
                'role_id' => 3,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }

    /**
     * Create basic employees
     */
    private function createEmployees(): void
    {
        $employees = [
            [
                'emp_no' => '0001',
                'name' => 'Ahmed Hassan',
                'gender' => 'Male',
                'designation' => 'System Administrator',
                'department' => 'IT',
                'nationality' => 'Maldives',
                'date_of_join' => '2024-01-01',
                'contact_number' => '+960 1234567',
                'emergency_contact_number' => '+960 7654321',
                'emergency_contact_name' => 'Fatima Hassan',
                'employment_status' => 'Active',
                'emp_email' => 'ahmed@rcc.com.mv',
                'company_email' => 'ahmed.hassan@rcc.com.mv',
                'permanent_address' => 'Male, Maldives',
                'persent_address' => 'Male, Maldives',
                'basic_salary' => 25000.00,
                'salary_currency' => 'MVR',
                'level' => 'senior',
                'company' => 'RASHEED CARPENTRY AND CONSTRUCTION PVT LTD',
            ],
            [
                'emp_no' => '0002',
                'name' => 'Aisha Mohamed',
                'gender' => 'Female',
                'designation' => 'HR Manager',
                'department' => 'HR',
                'nationality' => 'Maldives',
                'date_of_join' => '2024-01-15',
                'contact_number' => '+960 2345678',
                'emergency_contact_number' => '+960 8765432',
                'emergency_contact_name' => 'Mohamed Ali',
                'employment_status' => 'Active',
                'emp_email' => 'aisha@rcc.com.mv',
                'company_email' => 'aisha.mohamed@rcc.com.mv',
                'permanent_address' => 'Male, Maldives',
                'persent_address' => 'Male, Maldives',
                'basic_salary' => 20000.00,
                'salary_currency' => 'MVR',
                'level' => 'senior',
                'company' => 'RASHEED CARPENTRY AND CONSTRUCTION PVT LTD',
            ],
            [
                'emp_no' => '0003',
                'name' => 'Ibrahim Rahman',
                'gender' => 'Male',
                'designation' => 'Project Manager',
                'department' => 'Operations',
                'nationality' => 'Maldives',
                'date_of_join' => '2024-02-01',
                'contact_number' => '+960 3456789',
                'emergency_contact_number' => '+960 9876543',
                'emergency_contact_name' => 'Zara Rahman',
                'employment_status' => 'Active',
                'emp_email' => 'ibrahim@rcc.com.mv',
                'company_email' => 'ibrahim.rahman@rcc.com.mv',
                'permanent_address' => 'Hulhumale, Maldives',
                'persent_address' => 'Hulhumale, Maldives',
                'basic_salary' => 22000.00,
                'salary_currency' => 'MVR',
                'level' => 'senior',
                'company' => 'RASHEED CARPENTRY AND CONSTRUCTION PVT LTD',
            ],
            [
                'emp_no' => '0004',
                'name' => 'Mariam Ali',
                'gender' => 'Female',
                'designation' => 'Accountant',
                'department' => 'Finance',
                'nationality' => 'Maldives',
                'date_of_join' => '2024-02-15',
                'contact_number' => '+960 4567890',
                'emergency_contact_number' => '+960 0987654',
                'emergency_contact_name' => 'Ali Hassan',
                'employment_status' => 'Active',
                'emp_email' => 'mariam@rcc.com.mv',
                'company_email' => 'mariam.ali@rcc.com.mv',
                'permanent_address' => 'Male, Maldives',
                'persent_address' => 'Male, Maldives',
                'basic_salary' => 18000.00,
                'salary_currency' => 'MVR',
                'level' => 'junior',
                'company' => 'RASHEED CARPENTRY AND CONSTRUCTION PVT LTD',
            ],
            [
                'emp_no' => '0005',
                'name' => 'Omar Yusuf',
                'gender' => 'Male',
                'designation' => 'Site Engineer',
                'department' => 'Engineering',
                'nationality' => 'Maldives',
                'date_of_join' => '2024-03-01',
                'contact_number' => '+960 5678901',
                'emergency_contact_number' => '+960 1098765',
                'emergency_contact_name' => 'Layla Yusuf',
                'employment_status' => 'Active',
                'emp_email' => 'omar@rcc.com.mv',
                'company_email' => 'omar.yusuf@rcc.com.mv',
                'permanent_address' => 'Addu City, Maldives',
                'persent_address' => 'Male, Maldives',
                'basic_salary' => 16000.00,
                'salary_currency' => 'MVR',
                'level' => 'junior',
                'company' => 'RASHEED CARPENTRY AND CONSTRUCTION PVT LTD',
            ],
        ];

        foreach ($employees as $employeeData) {
            Employee::firstOrCreate(
                ['emp_no' => $employeeData['emp_no']],
                $employeeData
            );
        }
    }

    /**
     * Create leave types
     */
    private function createLeaveTypes(): void
    {
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'description' => 'Regular annual vacation leave',
                'max_days_per_year' => 30,
                'gender_restriction' => 'None',
                'requires_approval' => true,
            ],
            [
                'name' => 'Sick Leave',
                'description' => 'Medical leave for illness or injury',
                'max_days_per_year' => 15,
                'gender_restriction' => 'None',
                'requires_approval' => false,
            ],
            [
                'name' => 'Emergency Leave',
                'description' => 'Urgent personal or family emergency',
                'max_days_per_year' => 5,
                'gender_restriction' => 'None',
                'requires_approval' => true,
            ],
            [
                'name' => 'Maternity Leave',
                'description' => 'Leave for expecting mothers',
                'max_days_per_year' => 90,
                'gender_restriction' => 'Female',
                'requires_approval' => true,
            ],
            [
                'name' => 'Unpaid Leave',
                'description' => 'Leave without pay',
                'max_days_per_year' => 30,
                'gender_restriction' => 'None',
                'requires_approval' => true,
            ],
        ];

        foreach ($leaveTypes as $leaveTypeData) {
            LeaveType::firstOrCreate(
                ['name' => $leaveTypeData['name']],
                $leaveTypeData
            );
        }
    }

    /**
     * Create projects
     */
    private function createProjects(): void
    {
        $projects = [
            [
                'name' => 'Residential - Luxury Villa Development',
                'description' => 'Complete construction project including planning, execution, and handover',
                'started_date' => Carbon::now()->subMonths(6)->format('Y-m-d'),
                'end_date' => Carbon::now()->addMonths(12)->format('Y-m-d'),
                'status' => 'Active',
                'project_value' => 15000000.00,
                'client' => 'Private Developer Group',
                'images' => null,
            ],
            [
                'name' => 'Commercial - Office Complex Construction',
                'description' => 'Commercial development with retail and office spaces',
                'started_date' => Carbon::now()->subMonths(3)->format('Y-m-d'),
                'end_date' => Carbon::now()->addMonths(18)->format('Y-m-d'),
                'status' => 'Active',
                'project_value' => 25000000.00,
                'client' => 'Ministry of Construction',
                'images' => null,
            ],
            [
                'name' => 'Infrastructure - Road Project',
                'description' => 'Public infrastructure project serving the community',
                'started_date' => Carbon::now()->subMonths(1)->format('Y-m-d'),
                'end_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
                'status' => 'On Hold',
                'project_value' => 8000000.00,
                'client' => 'Male City Council',
                'images' => null,
            ],
        ];

        foreach ($projects as $projectData) {
            Project::firstOrCreate(
                ['name' => $projectData['name']],
                $projectData
            );
        }
    }
} 