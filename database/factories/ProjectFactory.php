<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['Active', 'Completed', 'On Hold'];
        $clients = ['Ministry of Construction', 'Housing Development Corporation', 'Maldives Transport and Contracting Company', 'Private Client'];
        
        $startDate = fake()->dateTimeBetween('-2 years', 'now');
        $endDate = fake()->dateTimeBetween($startDate, '+2 years');
        
        return [
            'name' => fake()->sentence(3),
            'project_value' => fake()->randomFloat(2, 100000, 10000000),
            'client' => fake()->randomElement($clients),
            'started_date' => $startDate,
            'end_date' => $endDate,
            'status' => fake()->randomElement($statuses),
            'images' => json_encode([
                fake()->imageUrl(),
                fake()->imageUrl(),
                fake()->imageUrl(),
            ]),
            'description' => fake()->paragraph(),
            'created_at' => now(),
        ];
    }

    /**
     * Indicate that the project is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Active',
        ]);
    }

    /**
     * Indicate that the project is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Completed',
        ]);
    }

    /**
     * Indicate that the project is on hold.
     */
    public function onHold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'On Hold',
        ]);
    }
} 