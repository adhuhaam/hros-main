<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceRecord>
 */
class AttendanceRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $month = rand(1, 12);
        $year = rand(2023, 2025);
        $day = rand(1, 28);
        
        $dayTypes = ['Weekday', 'Weekend', 'Holiday'];
        $shifts = ['Morning', 'Evening', 'Night', 'Day'];
        $presentAbsent = ['Present', 'Absent', 'Late', 'Half Day'];
        $statuses = ['Approved', 'Pending', 'Rejected'];
        
        return [
            'emp_no' => $this->generateEmployeeNumber(),
            'month' => $month,
            'year' => $year,
            'day' => $day,
            'day_type' => $dayTypes[array_rand($dayTypes)],
            'shift' => $shifts[array_rand($shifts)],
            'present_absent' => $presentAbsent[array_rand($presentAbsent)],
            'work_in' => $this->getRandomTime(),
            'work_out' => $this->getRandomTime(),
            'remarks' => $this->getRandomRemarks(),
            'island_name' => $this->getRandomIsland(),
            'site_name' => $this->getRandomSite(),
            'status' => $statuses[array_rand($statuses)],
            'upload_date' => $this->getRandomDate(),
        ];
    }

    /**
     * Generate employee number
     */
    private function generateEmployeeNumber(): string
    {
        static $counter = 1;
        return str_pad($counter++, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get random time
     */
    private function getRandomTime(): ?string
    {
        if (rand(1, 100) <= 80) { // 80% chance of having time
            $hour = str_pad(rand(0, 23), 2, '0', STR_PAD_LEFT);
            $minute = str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT);
            $second = str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT);
            return $hour . ':' . $minute . ':' . $second;
        }
        return null;
    }

    /**
     * Get random remarks
     */
    private function getRandomRemarks(): ?string
    {
        if (rand(1, 100) <= 30) { // 30% chance of having remarks
            $remarks = [
                'Regular work day',
                'Overtime completed',
                'Site visit',
                'Client meeting',
                'Training session',
                'Equipment maintenance',
                'Safety inspection'
            ];
            return $remarks[array_rand($remarks)];
        }
        return null;
    }

    /**
     * Get random island name
     */
    private function getRandomIsland(): ?string
    {
        if (rand(1, 100) <= 60) { // 60% chance of having island
            $islands = ['Male', 'Hulhumale', 'Addu City', 'Fuvahmulah', 'Thilafushi', 'Villingili'];
            return $islands[array_rand($islands)];
        }
        return null;
    }

    /**
     * Get random site name
     */
    private function getRandomSite(): ?string
    {
        if (rand(1, 100) <= 50) { // 50% chance of having site
            $sites = ['Main Office', 'Construction Site A', 'Residential Project', 'Commercial Building', 'Infrastructure Project'];
            return $sites[array_rand($sites)];
        }
        return null;
    }

    /**
     * Get random date
     */
    private function getRandomDate(): string
    {
        return Carbon::now()->subDays(rand(0, 365))->format('Y-m-d');
    }

    /**
     * Indicate that the attendance record is for present status.
     */
    public function present(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'present_absent' => 'Present',
                'work_in' => '08:00:00',
                'work_out' => '17:00:00',
            ];
        });
    }

    /**
     * Indicate that the attendance record is for absent status.
     */
    public function absent(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'present_absent' => 'Absent',
                'work_in' => null,
                'work_out' => null,
            ];
        });
    }

    /**
     * Indicate that the attendance record is for late status.
     */
    public function late(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'present_absent' => 'Late',
                'work_in' => '09:30:00',
                'work_out' => '17:00:00',
            ];
        });
    }
} 