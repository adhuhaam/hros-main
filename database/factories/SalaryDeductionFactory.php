<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalaryDeduction>
 */
class SalaryDeductionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'emp_no' => fake()->numerify('####'),
            'date' => fake()->date(),
            'other_deduction' => fake()->randomFloat(2, 0, 1000),
            'salary_advance' => fake()->randomFloat(2, 0, 2000),
            'loan' => fake()->randomFloat(2, 0, 1500),
            'pension' => fake()->randomFloat(2, 0, 800),
            'medical_deduction' => fake()->randomFloat(2, 0, 500),
            'no_pay' => fake()->randomFloat(2, 0, 1000),
            'late' => fake()->randomFloat(2, 0, 300),
        ];
    }

    /**
     * Indicate that the salary deduction has loan payments.
     */
    public function withLoan(): static
    {
        return $this->state(fn (array $attributes) => [
            'loan' => fake()->randomFloat(2, 500, 2000),
        ]);
    }

    /**
     * Indicate that the salary deduction has salary advance.
     */
    public function withAdvance(): static
    {
        return $this->state(fn (array $attributes) => [
            'salary_advance' => fake()->randomFloat(2, 1000, 3000),
        ]);
    }

    /**
     * Indicate that the salary deduction has no pay leave.
     */
    public function withNoPay(): static
    {
        return $this->state(fn (array $attributes) => [
            'no_pay' => fake()->randomFloat(2, 500, 2000),
        ]);
    }

    /**
     * Indicate that the salary deduction has late penalties.
     */
    public function withLatePenalty(): static
    {
        return $this->state(fn (array $attributes) => [
            'late' => fake()->randomFloat(2, 100, 500),
        ]);
    }
} 