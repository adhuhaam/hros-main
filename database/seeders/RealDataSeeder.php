<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use App\Models\AttendanceRecord;
use App\Models\LeaveRecord;
use App\Models\Warning;
use App\Models\SalaryIncome;
use App\Models\SalaryDeduction;
use Illuminate\Support\Facades\DB;

class RealDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only run in local/testing environments
        if (!app()->environment(['local', 'testing'])) {
            return;
        }

        $this->command->info('Creating real data...');

        // Create users with real data
        $this->command->info('Creating users...');
        $this->createRealUsers();

        // Create employees with real data
        $this->command->info('Creating employees...');
        $this->createRealEmployees();

        // Create attendance records
        $this->command->info('Creating attendance records...');
        $this->createRealAttendanceRecords();

        // Create leave records
        $this->command->info('Creating leave records...');
        $this->createRealLeaveRecords();

        // Create warnings
        $this->command->info('Creating warnings...');
        $this->createRealWarnings();

        // Create salary records
        $this->command->info('Creating salary records...');
        $this->createRealSalaryRecords();

        $this->command->info('Real data created successfully!');
    }

    /**
     * Create users with real data
     */
    private function createRealUsers(): void
    {
        $users = [
            [
                'emp_no' => '0001',
                'username' => 'ahmed.mohamed',
                'staff_name' => 'Ahmed Mohamed',
                'des' => 'Project Manager',
                'email' => 'ahmed.mohamed@rcc.com.mv',
                'password' => bcrypt('password'),
                'role_id' => 3, // Project Manager role
            ],
            [
                'emp_no' => '0002',
                'username' => 'aisha.hassan',
                'staff_name' => 'Aisha Hassan',
                'des' => 'HR Officer',
                'email' => 'aisha.hassan@rcc.com.mv',
                'password' => bcrypt('password'),
                'role_id' => 2, // HR Officer role
            ],
            [
                'emp_no' => '0003',
                'username' => 'ibrahim.khan',
                'staff_name' => 'Ibrahim Khan',
                'des' => 'Site Engineer',
                'email' => 'ibrahim.khan@rcc.com.mv',
                'password' => bcrypt('password'),
                'role_id' => 4, // Team Leader role
            ],
            [
                'emp_no' => '0004',
                'username' => 'fatima.ali',
                'staff_name' => 'Fatima Ali',
                'des' => 'Accountant',
                'email' => 'fatima.ali@rcc.com.mv',
                'password' => bcrypt('password'),
                'role_id' => 5, // Finance Officer role
            ],
            [
                'emp_no' => '0005',
                'username' => 'omar.rahman',
                'staff_name' => 'Omar Rahman',
                'des' => 'System Administrator',
                'email' => 'omar.rahman@rcc.com.mv',
                'password' => bcrypt('password'),
                'role_id' => 1, // Admin role
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }

    /**
     * Create employees with real data
     */
    private function createRealEmployees(): void
    {
        $employees = [
            [
                'emp_no' => '0001',
                'name' => 'Ahmed Mohamed',
                'gender' => 'Male',
                'designation' => 'Project Manager',
                'xpat_designation' => 'Senior Engineer',
                'xpat_join_date' => '2023-01-15',
                'department' => 'Engineering',
                'nationality' => 'Bangladesh',
                'passport_nic_no' => 'BD123456',
                'passport_expire_date' => '2028-12-31',
                'dob' => '1985-06-15',
                'wp_no' => 'WP0001',
                'date_of_join' => '2023-01-15',
                'contact_number' => '+960 1234567',
                'contact_number_foregn' => '+880 123456789',
                'emergency_contact_number' => '+960 9876543',
                'emergency_contact_name' => 'Fatima Ahmed',
                'employment_status' => 'Active',
                'work_site' => 'Main Site',
                'insurance_provider' => 'HDFC',
                'recruiting_agency' => 'ABC Recruitment',
                'emp_email' => 'ahmed.mohamed@gmail.com',
                'company_email' => 'ahmed.mohamed@rcc.com.mv',
                'permanent_address' => '123 Main Street, Dhaka, Bangladesh',
                'persent_address' => '456 Work Street, Male, Maldives',
                'basic_salary' => 15000.00,
                'salary_currency' => 'MVR',
                'termination_date' => null,
                'level' => 'senior',
                'company' => 'RASHEED CARPENTRY AND CONSTRUCTION PVT LTD',
            ],
            [
                'emp_no' => '0002',
                'name' => 'Aisha Hassan',
                'gender' => 'Female',
                'designation' => 'HR Officer',
                'xpat_designation' => null,
                'xpat_join_date' => '2023-02-01',
                'department' => 'HR',
                'nationality' => 'Maldives',
                'passport_nic_no' => 'MV789012',
                'passport_expire_date' => '2030-06-30',
                'dob' => '1990-03-20',
                'wp_no' => 'WP0002',
                'date_of_join' => '2023-02-01',
                'contact_number' => '+960 2345678',
                'contact_number_foregn' => null,
                'emergency_contact_number' => '+960 8765432',
                'emergency_contact_name' => 'Mohamed Ali',
                'employment_status' => 'Active',
                'work_site' => 'Office Building',
                'insurance_provider' => 'Amana Takaful',
                'recruiting_agency' => 'XYZ Recruitment',
                'emp_email' => 'aisha.hassan@gmail.com',
                'company_email' => 'aisha.hassan@rcc.com.mv',
                'permanent_address' => '789 Home Street, Male, Maldives',
                'persent_address' => '321 Office Street, Male, Maldives',
                'basic_salary' => 12000.00,
                'salary_currency' => 'MVR',
                'termination_date' => null,
                'level' => 'junior',
                'company' => 'RASHEED CARPENTRY AND CONSTRUCTION PVT LTD',
            ],
            [
                'emp_no' => '0003',
                'name' => 'Ibrahim Khan',
                'gender' => 'Male',
                'designation' => 'Site Engineer',
                'xpat_designation' => 'Technical Specialist',
                'xpat_join_date' => '2023-03-10',
                'department' => 'Engineering',
                'nationality' => 'India',
                'passport_nic_no' => 'IN345678',
                'passport_expire_date' => '2029-09-15',
                'dob' => '1988-11-10',
                'wp_no' => 'WP0003',
                'date_of_join' => '2023-03-10',
                'contact_number' => '+960 3456789',
                'contact_number_foregn' => '+91 987654321',
                'emergency_contact_number' => '+960 7654321',
                'emergency_contact_name' => 'Sara Khan',
                'employment_status' => 'Active',
                'work_site' => 'Construction Site',
                'insurance_provider' => 'Allianz',
                'recruiting_agency' => 'Best Recruitment',
                'emp_email' => 'ibrahim.khan@gmail.com',
                'company_email' => 'ibrahim.khan@rcc.com.mv',
                'permanent_address' => '456 Village Street, Mumbai, India',
                'persent_address' => '654 Site Street, Hulhumale, Maldives',
                'basic_salary' => 14000.00,
                'salary_currency' => 'MVR',
                'termination_date' => null,
                'level' => 'senior',
                'company' => 'NAZRASH COMPANY PVT LTD',
            ],
            [
                'emp_no' => '0004',
                'name' => 'Fatima Ali',
                'gender' => 'Female',
                'designation' => 'Accountant',
                'xpat_designation' => null,
                'xpat_join_date' => '2023-04-05',
                'department' => 'Finance',
                'nationality' => 'Maldives',
                'passport_nic_no' => 'MV567890',
                'passport_expire_date' => '2031-03-15',
                'dob' => '1992-08-25',
                'wp_no' => 'WP0004',
                'date_of_join' => '2023-04-05',
                'contact_number' => '+960 4567890',
                'contact_number_foregn' => null,
                'emergency_contact_number' => '+960 6543210',
                'emergency_contact_name' => 'Ali Hassan',
                'employment_status' => 'Active',
                'work_site' => 'Office Building',
                'insurance_provider' => 'Amana Takaful',
                'recruiting_agency' => 'Local Recruitment',
                'emp_email' => 'fatima.ali@gmail.com',
                'company_email' => 'fatima.ali@rcc.com.mv',
                'permanent_address' => '321 Home Street, Male, Maldives',
                'persent_address' => '789 Office Street, Male, Maldives',
                'basic_salary' => 11000.00,
                'salary_currency' => 'MVR',
                'termination_date' => null,
                'level' => 'junior',
                'company' => 'RASHEED CARPENTRY AND CONSTRUCTION PVT LTD',
            ],
            [
                'emp_no' => '0005',
                'name' => 'Omar Rahman',
                'gender' => 'Male',
                'designation' => 'System Administrator',
                'xpat_designation' => 'IT Specialist',
                'xpat_join_date' => '2023-05-20',
                'department' => 'IT',
                'nationality' => 'Bangladesh',
                'passport_nic_no' => 'BD987654',
                'passport_expire_date' => '2027-11-30',
                'dob' => '1987-12-05',
                'wp_no' => 'WP0005',
                'date_of_join' => '2023-05-20',
                'contact_number' => '+960 5678901',
                'contact_number_foregn' => '+880 123456789',
                'emergency_contact_number' => '+960 5432109',
                'emergency_contact_name' => 'Rahman Khan',
                'employment_status' => 'Active',
                'work_site' => 'Office Building',
                'insurance_provider' => 'HDFC',
                'recruiting_agency' => 'Tech Recruitment',
                'emp_email' => 'omar.rahman@gmail.com',
                'company_email' => 'omar.rahman@rcc.com.mv',
                'permanent_address' => '654 Tech Street, Dhaka, Bangladesh',
                'persent_address' => '987 IT Street, Male, Maldives',
                'basic_salary' => 16000.00,
                'salary_currency' => 'MVR',
                'termination_date' => null,
                'level' => 'senior',
                'company' => 'NAZRASH COMPANY PVT LTD',
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }

    /**
     * Create attendance records with real data
     */
    private function createRealAttendanceRecords(): void
    {
        $employees = ['0001', '0002', '0003', '0004', '0005'];
        $records = [];

        // Create attendance records for the last 30 days
        for ($i = 0; $i < 30; $i++) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dayOfWeek = date('N', strtotime($date)); // 1=Monday, 7=Sunday
            
            foreach ($employees as $empNo) {
                $isWeekend = $dayOfWeek >= 6; // Saturday or Sunday
                $isPresent = !$isWeekend && rand(1, 100) <= 85; // 85% attendance on weekdays
                
                $records[] = [
                    'emp_no' => $empNo,
                    'month' => (int)date('n', strtotime($date)),
                    'year' => (int)date('Y', strtotime($date)),
                    'day' => (int)date('j', strtotime($date)),
                    'day_type' => $isWeekend ? 'Weekend' : 'Weekday',
                    'shift' => 'Day',
                    'present_absent' => $isPresent ? 'Present' : 'Absent',
                    'work_in' => $isPresent ? '08:00:00' : null,
                    'work_out' => $isPresent ? '17:00:00' : null,
                    'remarks' => $isPresent ? 'Regular work day' : null,
                    'island_name' => 'Male',
                    'site_name' => 'Main Office',
                    'status' => 'Approved',
                    'upload_date' => $date,
                ];
            }
        }

        // Insert in batches
        $chunks = array_chunk($records, 50);
        foreach ($chunks as $chunk) {
            DB::table('attendance_records')->insert($chunk);
        }
    }

    /**
     * Create leave records with real data
     */
    private function createRealLeaveRecords(): void
    {
        $leaveRecords = [
            [
                'emp_no' => '0001',
                'leave_type_id' => 1, // Annual Leave
                'start_date' => '2024-06-15',
                'end_date' => '2024-06-20',
                'days' => 5,
                'reason' => 'Family vacation',
                'status' => 'Approved',
                'approved_by' => '0005', // Omar Rahman (Admin)
                'approved_date' => '2024-06-10',
            ],
            [
                'emp_no' => '0002',
                'leave_type_id' => 2, // Sick Leave
                'start_date' => '2024-07-01',
                'end_date' => '2024-07-03',
                'days' => 3,
                'reason' => 'Medical appointment',
                'status' => 'Approved',
                'approved_by' => '0001', // Ahmed Mohamed (Manager)
                'approved_date' => '2024-06-28',
            ],
            [
                'emp_no' => '0003',
                'leave_type_id' => 1, // Annual Leave
                'start_date' => '2024-08-10',
                'end_date' => '2024-08-15',
                'days' => 5,
                'reason' => 'Personal leave',
                'status' => 'Pending',
                'approved_by' => null,
                'approved_date' => null,
            ],
        ];

        foreach ($leaveRecords as $record) {
            LeaveRecord::create($record);
        }
    }

    /**
     * Create warnings with real data
     */
    private function createRealWarnings(): void
    {
        $warnings = [
            [
                'employee_id' => '0003',
                'warning_type' => 'Late Arrival',
                'description' => 'Arrived 30 minutes late without prior notice',
                'warning_date' => '2024-05-15',
                'issued_by' => '0001', // Ahmed Mohamed
                'status' => 'Active',
            ],
            [
                'employee_id' => '0004',
                'warning_type' => 'Documentation Error',
                'description' => 'Incorrect data entry in financial reports',
                'warning_date' => '2024-06-01',
                'issued_by' => '0005', // Omar Rahman
                'status' => 'Resolved',
            ],
        ];

        foreach ($warnings as $warning) {
            Warning::create($warning);
        }
    }

    /**
     * Create salary records with real data
     */
    private function createRealSalaryRecords(): void
    {
        // Salary Income Records
        $salaryIncomes = [
            [
                'emp_no' => '0001',
                'month' => 7,
                'year' => 2024,
                'basic_salary' => 15000.00,
                'allowance' => 2000.00,
                'overtime' => 1500.00,
                'bonus' => 1000.00,
                'total_income' => 19500.00,
            ],
            [
                'emp_no' => '0002',
                'month' => 7,
                'year' => 2024,
                'basic_salary' => 12000.00,
                'allowance' => 1500.00,
                'overtime' => 800.00,
                'bonus' => 500.00,
                'total_income' => 14800.00,
            ],
            [
                'emp_no' => '0003',
                'month' => 7,
                'year' => 2024,
                'basic_salary' => 14000.00,
                'allowance' => 1800.00,
                'overtime' => 1200.00,
                'bonus' => 800.00,
                'total_income' => 17800.00,
            ],
        ];

        foreach ($salaryIncomes as $income) {
            SalaryIncome::create($income);
        }

        // Salary Deduction Records
        $salaryDeductions = [
            [
                'emp_no' => '0001',
                'month' => 7,
                'year' => 2024,
                'tax' => 1500.00,
                'insurance' => 500.00,
                'loan' => 1000.00,
                'other_deductions' => 200.00,
                'total_deductions' => 3200.00,
            ],
            [
                'emp_no' => '0002',
                'month' => 7,
                'year' => 2024,
                'tax' => 1200.00,
                'insurance' => 400.00,
                'loan' => 0.00,
                'other_deductions' => 150.00,
                'total_deductions' => 1750.00,
            ],
            [
                'emp_no' => '0003',
                'month' => 7,
                'year' => 2024,
                'tax' => 1400.00,
                'insurance' => 450.00,
                'loan' => 800.00,
                'other_deductions' => 180.00,
                'total_deductions' => 2830.00,
            ],
        ];

        foreach ($salaryDeductions as $deduction) {
            SalaryDeduction::create($deduction);
        }
    }
} 