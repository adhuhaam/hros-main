<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

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
        $statuses = ['Pending', 'Under Review', 'Resolved', 'Escalated', 'Closed'];

        return [
            'emp_no' => $this->generateEmployeeNumber(),
            'problem' => $this->getRandomProblem(),
            'employee_statement' => $this->getRandomEmployeeStatement(),
            'hrm_statement' => $this->getRandomHRMStatement(),
            'hod_statement' => $this->getRandomHODStatement(),
            'management_comment' => $this->getRandomManagementComment(),
            'management_decision' => $this->getRandomManagementDecision(),
            'status' => $statuses[array_rand($statuses)],
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
     * Get random problem description
     */
    private function getRandomProblem(): string
    {
        $problems = [
            'Frequent late arrivals affecting team productivity',
            'Inappropriate behavior towards colleagues',
            'Failure to complete assigned tasks on time',
            'Violation of company dress code policy',
            'Unauthorized use of company resources',
            'Poor communication with team members',
            'Failure to follow safety protocols',
            'Insubordination towards supervisors',
            'Excessive personal phone usage during work hours',
            'Failure to maintain workplace cleanliness'
        ];
        return $problems[array_rand($problems)];
    }

    /**
     * Get random employee statement
     */
    private function getRandomEmployeeStatement(): ?string
    {
        if (rand(1, 100) <= 70) { // 70% chance of having statement
            $statements = [
                'I acknowledge the issue and will improve my performance',
                'There were extenuating circumstances that led to this situation',
                'I was not aware of the policy and will ensure compliance going forward',
                'I take full responsibility and apologize for the inconvenience caused',
                'I will work on improving my time management skills'
            ];
            return $statements[array_rand($statements)];
        }
        return null;
    }

    /**
     * Get random HRM statement
     */
    private function getRandomHRMStatement(): ?string
    {
        if (rand(1, 100) <= 60) { // 60% chance of having statement
            $statements = [
                'Employee has been counseled on company policies',
                'Performance improvement plan has been discussed',
                'Employee shows willingness to improve',
                'Further monitoring required',
                'Employee has been given written warning'
            ];
            return $statements[array_rand($statements)];
        }
        return null;
    }

    /**
     * Get random HOD statement
     */
    private function getRandomHODStatement(): ?string
    {
        if (rand(1, 100) <= 50) { // 50% chance of having statement
            $statements = [
                'Employee performance has been below expectations',
                'Team dynamics have been affected by this behavior',
                'Employee shows potential for improvement',
                'Immediate action required to address the issue',
                'Employee has been cooperative during investigation'
            ];
            return $statements[array_rand($statements)];
        }
        return null;
    }

    /**
     * Get random management comment
     */
    private function getRandomManagementComment(): ?string
    {
        if (rand(1, 100) <= 40) { // 40% chance of having comment
            $comments = [
                'Management supports the disciplinary action taken',
                'Employee should be given opportunity to improve',
                'This is a serious violation requiring immediate attention',
                'Management will monitor the situation closely',
                'Employee has good track record, consider leniency'
            ];
            return $comments[array_rand($comments)];
        }
        return null;
    }

    /**
     * Get random management decision
     */
    private function getRandomManagementDecision(): ?string
    {
        if (rand(1, 100) <= 30) { // 30% chance of having decision
            $decisions = [
                'Written warning issued',
                'Performance improvement plan implemented',
                'Final warning before termination',
                'Suspension for 3 days',
                'Verbal warning and monitoring'
            ];
            return $decisions[array_rand($decisions)];
        }
        return null;
    }

    /**
     * Indicate that the warning is pending.
     */
    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Pending',
                'hrm_statement' => null,
                'hod_statement' => null,
                'management_comment' => null,
                'management_decision' => null,
            ];
        });
    }

    /**
     * Indicate that the warning is under review.
     */
    public function underReview(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Under Review',
                'hrm_statement' => $this->getRandomHRMStatement(),
                'hod_statement' => $this->getRandomHODStatement(),
            ];
        });
    }

    /**
     * Indicate that the warning is resolved.
     */
    public function resolved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Resolved',
                'hrm_statement' => $this->getRandomHRMStatement(),
                'hod_statement' => $this->getRandomHODStatement(),
                'management_comment' => $this->getRandomManagementComment(),
                'management_decision' => $this->getRandomManagementDecision(),
            ];
        });
    }

    /**
     * Indicate that the warning is escalated.
     */
    public function escalated(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Escalated',
                'hrm_statement' => $this->getRandomHRMStatement(),
                'hod_statement' => $this->getRandomHODStatement(),
                'management_comment' => 'Issue escalated to senior management for review',
            ];
        });
    }

    /**
     * Indicate that the warning is closed.
     */
    public function closed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Closed',
                'hrm_statement' => $this->getRandomHRMStatement(),
                'hod_statement' => $this->getRandomHODStatement(),
                'management_comment' => $this->getRandomManagementComment(),
                'management_decision' => $this->getRandomManagementDecision(),
            ];
        });
    }
} 