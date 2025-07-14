<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nationalities = ['MALDIVIAN', 'BANGLADESHI', 'INDIAN', 'SRI LANKAN', 'NEPALI', 'PAKISTANI'];
        $companies = ['RASHEED CARPENTRY AND CONSTRUCTION PVT LTD', 'NAZRASH COMPANY PVT LTD'];
        $employmentStatuses = ['Active', 'Terminated', 'Resigned', 'Rejoined', 'Dead', 'Retired', 'Missing'];
        
        return [
            'emp_no' => fake()->unique()->numerify('####'),
            'name' => fake()->name(),
            'gender' => fake()->randomElement(['Male', 'Female']),
            'designation' => fake()->jobTitle(),
            'xpat_designation' => fake()->optional()->jobTitle(),
            'xpat_join_date' => fake()->optional()->date(),
            'department' => fake()->randomElement(['HR', 'Finance', 'Operations', 'Engineering', 'Administration']),
            'nationality' => fake()->randomElement($nationalities),
            'passport_nic_no' => fake()->optional()->numerify('##########'),
            'passport_nic_no_expires' => fake()->optional()->date(),
            'dob' => fake()->date(),
            'wp_no' => fake()->optional()->numerify('WP####'),
            'date_of_join' => fake()->date(),
            'contact_number' => fake()->optional()->phoneNumber(),
            'contact_number_foregn' => fake()->optional()->phoneNumber(),
            'emergency_contact_number' => fake()->optional()->phoneNumber(),
            'emergency_contact_name' => fake()->optional()->name(),
            'employment_status' => fake()->randomElement($employmentStatuses),
            'work_site' => fake()->optional()->city(),
            'insurance_provider' => fake()->optional()->company(),
            'recruiting_agency' => fake()->optional()->company(),
            'emp_email' => fake()->optional()->safeEmail(),
            'company_email' => fake()->optional()->safeEmail(),
            'permanent_address' => fake()->optional()->address(),
            'persent_address' => fake()->address(),
            'basic_salary' => fake()->randomFloat(2, 5000, 50000),
            'salary_currency' => fake()->randomElement(['MVR', 'USD']),
            'termination_date' => fake()->optional()->date(),
            'level' => fake()->randomElement(['senior', 'junior']),
            'company' => fake()->randomElement($companies),
            'player_id' => fake()->optional()->uuid(),
        ];
    }

    /**
     * Indicate that the employee is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'employment_status' => 'Active',
        ]);
    }

    /**
     * Indicate that the employee is Maldivian.
     */
    public function maldivian(): static
    {
        return $this->state(fn (array $attributes) => [
            'nationality' => 'MALDIVIAN',
        ]);
    }

    /**
     * Indicate that the employee is expatriate.
     */
    public function expatriate(): static
    {
        return $this->state(fn (array $attributes) => [
            'nationality' => fake()->randomElement(['BANGLADESHI', 'INDIAN', 'SRI LANKAN', 'NEPALI', 'PAKISTANI']),
        ]);
    }
} 