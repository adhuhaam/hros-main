<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
        $month = fake()->numberBetween(1, 12);
        $year = fake()->numberBetween(2023, 2025);
        $day = fake()->numberBetween(1, 28);
        
        $dayTypes = ['Weekday', 'Weekend', 'Holiday'];
        $shifts = ['Morning', 'Evening', 'Night', 'Day'];
        $presentAbsent = ['Present', 'Absent', 'Late', 'Half Day'];
        $statuses = ['Approved', 'Pending', 'Rejected'];
        
        return [
            'emp_no' => fake()->numerify('####'),
            'month' => $month,
            'year' => $year,
            'day' => $day,
            'day_type' => fake()->randomElement($dayTypes),
            'shift' => fake()->randomElement($shifts),
            'present_absent' => fake()->randomElement($presentAbsent),
            'work_in' => fake()->time(),
            'work_out' => fake()->time(),
            'remarks' => fake()->optional()->sentence(),
            'island_name' => fake()->optional()->randomElement(['Male', 'Hulhumale', 'Addu City', 'Fuvahmulah']),
            'site_name' => fake()->optional()->sentence(2),
            'status' => fake()->randomElement($statuses),
            'upload_date' => fake()->date(),
        ];
    }

    /**
     * Indicate that the attendance record is for present status.
     */
    public function present(): static
    {
        return $this->state(fn (array $attributes) => [
            'present_absent' => 'Present',
            'work_in' => '08:00:00',
            'work_out' => '17:00:00',
        ]);
    }

    /**
     * Indicate that the attendance record is for absent status.
     */
    public function absent(): static
    {
        return $this->state(fn (array $attributes) => [
            'present_absent' => 'Absent',
            'work_in' => null,
            'work_out' => null,
        ]);
    }

    /**
     * Indicate that the attendance record is for late status.
     */
    public function late(): static
    {
        return $this->state(fn (array $attributes) => [
            'present_absent' => 'Late',
            'work_in' => '09:30:00',
            'work_out' => '17:00:00',
        ]);
    }
} 