<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalaryIncome>
 */
class SalaryIncomeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $basicSalary = fake()->randomFloat(2, 8000, 25000);
        
        return [
            'emp_no' => fake()->numerify('####'),
            'date' => fake()->date(),
            'basic_salary' => $basicSalary,
            'service_allowance' => fake()->randomFloat(2, 0, $basicSalary * 0.3),
            'island_allowance' => fake()->randomFloat(2, 0, $basicSalary * 0.2),
            'attendance_allowance' => fake()->randomFloat(2, 0, $basicSalary * 0.1),
            'salary_arrear_other' => fake()->randomFloat(2, 0, $basicSalary * 0.15),
            'safety_allowance' => fake()->randomFloat(2, 0, $basicSalary * 0.1),
            'pump_brick_batching' => fake()->randomFloat(2, 0, $basicSalary * 0.2),
            'food_and_tea' => fake()->randomFloat(2, 0, $basicSalary * 0.1),
            'long_term_service_allowance' => fake()->randomFloat(2, 0, $basicSalary * 0.25),
            'living_allowance' => fake()->randomFloat(2, 0, $basicSalary * 0.15),
            'ot' => fake()->randomFloat(2, 0, $basicSalary * 0.3),
            'ot_arrears' => fake()->randomFloat(2, 0, $basicSalary * 0.2),
            'phone_allowance' => fake()->randomFloat(2, 0, 500),
            'petrol_allowance' => fake()->randomFloat(2, 0, 1000),
            'pension' => fake()->randomFloat(2, 0, $basicSalary * 0.1),
        ];
    }

    /**
     * Indicate that the salary income has overtime.
     */
    public function withOvertime(): static
    {
        return $this->state(fn (array $attributes) => [
            'ot' => fake()->randomFloat(2, 1000, 5000),
            'ot_arrears' => fake()->randomFloat(2, 0, 3000),
        ]);
    }

    /**
     * Indicate that the salary income has allowances.
     */
    public function withAllowances(): static
    {
        return $this->state(fn (array $attributes) => [
            'service_allowance' => fake()->randomFloat(2, 1000, 3000),
            'island_allowance' => fake()->randomFloat(2, 500, 2000),
            'attendance_allowance' => fake()->randomFloat(2, 200, 1000),
            'safety_allowance' => fake()->randomFloat(2, 300, 1500),
            'food_and_tea' => fake()->randomFloat(2, 400, 1200),
            'phone_allowance' => fake()->randomFloat(2, 100, 500),
            'petrol_allowance' => fake()->randomFloat(2, 200, 1000),
        ]);
    }

    /**
     * Indicate that the salary income has arrears.
     */
    public function withArrears(): static
    {
        return $this->state(fn (array $attributes) => [
            'salary_arrear_other' => fake()->randomFloat(2, 1000, 5000),
            'ot_arrears' => fake()->randomFloat(2, 500, 3000),
        ]);
    }
} 