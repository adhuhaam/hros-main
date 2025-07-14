<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveType>
 */
class LeaveTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $leaveTypes = [
            'Annual Leave' => 30,
            'Sick Leave' => 15,
            'Maternity Leave' => 90,
            'Paternity Leave' => 7,
            'Emergency Leave' => 5,
            'Unpaid Leave' => 0,
            'Study Leave' => 10,
            'Hajj Leave' => 30,
        ];
        
        $leaveType = fake()->randomElement(array_keys($leaveTypes));
        
        return [
            'name' => $leaveType,
            'description' => fake()->sentence(),
            'max_days_per_year' => $leaveTypes[$leaveType],
            'gender_restriction' => fake()->randomElement(['None', 'Male', 'Female']),
            'requires_approval' => fake()->boolean(80), // 80% chance of requiring approval
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the leave type is for annual leave.
     */
    public function annual(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Annual Leave',
            'max_days_per_year' => 30,
            'gender_restriction' => 'None',
            'requires_approval' => true,
        ]);
    }

    /**
     * Indicate that the leave type is for sick leave.
     */
    public function sick(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Sick Leave',
            'max_days_per_year' => 15,
            'gender_restriction' => 'None',
            'requires_approval' => true,
        ]);
    }

    /**
     * Indicate that the leave type is for maternity leave.
     */
    public function maternity(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Maternity Leave',
            'max_days_per_year' => 90,
            'gender_restriction' => 'Female',
            'requires_approval' => true,
        ]);
    }
} 