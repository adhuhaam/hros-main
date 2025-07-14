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

class TestDataSeeder extends Seeder
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

        // Create additional users for testing
        User::factory(10)->create();

        // Create employees
        Employee::factory(50)->create();

        // Create attendance records
        AttendanceRecord::factory(200)->create();

        // Create leave records
        LeaveRecord::factory(30)->create();

        // Create warnings
        Warning::factory(15)->create();

        // Create salary income records
        SalaryIncome::factory(100)->create();

        // Create salary deduction records
        SalaryDeduction::factory(80)->create();
    }
} 