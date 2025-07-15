<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

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
        $basicSalary = $this->getRandomBasicSalary();
        
        return [
            'emp_no' => $this->generateEmployeeNumber(),
            'date' => $this->getRandomDate(),
            'basic_salary' => $basicSalary,
            'service_allowance' => $this->getRandomAmount(0, $basicSalary * 0.3),
            'island_allowance' => $this->getRandomAmount(0, $basicSalary * 0.2),
            'attendance_allowance' => $this->getRandomAmount(0, $basicSalary * 0.1),
            'salary_arrear_other' => $this->getRandomAmount(0, $basicSalary * 0.15),
            'safety_allowance' => $this->getRandomAmount(0, $basicSalary * 0.1),
            'pump_brick_batching' => $this->getRandomAmount(0, $basicSalary * 0.2),
            'food_and_tea' => $this->getRandomAmount(0, $basicSalary * 0.1),
            'long_term_service_allowance' => $this->getRandomAmount(0, $basicSalary * 0.25),
            'living_allowance' => $this->getRandomAmount(0, $basicSalary * 0.15),
            'ot' => $this->getRandomAmount(0, $basicSalary * 0.3),
            'ot_arrears' => $this->getRandomAmount(0, $basicSalary * 0.2),
            'phone_allowance' => $this->getRandomAmount(0, 500),
            'petrol_allowance' => $this->getRandomAmount(0, 1000),
            'pension' => $this->getRandomAmount(0, $basicSalary * 0.1),
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
     * Get random basic salary
     */
    private function getRandomBasicSalary(): float
    {
        return round(rand(8000, 25000), 2);
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
     * Indicate that the income has overtime.
     */
    public function withOvertime(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'ot' => $this->getRandomAmount(1000, 5000),
                'ot_arrears' => $this->getRandomAmount(0, 3000),
            ];
        });
    }

    /**
     * Indicate that the income has allowances.
     */
    public function withAllowances(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'service_allowance' => $this->getRandomAmount(1000, 3000),
                'island_allowance' => $this->getRandomAmount(500, 2000),
                'attendance_allowance' => $this->getRandomAmount(200, 1000),
                'safety_allowance' => $this->getRandomAmount(300, 1500),
                'food_and_tea' => $this->getRandomAmount(400, 1200),
                'phone_allowance' => $this->getRandomAmount(100, 500),
                'petrol_allowance' => $this->getRandomAmount(200, 1000),
            ];
        });
    }

    /**
     * Indicate that the income has arrears.
     */
    public function withArrears(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'salary_arrear_other' => $this->getRandomAmount(1000, 5000),
                'ot_arrears' => $this->getRandomAmount(500, 3000),
            ];
        });
    }
} 