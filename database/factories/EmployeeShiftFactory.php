<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

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
        $employee = Employee::factory()->create();
        $shift = $this->getRandomShift();
        
        return [
            'employee_id' => $employee->emp_no,
            'shift_name' => $shift['name'],
            'start_time' => $shift['start_time'],
            'end_time' => $shift['end_time'],
            'break_start' => $shift['break_start'],
            'break_end' => $shift['break_end'],
            'grace_period_minutes' => $this->getRandomGracePeriod(),
            'is_active' => true,
            'working_days' => $this->getRandomWorkingDays(),
            'effective_from' => Carbon::now()->subMonths(rand(1, 6)),
            'effective_to' => $this->getRandomEffectiveTo(),
            'notes' => $this->getRandomNotes(),
        ];
    }

    /**
     * Get random shift configuration
     */
    private function getRandomShift(): array
    {
        $shifts = [
            [
                'name' => 'Morning Shift',
                'start_time' => '08:00',
                'end_time' => '16:00',
                'break_start' => '12:00',
                'break_end' => '13:00',
            ],
            [
                'name' => 'Evening Shift',
                'start_time' => '16:00',
                'end_time' => '00:00',
                'break_start' => '20:00',
                'break_end' => '21:00',
            ],
            [
                'name' => 'Night Shift',
                'start_time' => '00:00',
                'end_time' => '08:00',
                'break_start' => '04:00',
                'break_end' => '05:00',
            ],
            [
                'name' => 'Day Shift',
                'start_time' => '09:00',
                'end_time' => '17:00',
                'break_start' => '13:00',
                'break_end' => '14:00',
            ],
            [
                'name' => 'Flexible Shift',
                'start_time' => '10:00',
                'end_time' => '18:00',
                'break_start' => '14:00',
                'break_end' => '15:00',
            ],
        ];

        return $shifts[array_rand($shifts)];
    }

    /**
     * Get random grace period
     */
    private function getRandomGracePeriod(): int
    {
        $gracePeriods = [10, 15, 20];
        return $gracePeriods[array_rand($gracePeriods)];
    }

    /**
     * Get random working days
     */
    private function getRandomWorkingDays(): array
    {
        $workingDaysOptions = [
            [1, 2, 3, 4, 5], // Monday to Friday
            [1, 2, 3, 4, 5, 6], // Monday to Saturday
            [1, 2, 3, 4, 5, 6, 0], // All days
            [2, 3, 4, 5, 6], // Tuesday to Saturday
        ];

        return $workingDaysOptions[array_rand($workingDaysOptions)];
    }

    /**
     * Get random effective to date
     */
    private function getRandomEffectiveTo(): ?string
    {
        if (rand(1, 100) <= 20) { // 20% chance of having end date
            return Carbon::now()->addMonths(rand(1, 12))->format('Y-m-d');
        }
        return null;
    }

    /**
     * Get random notes
     */
    private function getRandomNotes(): ?string
    {
        $notes = [
            'Default shift assignment',
            'Temporary shift change',
            'Project-based assignment',
            'Seasonal shift',
            'Emergency shift',
            null
        ];
        return $notes[array_rand($notes)];
    }

    /**
     * Indicate that the shift is morning shift.
     */
    public function morning(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'shift_name' => 'Morning Shift',
                'start_time' => '08:00',
                'end_time' => '16:00',
                'break_start' => '12:00',
                'break_end' => '13:00',
                'working_days' => [1, 2, 3, 4, 5], // Monday to Friday
            ];
        });
    }

    /**
     * Indicate that the shift is evening shift.
     */
    public function evening(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'shift_name' => 'Evening Shift',
                'start_time' => '16:00',
                'end_time' => '00:00',
                'break_start' => '20:00',
                'break_end' => '21:00',
                'working_days' => [1, 2, 3, 4, 5, 6], // Monday to Saturday
            ];
        });
    }

    /**
     * Indicate that the shift is night shift.
     */
    public function night(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'shift_name' => 'Night Shift',
                'start_time' => '00:00',
                'end_time' => '08:00',
                'break_start' => '04:00',
                'break_end' => '05:00',
                'working_days' => [1, 2, 3, 4, 5, 6, 0], // All days
            ];
        });
    }

    /**
     * Indicate that the shift is flexible.
     */
    public function flexible(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'shift_name' => 'Flexible Shift',
                'start_time' => '10:00',
                'end_time' => '18:00',
                'break_start' => '14:00',
                'break_end' => '15:00',
                'working_days' => [1, 2, 3, 4, 5], // Monday to Friday
            ];
        });
    }

    /**
     * Indicate that the shift is active.
     */
    public function active(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
                'effective_to' => null,
            ];
        });
    }

    /**
     * Indicate that the shift is inactive.
     */
    public function inactive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
                'effective_to' => Carbon::now()->subDays(rand(1, 30)),
            ];
        });
    }

    /**
     * Indicate that the shift is for weekdays only.
     */
    public function weekdays(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'working_days' => [1, 2, 3, 4, 5], // Monday to Friday
            ];
        });
    }

    /**
     * Indicate that the shift is for all days.
     */
    public function allDays(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'working_days' => [1, 2, 3, 4, 5, 6, 0], // All days
            ];
        });
    }
} 