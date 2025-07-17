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

        $this->command->info('Creating test data...');

        // Create additional users for testing
        $this->command->info('Creating users...');
        User::factory(5)->create();

        // Create employees
        $this->command->info('Creating employees...');
        Employee::factory(20)->create();

        // Create attendance records in smaller batches
        $this->command->info('Creating attendance records...');
        $this->createAttendanceRecordsInBatches(50);

        // Create leave records
        $this->command->info('Creating leave records...');
        LeaveRecord::factory(10)->create();

        // Create warnings
        $this->command->info('Creating warnings...');
        Warning::factory(5)->create();

        // Create salary income records
        $this->command->info('Creating salary income records...');
        SalaryIncome::factory(30)->create();

        // Create salary deduction records
        $this->command->info('Creating salary deduction records...');
        SalaryDeduction::factory(25)->create();

        $this->command->info('Test data created successfully!');
    }

    /**
     * Create attendance records in smaller batches to avoid memory issues
     */
    private function createAttendanceRecordsInBatches(int $totalRecords): void
    {
        $batchSize = 10;
        $batches = ceil($totalRecords / $batchSize);

        for ($i = 0; $i < $batches; $i++) {
            $recordsToCreate = min($batchSize, $totalRecords - ($i * $batchSize));
            AttendanceRecord::factory($recordsToCreate)->create();
            
            // Clear memory after each batch
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
        }
    }
} 