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
            [
                'name' => 'Annual Leave',
                'description' => 'Regular annual vacation leave',
                'default_days' => 30,
                'is_paid' => true,
                'requires_approval' => true,
            ],
            [
                'name' => 'Sick Leave',
                'description' => 'Medical leave for illness or injury',
                'default_days' => 15,
                'is_paid' => true,
                'requires_approval' => false,
            ],
            [
                'name' => 'Emergency Leave',
                'description' => 'Urgent personal or family emergency',
                'default_days' => 5,
                'is_paid' => true,
                'requires_approval' => true,
            ],
            [
                'name' => 'Maternity Leave',
                'description' => 'Leave for expecting mothers',
                'default_days' => 90,
                'is_paid' => true,
                'requires_approval' => true,
            ],
            [
                'name' => 'Paternity Leave',
                'description' => 'Leave for new fathers',
                'default_days' => 14,
                'is_paid' => true,
                'requires_approval' => true,
            ],
            [
                'name' => 'Unpaid Leave',
                'description' => 'Leave without pay',
                'default_days' => 30,
                'is_paid' => false,
                'requires_approval' => true,
            ],
            [
                'name' => 'Study Leave',
                'description' => 'Leave for educational purposes',
                'default_days' => 10,
                'is_paid' => false,
                'requires_approval' => true,
            ],
            [
                'name' => 'Bereavement Leave',
                'description' => 'Leave for family bereavement',
                'default_days' => 7,
                'is_paid' => true,
                'requires_approval' => false,
            ],
        ];

        $leaveType = $leaveTypes[array_rand($leaveTypes)];

        return [
            'name' => $leaveType['name'],
            'description' => $leaveType['description'],
            'default_days' => $leaveType['default_days'],
            'is_paid' => $leaveType['is_paid'],
            'requires_approval' => $leaveType['requires_approval'],
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the leave type is annual leave.
     */
    public function annual(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Annual Leave',
                'description' => 'Regular annual vacation leave',
                'default_days' => 30,
                'is_paid' => true,
                'requires_approval' => true,
            ];
        });
    }

    /**
     * Indicate that the leave type is sick leave.
     */
    public function sick(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Sick Leave',
                'description' => 'Medical leave for illness or injury',
                'default_days' => 15,
                'is_paid' => true,
                'requires_approval' => false,
            ];
        });
    }

    /**
     * Indicate that the leave type is emergency leave.
     */
    public function emergency(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Emergency Leave',
                'description' => 'Urgent personal or family emergency',
                'default_days' => 5,
                'is_paid' => true,
                'requires_approval' => true,
            ];
        });
    }

    /**
     * Indicate that the leave type is maternity leave.
     */
    public function maternity(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Maternity Leave',
                'description' => 'Leave for expecting mothers',
                'default_days' => 90,
                'is_paid' => true,
                'requires_approval' => true,
            ];
        });
    }

    /**
     * Indicate that the leave type is unpaid leave.
     */
    public function unpaid(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Unpaid Leave',
                'description' => 'Leave without pay',
                'default_days' => 30,
                'is_paid' => false,
                'requires_approval' => true,
            ];
        });
    }

    /**
     * Indicate that the leave type is active.
     */
    public function active(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }

    /**
     * Indicate that the leave type is inactive.
     */
    public function inactive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
} 