<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

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
        $startDate = Carbon::now()->addDays(rand(1, 30));
        $endDate = $startDate->copy()->addDays(rand(1, 14));
        
        return [
            'emp_no' => $this->generateEmployeeNumber(),
            'leave_type_id' => rand(1, 5),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'days_requested' => $startDate->diffInDays($endDate) + 1,
            'reason' => $this->getRandomReason(),
            'status' => $this->getRandomStatus(),
            'approved_by' => $this->getRandomApprover(),
            'approved_at' => $this->getRandomApprovalDate(),
            'rejection_reason' => $this->getRandomRejectionReason(),
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
     * Get random reason
     */
    private function getRandomReason(): string
    {
        $reasons = [
            'Annual vacation with family',
            'Medical appointment',
            'Personal emergency',
            'Family function',
            'Mental health break',
            'Travel for business',
            'Wedding ceremony',
            'Religious holiday',
            'Home renovation',
            'Child care'
        ];
        return $reasons[array_rand($reasons)];
    }

    /**
     * Get random status
     */
    private function getRandomStatus(): string
    {
        $statuses = ['Pending', 'Approved', 'Rejected', 'Cancelled'];
        return $statuses[array_rand($statuses)];
    }

    /**
     * Get random approver
     */
    private function getRandomApprover(): ?int
    {
        if (rand(1, 100) <= 70) { // 70% chance of having approver
            return rand(1, 10);
        }
        return null;
    }

    /**
     * Get random approval date
     */
    private function getRandomApprovalDate(): ?string
    {
        if (rand(1, 100) <= 60) { // 60% chance of having approval date
            return Carbon::now()->subDays(rand(0, 30))->format('Y-m-d H:i:s');
        }
        return null;
    }

    /**
     * Get random rejection reason
     */
    private function getRandomRejectionReason(): ?string
    {
        if (rand(1, 100) <= 20) { // 20% chance of having rejection reason
            $reasons = [
                'Insufficient leave balance',
                'Critical project deadline',
                'Staff shortage during requested period',
                'Incomplete application',
                'Requested dates not available'
            ];
            return $reasons[array_rand($reasons)];
        }
        return null;
    }

    /**
     * Indicate that the leave is pending.
     */
    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Pending',
                'approved_by' => null,
                'approved_at' => null,
                'rejection_reason' => null,
            ];
        });
    }

    /**
     * Indicate that the leave is approved.
     */
    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Approved',
                'approved_by' => rand(1, 10),
                'approved_at' => Carbon::now()->subDays(rand(1, 7))->format('Y-m-d H:i:s'),
                'rejection_reason' => null,
            ];
        });
    }

    /**
     * Indicate that the leave is rejected.
     */
    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Rejected',
                'approved_by' => rand(1, 10),
                'approved_at' => Carbon::now()->subDays(rand(1, 7))->format('Y-m-d H:i:s'),
                'rejection_reason' => $this->getRandomRejectionReason(),
            ];
        });
    }

    /**
     * Indicate that the leave is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Cancelled',
                'approved_by' => null,
                'approved_at' => null,
                'rejection_reason' => 'Leave cancelled by employee',
            ];
        });
    }

    /**
     * Indicate that the leave is for annual leave.
     */
    public function annual(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'leave_type_id' => 1, // Assuming 1 is annual leave
                'reason' => 'Annual vacation with family',
            ];
        });
    }

    /**
     * Indicate that the leave is for sick leave.
     */
    public function sick(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'leave_type_id' => 2, // Assuming 2 is sick leave
                'reason' => 'Medical appointment',
            ];
        });
    }

    /**
     * Indicate that the leave is for emergency leave.
     */
    public function emergency(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'leave_type_id' => 3, // Assuming 3 is emergency leave
                'reason' => 'Personal emergency',
                'status' => 'Approved', // Emergency leaves are usually approved
            ];
        });
    }
} 