<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveRecord>
 */
class LeaveRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-6 months', '+6 months');
        $endDate = fake()->dateTimeBetween($startDate, '+30 days');
        $numDays = fake()->numberBetween(1, 30);
        
        $statuses = ['Pending', 'Approved', 'Rejected', 'Pending Leave Arrival', 'Arrived', 'Departed'];
        
        return [
            'emp_no' => fake()->numerify('####'),
            'leave_type_id' => fake()->numberBetween(1, 8),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'actual_arrival_date' => fake()->optional()->dateTimeBetween($endDate, '+7 days'),
            'num_days' => $numDays,
            'remarks' => fake()->optional()->sentence(),
            'status' => fake()->randomElement($statuses),
            'applied_date' => now(),
            'approved_by' => fake()->optional()->numberBetween(1, 10),
            'approval_date' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'ticket_id' => fake()->optional()->numberBetween(1, 100),
            'departure_ticket_id' => fake()->optional()->numberBetween(1, 100),
            'arrival_ticket_id' => fake()->optional()->numberBetween(1, 100),
            'medical_doc' => fake()->optional()->filePath(),
        ];
    }

    /**
     * Indicate that the leave record is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Pending',
            'approved_by' => null,
            'approval_date' => null,
        ]);
    }

    /**
     * Indicate that the leave record is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Approved',
            'approved_by' => fake()->numberBetween(1, 10),
            'approval_date' => now(),
        ]);
    }

    /**
     * Indicate that the leave record is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Rejected',
            'approved_by' => fake()->numberBetween(1, 10),
            'approval_date' => now(),
        ]);
    }

    /**
     * Indicate that the leave record is for annual leave.
     */
    public function annual(): static
    {
        return $this->state(fn (array $attributes) => [
            'leave_type_id' => 1, // Assuming Annual Leave has ID 1
            'num_days' => fake()->numberBetween(1, 30),
        ]);
    }

    /**
     * Indicate that the leave record is for sick leave.
     */
    public function sick(): static
    {
        return $this->state(fn (array $attributes) => [
            'leave_type_id' => 2, // Assuming Sick Leave has ID 2
            'num_days' => fake()->numberBetween(1, 15),
        ]);
    }
} 