<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployeeShift>
 */
class EmployeeShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $shiftTypes = [
            [
                'name' => 'Morning Shift',
                'start_time' => '08:00',
                'end_time' => '16:00',
                'break_start' => '12:00',
                'break_end' => '13:00',
                'working_days' => [1, 2, 3, 4, 5] // Monday to Friday
            ],
            [
                'name' => 'Afternoon Shift',
                'start_time' => '14:00',
                'end_time' => '22:00',
                'break_start' => '18:00',
                'break_end' => '19:00',
                'working_days' => [1, 2, 3, 4, 5]
            ],
            [
                'name' => 'Night Shift',
                'start_time' => '22:00',
                'end_time' => '06:00',
                'break_start' => '02:00',
                'break_end' => '03:00',
                'working_days' => [1, 2, 3, 4, 5]
            ],
            [
                'name' => 'Flexible Shift',
                'start_time' => '09:00',
                'end_time' => '17:00',
                'break_start' => '12:00',
                'break_end' => '13:00',
                'working_days' => [1, 2, 3, 4, 5, 6] // Including Saturday
            ],
            [
                'name' => 'Part-time Morning',
                'start_time' => '09:00',
                'end_time' => '13:00',
                'break_start' => null,
                'break_end' => null,
                'working_days' => [1, 2, 3, 4, 5]
            ]
        ];

        $shift = $this->faker->randomElement($shiftTypes);

        return [
            'employee_id' => Employee::factory(),
            'shift_name' => $shift['name'],
            'start_time' => $shift['start_time'],
            'end_time' => $shift['end_time'],
            'break_start' => $shift['break_start'],
            'break_end' => $shift['break_end'],
            'grace_period_minutes' => $this->faker->randomElement([10, 15, 20]),
            'is_active' => true,
            'working_days' => $shift['working_days'],
            'effective_from' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'effective_to' => $this->faker->optional(0.2)->dateTimeBetween('now', '+6 months'),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the shift is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a morning shift.
     */
    public function morning(): static
    {
        return $this->state(fn (array $attributes) => [
            'shift_name' => 'Morning Shift',
            'start_time' => '08:00',
            'end_time' => '16:00',
            'break_start' => '12:00',
            'break_end' => '13:00',
            'working_days' => [1, 2, 3, 4, 5],
        ]);
    }

    /**
     * Create an afternoon shift.
     */
    public function afternoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'shift_name' => 'Afternoon Shift',
            'start_time' => '14:00',
            'end_time' => '22:00',
            'break_start' => '18:00',
            'break_end' => '19:00',
            'working_days' => [1, 2, 3, 4, 5],
        ]);
    }

    /**
     * Create a night shift.
     */
    public function night(): static
    {
        return $this->state(fn (array $attributes) => [
            'shift_name' => 'Night Shift',
            'start_time' => '22:00',
            'end_time' => '06:00',
            'break_start' => '02:00',
            'break_end' => '03:00',
            'working_days' => [1, 2, 3, 4, 5],
        ]);
    }

    /**
     * Create a weekend shift.
     */
    public function weekend(): static
    {
        return $this->state(fn (array $attributes) => [
            'shift_name' => 'Weekend Shift',
            'start_time' => '10:00',
            'end_time' => '18:00',
            'break_start' => '13:00',
            'break_end' => '14:00',
            'working_days' => [6, 7], // Saturday and Sunday
        ]);
    }

    /**
     * Create a flexible shift.
     */
    public function flexible(): static
    {
        return $this->state(fn (array $attributes) => [
            'shift_name' => 'Flexible Shift',
            'start_time' => '09:00',
            'end_time' => '17:00',
            'break_start' => '12:00',
            'break_end' => '13:00',
            'working_days' => [1, 2, 3, 4, 5, 6],
        ]);
    }
} 