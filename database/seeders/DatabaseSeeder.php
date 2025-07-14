<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Project;
use App\Models\LeaveType;
use App\Models\AttendanceRecord;
use App\Models\LeaveRecord;
use App\Models\Warning;
use App\Models\SalaryIncome;
use App\Models\SalaryDeduction;
use Illuminate\Support\Facades\DB;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first
        DB::table('roles')->insert([
            [
                'id' => 1,
                'role_name' => 'Admin',
                'description' => 'Full access to the system',
                'created_at' => '2024-12-14 03:26:56',
                'permissions' => 'view_employees,edit_employee',
            ],
            [
                'id' => 2,
                'role_name' => 'Other Staff',
                'description' => 'Limited access based on their tasks',
                'created_at' => '2024-12-14 03:26:56',
                'permissions' => null,
            ],
            [
                'id' => 3,
                'role_name' => 'Information Officer',
                'description' => 'Manages and accesses information-related tasks',
                'created_at' => '2024-12-14 03:26:56',
                'permissions' => null,
            ],
            [
                'id' => 4,
                'role_name' => 'Xpat Officer',
                'description' => 'Handles expatriate-related tasks',
                'created_at' => '2024-12-14 03:26:56',
                'permissions' => null,
            ],
            [
                'id' => 5,
                'role_name' => 'Leave Officer',
                'description' => 'Manages leave records and approvals',
                'created_at' => '2024-12-14 03:26:56',
                'permissions' => null,
            ],
            [
                'id' => 6,
                'role_name' => 'HR Manager',
                'description' => 'Manages employee records and HR-related tasks',
                'created_at' => '2024-12-14 03:26:56',
                'permissions' => null,
            ],
            [
                'id' => 7,
                'role_name' => 'Payroll Officer',
                'description' => 'Handles salary management and financial records',
                'created_at' => '2024-12-14 03:26:56',
                'permissions' => null,
            ],
            [
                'id' => 8,
                'role_name' => 'Supervisor',
                'description' => 'Oversees teams and approves tasks',
                'created_at' => '2024-12-14 03:26:56',
                'permissions' => null,
            ],
            [
                'id' => 9,
                'role_name' => 'hod',
                'description' => 'head of department',
                'created_at' => '2025-01-01 08:55:16',
                'permissions' => null,
            ],
            [
                'id' => 10,
                'role_name' => 'director',
                'description' => 'management login',
                'created_at' => '2025-01-01 08:55:16',
                'permissions' => null,
            ],
            [
                'id' => 11,
                'role_name' => 'planing',
                'description' => 'planing department',
                'created_at' => '2025-01-06 12:13:25',
                'permissions' => null,
            ],
            [
                'id' => 12,
                'role_name' => 'welfare',
                'description' => 'welfare officer',
                'created_at' => '2025-02-03 19:45:43',
                'permissions' => null,
            ],
            [
                'id' => 13,
                'role_name' => 'employees',
                'description' => 'general employees',
                'created_at' => '2025-03-25 01:49:53',
                'permissions' => null,
            ],
            [
                'id' => 14,
                'role_name' => 'reception',
                'description' => 'rcc reception',
                'created_at' => '2025-04-12 13:15:38',
                'permissions' => null,
            ],
        ]);

        // Seed users
        DB::table('users')->insert([
            [
                'id' => 1,
                'emp_no' => '5016',
                'username' => '5016',
                'staff_name' => 'Adhuham Layaal Qasim',
                'des' => 'HR OFFICER',
                'email' => 'adhuham@rcc.com.mv',
                'password' => '$2y$10$8OabfGCPw3PQfReA6ZnxeO8uA2vv7JCqUfaYrnSYoe2N.TyBNcoDK',
                'role_id' => 1,
                'created_at' => '2024-12-12 12:41:29',
            ],
            [
                'id' => 2,
                'emp_no' => '5044',
                'username' => '5044',
                'staff_name' => 'Abdulla Miuwan Rafeeq',
                'des' => 'HR OFFICER',
                'email' => 'miuwan@rcc.com.mv',
                'password' => '$2y$10$fT2BQxKOckiaeZDDXyzrVunjAoUztL/eNvsPPIBFPAhKyzAa/XAfO',
                'role_id' => 3,
                'created_at' => '2024-12-14 03:14:38',
            ],
            [
                'id' => 3,
                'emp_no' => '4353',
                'username' => '4353',
                'staff_name' => 'Fathimath Shahidha',
                'des' => 'Senior HR Executive',
                'email' => 'shahida@rcc.com.mv',
                'password' => '$2y$10$AltziZMgGZ5PrrmDNx.S.usVbalocPLx01wGCu5I1zbzNy2kDfCL.',
                'role_id' => 3,
                'created_at' => '2024-12-14 08:49:30',
            ],
            [
                'id' => 4,
                'emp_no' => '5059',
                'username' => '5059',
                'staff_name' => 'Hansila',
                'des' => 'HR OFFICER',
                'email' => 'hansila@rcc.com.mv',
                'password' => '$2y$10$FEHUK/YOVNox2pqt0fNq.ODk00EDiK3i35qBvzSlvm.Ya/ZAf8Rw6',
                'role_id' => 4,
                'created_at' => '2024-12-28 10:01:51',
            ],
            [
                'id' => 5,
                'emp_no' => '3321',
                'username' => '3321',
                'staff_name' => 'Sahil',
                'des' => 'EMPLOYEE RELATION OFFICER',
                'email' => 'sahil@rcc.com.mv',
                'password' => '$2y$10$OGD.EDswJGSz1YNnLgXo6.lthsZKiU70L6He/MrSJxbGT6Kz4y.W.',
                'role_id' => 2,
                'created_at' => '2024-12-29 14:14:56',
            ],
            [
                'id' => 6,
                'emp_no' => '5081',
                'username' => '5081',
                'staff_name' => 'Naina',
                'des' => 'PAYROLL OFFICER',
                'email' => 'naina@rcc.com.mv',
                'password' => '$2y$10$yOXP9GJE9RWhXFWbtL/lhOASfp3k3xK9oTN154UHx9XnX/3ToQOcm',
                'role_id' => 7,
                'created_at' => '2024-12-31 08:37:34',
            ],
            [
                'id' => 7,
                'emp_no' => '4913',
                'username' => '4913',
                'staff_name' => 'HRM',
                'des' => 'HRM',
                'email' => 'hr@rcc.com.mv',
                'password' => '$2y$10$KV6aaasgcyvDYzR5KUguV.ADKUh0sI3DQp6ujGZyY.igeG1dBVY3W',
                'role_id' => 6,
                'created_at' => '2024-12-31 16:28:20',
            ],
            [
                'id' => 8,
                'emp_no' => '555',
                'username' => '555',
                'staff_name' => 'Rakheem',
                'des' => 'PROJECT DIRECTOR',
                'email' => 'rakheem@rcc.com.mv',
                'password' => '$2y$10$qbiDLM0dd1o3iSDiEXHVuOEXLbaHZR1mNQolrZN/fMPny974CDGCi',
                'role_id' => 10,
                'created_at' => '2025-01-01 08:57:15',
            ],
            [
                'id' => 9,
                'emp_no' => '2253',
                'username' => '2253',
                'staff_name' => 'JP',
                'des' => 'PROJECT MANAGER (MALE)',
                'email' => 'prakash@rcc.com.mv',
                'password' => '$2y$10$Y9UOY3hmvFJ7ldR6KSCJuO8jBiKqjzSIzLGcKockGvrsdGJq.ey6K',
                'role_id' => 9,
                'created_at' => '2025-01-01 10:58:47',
            ],
            [
                'id' => 11,
                'emp_no' => '5193',
                'username' => '5193',
                'staff_name' => 'Sachintha',
                'des' => 'PAYROLL OFFICER',
                'email' => 'sachintha@rcc.com.mv',
                'password' => '$2y$10$9miTw.M03RzP4oktW1zXIOqOuqxtKyTdn2wAxJvaBCuKZXO64SZFa',
                'role_id' => 7,
                'created_at' => '2025-02-01 07:37:36',
            ],
            [
                'id' => 12,
                'emp_no' => '2943',
                'username' => '2943',
                'staff_name' => 'MOHAMED ZUHAIR',
                'des' => 'WELFARE OFFICER',
                'email' => 'zuhair@rcc.com.mv',
                'password' => '$2y$10$8OabfGCPw3PQfReA6ZnxeO8uA2vv7JCqUfaYrnSYoe2N.TyBNcoDK',
                'role_id' => 12,
                'created_at' => '2025-02-03 09:45:28',
            ],
            [
                'id' => 14,
                'emp_no' => '',
                'username' => '2977',
                'staff_name' => 'VISHNURAJ KRISHNARAJ',
                'des' => 'SENIOR PLANNING ENGINEER',
                'email' => 'x@gmail.com',
                'password' => '$2y$10$/S5ko//WcIMNteCglFGLUOcXAcNJ2B1FYY/a/W8WTteYJj.2XSI1S',
                'role_id' => 11,
                'created_at' => '2025-02-12 11:47:51',
            ],
            [
                'id' => 15,
                'emp_no' => '',
                'username' => '5213',
                'staff_name' => 'Riyash Ali',
                'des' => 'HR ASSISITANT ',
                'email' => 'riyash@rcc.com.mv',
                'password' => '2',
                'role_id' => 4,
                'created_at' => '2025-02-23 04:17:10',
            ],
            [
                'id' => 16,
                'emp_no' => '',
                'username' => '2345',
                'staff_name' => 'Uvais',
                'des' => 'DEPUTY CFO',
                'email' => 'uvais@rcc.com.mv',
                'password' => '$2y$10$bUtwt3Kb/5BHHKurPdDfAecAWBzla2XCz9e2C6L2GuB4oOnriEzWW',
                'role_id' => 11,
                'created_at' => '2025-03-22 15:02:04',
            ],
            [
                'id' => 43,
                'emp_no' => '5263',
                'username' => '5263',
                'staff_name' => 'Ahmed Sameer',
                'des' => 'Legal Officer',
                'email' => 'samyr_7@hotmail.com',
                'password' => '125cc8fa41714df3ea03d23b72ca58e0dd4469959ac69b35f2daa747fa4ecf0b',
                'role_id' => 13,
                'created_at' => '2025-04-07 02:53:53',
            ],
            [
                'id' => 44,
                'emp_no' => '5264',
                'username' => '5264',
                'staff_name' => 'MD Ishak Alam',
                'des' => 'BARBENDER',
                'email' => null,
                'password' => '408ae596784e336f141c483f9bd28f3a7ab8ab5f79bebb07916b260a6bc339aa',
                'role_id' => 13,
                'created_at' => '2025-04-07 11:26:34',
            ],
            [
                'id' => 45,
                'emp_no' => '5265',
                'username' => '5265',
                'staff_name' => 'SATISH SINGH',
                'des' => 'LABOURER',
                'email' => null,
                'password' => 'f49fc8756a1cb124c44cbbf6a91b8967f5b892be97cbe50dfeb49dcb9ef92790',
                'role_id' => 13,
                'created_at' => '2025-04-07 11:26:42',
            ],
            [
                'id' => 46,
                'emp_no' => '5266',
                'username' => '5266',
                'staff_name' => 'Ahmed Zain Mohamed Nabeel',
                'des' => 'RECEPTIONIST',
                'email' => null,
                'password' => '$2y$10$39gGLhMiEvQm/wjplVAkkuBDE1GQ.Kg0znGuQTsiapzEN.rFXgNKS',
                'role_id' => 3,
                'created_at' => '2025-04-08 03:19:20',
            ],
        ]);

        // Generate test data using factories
        if (app()->environment('local', 'testing')) {
            // Create leave types
            LeaveType::factory()->count(8)->create();
            
            // Create projects
            Project::factory()->count(10)->create();
            
            // Create additional employees (excluding the ones already seeded)
            Employee::factory()->count(50)->create();
            
            // Create attendance records
            AttendanceRecord::factory()->count(200)->create();
            
            // Create leave records
            LeaveRecord::factory()->count(100)->create();
            
            // Create warnings
            Warning::factory()->count(30)->create();
            
            // Create salary records
            SalaryIncome::factory()->count(150)->create();
            SalaryDeduction::factory()->count(150)->create();
        }
    }
}
