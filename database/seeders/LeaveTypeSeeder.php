<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'description' => 'Regular annual leave entitlement',
                'max_days_per_year' => 21,
                'gender_restriction' => 'None',
                'requires_approval' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sick Leave',
                'description' => 'Medical leave for illness',
                'max_days_per_year' => 14,
                'gender_restriction' => 'None',
                'requires_approval' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Maternity Leave',
                'description' => 'Leave for expecting mothers',
                'max_days_per_year' => 90,
                'gender_restriction' => 'Female',
                'requires_approval' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paternity Leave',
                'description' => 'Leave for new fathers',
                'max_days_per_year' => 14,
                'gender_restriction' => 'Male',
                'requires_approval' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Unpaid Leave',
                'description' => 'Leave without pay',
                'max_days_per_year' => 0,
                'gender_restriction' => 'None',
                'requires_approval' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Emergency Leave',
                'description' => 'Emergency personal leave',
                'max_days_per_year' => 3,
                'gender_restriction' => 'None',
                'requires_approval' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('leave_types')->insert($leaveTypes);
    }
} 