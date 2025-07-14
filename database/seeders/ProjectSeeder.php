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
                'id' => 1,
                'name' => 'Main Office Building',
                'description' => 'Construction of the main office building',
                'location' => 'Downtown Area',
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'status' => 'active',
                'budget' => 5000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Residential Complex A',
                'description' => 'Luxury residential complex development',
                'location' => 'Suburban Area',
                'start_date' => '2024-03-01',
                'end_date' => '2025-06-30',
                'status' => 'active',
                'budget' => 8000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Shopping Mall',
                'description' => 'Modern shopping mall construction',
                'location' => 'Commercial District',
                'start_date' => '2024-06-01',
                'end_date' => '2025-12-31',
                'status' => 'planning',
                'budget' => 12000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Highway Extension',
                'description' => 'Highway extension project',
                'location' => 'Outskirts',
                'start_date' => '2024-09-01',
                'end_date' => '2026-03-31',
                'status' => 'planning',
                'budget' => 15000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Hospital Renovation',
                'description' => 'Renovation of existing hospital facilities',
                'location' => 'Medical District',
                'start_date' => '2024-02-01',
                'end_date' => '2024-11-30',
                'status' => 'active',
                'budget' => 3000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('projects')->insert($projects);
    }
} 