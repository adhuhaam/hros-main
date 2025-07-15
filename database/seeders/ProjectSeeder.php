<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'name' => 'Main Office Building',
                'description' => 'Construction of the main office building',
                'project_value' => 5000000.00,
                'client' => 'Downtown Area',
                'started_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'status' => 'Active',
                'created_at' => now(),
            ],
            [
                'name' => 'Residential Complex A',
                'description' => 'Luxury residential complex development',
                'project_value' => 8000000.00,
                'client' => 'Suburban Area',
                'started_date' => '2024-03-01',
                'end_date' => '2025-06-30',
                'status' => 'Active',
                'created_at' => now(),
            ],
            [
                'name' => 'Shopping Mall',
                'description' => 'Modern shopping mall construction',
                'project_value' => 12000000.00,
                'client' => 'Commercial District',
                'started_date' => '2024-06-01',
                'end_date' => '2025-12-31',
                'status' => 'On Hold',
                'created_at' => now(),
            ],
            [
                'name' => 'Highway Extension',
                'description' => 'Highway extension project',
                'project_value' => 15000000.00,
                'client' => 'Outskirts',
                'started_date' => '2024-09-01',
                'end_date' => '2026-03-31',
                'status' => 'On Hold',
                'created_at' => now(),
            ],
            [
                'name' => 'Hospital Renovation',
                'description' => 'Renovation of existing hospital facilities',
                'project_value' => 3000000.00,
                'client' => 'Medical District',
                'started_date' => '2024-02-01',
                'end_date' => '2024-11-30',
                'status' => 'Active',
                'created_at' => now(),
            ],
        ];

        DB::table('projects')->insert($projects);
    }
} 