<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warning>
 */
class WarningFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['Pending HOD Review', 'Pending HRM Review', 'Pending Director Review', 'Resolved'];
        
        return [
            'emp_no' => fake()->numerify('####'),
            'problem' => fake()->paragraph(),
            'employee_statement' => fake()->optional()->paragraph(),
            'hrm_statement' => fake()->optional()->paragraph(),
            'hod_statement' => fake()->optional()->paragraph(),
            'management_comment' => fake()->optional()->paragraph(),
            'management_decision' => fake()->optional()->sentence(),
            'status' => fake()->randomElement($statuses),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the warning is pending HOD review.
     */
    public function pendingHod(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Pending HOD Review',
        ]);
    }

    /**
     * Indicate that the warning is pending HRM review.
     */
    public function pendingHrm(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Pending HRM Review',
            'hod_statement' => fake()->paragraph(),
        ]);
    }

    /**
     * Indicate that the warning is pending director review.
     */
    public function pendingDirector(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Pending Director Review',
            'hod_statement' => fake()->paragraph(),
            'hrm_statement' => fake()->paragraph(),
        ]);
    }

    /**
     * Indicate that the warning is resolved.
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Resolved',
            'hod_statement' => fake()->paragraph(),
            'hrm_statement' => fake()->paragraph(),
            'management_comment' => fake()->paragraph(),
            'management_decision' => fake()->sentence(),
        ]);
    }
} 