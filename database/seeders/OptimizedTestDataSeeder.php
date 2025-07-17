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

class OptimizedTestDataSeeder extends Seeder
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

        $this->command->info('Creating optimized test data...');

        // Create additional users for testing
        $this->command->info('Creating users...');
        User::factory(5)->create();

        // Create employees
        $this->command->info('Creating employees...');
        Employee::factory(20)->create();

        // Create attendance records efficiently
        $this->command->info('Creating attendance records...');
        $this->createAttendanceRecordsEfficiently(50);

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

        $this->command->info('Optimized test data created successfully!');
    }

    /**
     * Create attendance records efficiently without using factories
     */
    private function createAttendanceRecordsEfficiently(int $totalRecords): void
    {
        $dayTypes = ['Weekday', 'Weekend', 'Holiday'];
        $shifts = ['Morning', 'Evening', 'Night', 'Day'];
        $presentAbsent = ['Present', 'Absent', 'Late', 'Half Day'];
        $statuses = ['Approved', 'Pending', 'Rejected'];
        $islands = ['Male', 'Hulhumale', 'Addu City', 'Fuvahmulah', 'Thilafushi', 'Villingili'];
        $sites = ['Main Office', 'Construction Site A', 'Residential Project', 'Commercial Building', 'Infrastructure Project'];

        $records = [];
        $batchSize = 100;

        for ($i = 0; $i < $totalRecords; $i++) {
            $month = rand(1, 12);
            $year = rand(2023, 2025);
            $day = rand(1, 28);
            
            $records[] = [
                'emp_no' => str_pad(rand(1, 20), 4, '0', STR_PAD_LEFT),
                'month' => $month,
                'year' => $year,
                'day' => $day,
                'day_type' => $dayTypes[array_rand($dayTypes)],
                'shift' => $shifts[array_rand($shifts)],
                'present_absent' => $presentAbsent[array_rand($presentAbsent)],
                'work_in' => $this->getRandomTime(),
                'work_out' => $this->getRandomTime(),
                'remarks' => rand(1, 100) <= 30 ? 'Regular work day' : null,
                'island_name' => rand(1, 100) <= 60 ? $islands[array_rand($islands)] : null,
                'site_name' => rand(1, 100) <= 50 ? $sites[array_rand($sites)] : null,
                'status' => $statuses[array_rand($statuses)],
                'upload_date' => $this->getSimpleDate($year, $month, $day),
            ];

            // Insert in batches to avoid memory issues
            if (count($records) >= $batchSize) {
                DB::table('attendance_records')->insert($records);
                $records = [];
                
                // Clear memory
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }
            }
        }

        // Insert remaining records
        if (!empty($records)) {
            DB::table('attendance_records')->insert($records);
        }
    }

    /**
     * Get random time
     */
    private function getRandomTime(): ?string
    {
        if (rand(1, 100) <= 80) {
            $hour = str_pad(rand(0, 23), 2, '0', STR_PAD_LEFT);
            $minute = str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT);
            $second = str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT);
            return $hour . ':' . $minute . ':' . $second;
        }
        return null;
    }

    /**
     * Get simple date string
     */
    private function getSimpleDate(int $year, int $month, int $day): string
    {
        $month = str_pad($month, 2, '0', STR_PAD_LEFT);
        $day = str_pad($day, 2, '0', STR_PAD_LEFT);
        return $year . '-' . $month . '-' . $day;
    }
} 