<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

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
            'emp_no' => $this->generateEmployeeNumber(),
            'date' => $this->getRandomDate(),
            'other_deduction' => $this->getRandomAmount(0, 1000),
            'salary_advance' => $this->getRandomAmount(0, 2000),
            'loan' => $this->getRandomAmount(0, 1500),
            'pension' => $this->getRandomAmount(0, 800),
            'medical_deduction' => $this->getRandomAmount(0, 500),
            'no_pay' => $this->getRandomAmount(0, 1000),
            'late' => $this->getRandomAmount(0, 300),
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
     * Get random date
     */
    private function getRandomDate(): string
    {
        return Carbon::now()->subMonths(rand(0, 12))->format('Y-m-d');
    }

    /**
     * Get random amount
     */
    private function getRandomAmount(float $min, float $max): float
    {
        return round(rand($min * 100, $max * 100) / 100, 2);
    }

    /**
     * Indicate that the deduction is for loan.
     */
    public function loan(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'loan' => $this->getRandomAmount(500, 2000),
                'other_deduction' => 0,
                'salary_advance' => 0,
                'pension' => 0,
                'medical_deduction' => 0,
                'no_pay' => 0,
                'late' => 0,
            ];
        });
    }

    /**
     * Indicate that the deduction is for salary advance.
     */
    public function salaryAdvance(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'salary_advance' => $this->getRandomAmount(1000, 3000),
                'other_deduction' => 0,
                'loan' => 0,
                'pension' => 0,
                'medical_deduction' => 0,
                'no_pay' => 0,
                'late' => 0,
            ];
        });
    }

    /**
     * Indicate that the deduction is for no pay leave.
     */
    public function noPay(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'no_pay' => $this->getRandomAmount(500, 2000),
                'other_deduction' => 0,
                'salary_advance' => 0,
                'loan' => 0,
                'pension' => 0,
                'medical_deduction' => 0,
                'late' => 0,
            ];
        });
    }

    /**
     * Indicate that the deduction is for late attendance.
     */
    public function late(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'late' => $this->getRandomAmount(100, 500),
                'other_deduction' => 0,
                'salary_advance' => 0,
                'loan' => 0,
                'pension' => 0,
                'medical_deduction' => 0,
                'no_pay' => 0,
            ];
        });
    }
} 