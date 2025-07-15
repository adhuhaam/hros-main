<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\EmployeeShift;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $employee = Employee::factory()->create();
        $date = Carbon::now()->subDays(rand(0, 30));
        
        // Get or create employee shift
        $shift = EmployeeShift::where('employee_id', $employee->emp_no)
            ->where('is_active', true)
            ->first();
            
        if (!$shift) {
            $shift = EmployeeShift::factory()->create([
                'employee_id' => $employee->emp_no,
                'effective_from' => Carbon::parse($date)->subDays(30),
            ]);
        }

        $status = $this->getRandomStatus();
        
        $checkIn = null;
        $checkOut = null;
        $totalHours = 0;
        $overtimeHours = 0;
        
        if (in_array($status, ['Present', 'Late', 'Remote'])) {
            // Generate check-in time
            $scheduledStart = Carbon::parse($shift->start_time);
            $gracePeriod = $shift->grace_period_minutes;
            
            if ($status === 'Late') {
                // Late check-in (after grace period)
                $checkIn = $scheduledStart->copy()->addMinutes($gracePeriod + rand(5, 30));
            } else {
                // On time or early check-in
                $checkIn = $scheduledStart->copy()->addMinutes(rand(-30, $gracePeriod));
            }
            
            // Generate check-out time
            $scheduledEnd = Carbon::parse($shift->end_time);
            $workDuration = rand(6, 10); // 6-10 hours of work
            
            $checkOut = $checkIn->copy()->addHours($workDuration);
            
            // Calculate total hours
            $totalHours = $checkIn->diffInMinutes($checkOut) / 60;
            
            // Calculate overtime
            if ($checkOut->gt($scheduledEnd)) {
                $overtimeHours = $scheduledEnd->diffInMinutes($checkOut) / 60;
            }
        }

        return [
            'employee_id' => $employee->emp_no,
            'date' => $date->format('Y-m-d'),
            'scheduled_start' => $shift->start_time,
            'scheduled_end' => $shift->end_time,
            'check_in' => $checkIn ? $checkIn->format('H:i') : null,
            'check_out' => $checkOut ? $checkOut->format('H:i') : null,
            'total_hours' => round($totalHours, 2),
            'overtime_hours' => round($overtimeHours, 2),
            'break_hours' => $shift->break_start ? 1.0 : 0, // 1 hour break if break time is set
            'status' => $status,
            'check_in_status' => $checkIn ? ($status === 'Late' ? 'Late' : 'On Time') : null,
            'check_out_status' => $checkOut ? 'On Time' : null,
            'check_in_location' => $this->getRandomLocation(),
            'check_out_location' => $this->getRandomLocation(),
            'check_in_ip' => $this->getRandomIP(),
            'check_out_ip' => $this->getRandomIP(),
            'notes' => $this->getRandomNotes($status),
            'manager_notes' => $this->getRandomManagerNotes(),
            'is_approved' => rand(1, 100) <= 80, // 80% chance of being approved
            'approved_by' => null, // Will be set if approved
            'approved_at' => null, // Will be set if approved
        ];
    }

    /**
     * Get random status
     */
    private function getRandomStatus(): string
    {
        $statuses = ['Present', 'Absent', 'Late', 'Remote', 'Leave'];
        return $statuses[array_rand($statuses)];
    }

    /**
     * Get random location
     */
    private function getRandomLocation(): ?string
    {
        $locations = ['Office', 'Site A', 'Site B', 'Remote', 'Client Location', null];
        return $locations[array_rand($locations)];
    }

    /**
     * Get random IP address
     */
    private function getRandomIP(): string
    {
        $ips = ['192.168.1.100', '192.168.1.101', '192.168.1.102', '10.0.0.1', '10.0.0.2'];
        return $ips[array_rand($ips)];
    }

    /**
     * Get random notes based on status
     */
    private function getRandomNotes(string $status): ?string
    {
        $notes = [
            'Present' => ['Regular work day', 'Completed all tasks', 'Productive day'],
            'Absent' => ['Sick leave', 'Personal leave', 'No show'],
            'Late' => ['Traffic delay', 'Public transport issue', 'Personal emergency'],
            'Remote' => ['Working from home', 'Client meeting', 'Flexible work arrangement'],
            'Leave' => ['Annual leave', 'Sick leave', 'Personal leave', 'Maternity leave']
        ];

        if (isset($notes[$status])) {
            return $notes[$status][array_rand($notes[$status])];
        }

        return null;
    }

    /**
     * Get random manager notes
     */
    private function getRandomManagerNotes(): ?string
    {
        $notes = ['Approved', 'Good performance', 'Needs improvement', 'Overtime approved', null];
        return $notes[array_rand($notes)];
    }

    /**
     * Indicate that the employee is present.
     */
    public function present(): static
    {
        return $this->state(function (array $attributes) {
            $employee = Employee::find($attributes['employee_id']);
            $shift = EmployeeShift::where('employee_id', $employee->emp_no)
                ->where('is_active', true)
                ->first();
                
            if (!$shift) {
                $shift = EmployeeShift::factory()->create([
                    'employee_id' => $employee->emp_no,
                ]);
            }

            $scheduledStart = Carbon::parse($shift->start_time);
            $scheduledEnd = Carbon::parse($shift->end_time);
            
            $checkIn = $scheduledStart->copy()->addMinutes(rand(-15, 15));
            $checkOut = $scheduledEnd->copy()->addMinutes(rand(-30, 30));
            
            return [
                'status' => 'Present',
                'check_in' => $checkIn->format('H:i'),
                'check_out' => $checkOut->format('H:i'),
                'check_in_status' => 'On Time',
                'check_out_status' => 'On Time',
                'total_hours' => round($checkIn->diffInMinutes($checkOut) / 60, 2),
                'overtime_hours' => $checkOut->gt($scheduledEnd) ? round($scheduledEnd->diffInMinutes($checkOut) / 60, 2) : 0,
            ];
        });
    }

    /**
     * Indicate that the employee is absent.
     */
    public function absent(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Absent',
                'check_in' => null,
                'check_out' => null,
                'check_in_status' => null,
                'check_out_status' => null,
                'total_hours' => 0,
                'overtime_hours' => 0,
                'notes' => ['Sick leave', 'Personal leave', 'No show'][array_rand([0, 1, 2])],
            ];
        });
    }

    /**
     * Indicate that the employee is late.
     */
    public function late(): static
    {
        return $this->state(function (array $attributes) {
            $employee = Employee::find($attributes['employee_id']);
            $shift = EmployeeShift::where('employee_id', $employee->emp_no)
                ->where('is_active', true)
                ->first();
                
            if (!$shift) {
                $shift = EmployeeShift::factory()->create([
                    'employee_id' => $employee->emp_no,
                ]);
            }

            $scheduledStart = Carbon::parse($shift->start_time);
            $scheduledEnd = Carbon::parse($shift->end_time);
            
            $checkIn = $scheduledStart->copy()->addMinutes(rand(15, 45));
            $checkOut = $scheduledEnd->copy()->addMinutes(rand(-15, 30));
            
            return [
                'status' => 'Late',
                'check_in' => $checkIn->format('H:i'),
                'check_out' => $checkOut->format('H:i'),
                'check_in_status' => 'Late',
                'check_out_status' => 'On Time',
                'total_hours' => round($checkIn->diffInMinutes($checkOut) / 60, 2),
                'overtime_hours' => $checkOut->gt($scheduledEnd) ? round($scheduledEnd->diffInMinutes($checkOut) / 60, 2) : 0,
                'notes' => ['Traffic delay', 'Public transport issue', 'Personal emergency'][array_rand([0, 1, 2])],
            ];
        });
    }

    /**
     * Indicate that the employee is working remotely.
     */
    public function remote(): static
    {
        return $this->state(function (array $attributes) {
            $employee = Employee::find($attributes['employee_id']);
            $shift = EmployeeShift::where('employee_id', $employee->emp_no)
                ->where('is_active', true)
                ->first();
                
            if (!$shift) {
                $shift = EmployeeShift::factory()->create([
                    'employee_id' => $employee->emp_no,
                ]);
            }

            $scheduledStart = Carbon::parse($shift->start_time);
            $scheduledEnd = Carbon::parse($shift->end_time);
            
            $checkIn = $scheduledStart->copy()->addMinutes(rand(-30, 30));
            $checkOut = $scheduledEnd->copy()->addMinutes(rand(-30, 60));
            
            return [
                'status' => 'Remote',
                'check_in' => $checkIn->format('H:i'),
                'check_out' => $checkOut->format('H:i'),
                'check_in_status' => 'On Time',
                'check_out_status' => 'On Time',
                'total_hours' => round($checkIn->diffInMinutes($checkOut) / 60, 2),
                'overtime_hours' => $checkOut->gt($scheduledEnd) ? round($scheduledEnd->diffInMinutes($checkOut) / 60, 2) : 0,
                'check_in_location' => 'Remote - Home Office',
                'check_out_location' => 'Remote - Home Office',
                'notes' => 'Working from home',
            ];
        });
    }

    /**
     * Indicate that the employee is on leave.
     */
    public function leave(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Leave',
                'check_in' => null,
                'check_out' => null,
                'check_in_status' => null,
                'check_out_status' => null,
                'total_hours' => 0,
                'overtime_hours' => 0,
                'notes' => ['Annual leave', 'Sick leave', 'Personal leave', 'Maternity leave'][array_rand([0, 1, 2, 3])],
            ];
        });
    }

    /**
     * Indicate that the attendance is approved.
     */
    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_approved' => true,
                'approved_by' => 1, // Assuming user ID 1 is admin
                'approved_at' => Carbon::parse($attributes['date'])->addHours(rand(1, 8)),
            ];
        });
    }

    /**
     * Indicate that the attendance is pending approval.
     */
    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_approved' => false,
                'approved_by' => null,
                'approved_at' => null,
            ];
        });
    }

    /**
     * Create attendance for today.
     */
    public function today(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'date' => today()->format('Y-m-d'),
            ];
        });
    }

    /**
     * Create attendance for this week.
     */
    public function thisWeek(): static
    {
        return $this->state(function (array $attributes) {
            $startOfWeek = now()->startOfWeek();
            $endOfWeek = now()->endOfWeek();
            $randomDate = $startOfWeek->copy()->addDays(rand(0, 6));
            
            return [
                'date' => $randomDate->format('Y-m-d'),
            ];
        });
    }

    /**
     * Create attendance for this month.
     */
    public function thisMonth(): static
    {
        return $this->state(function (array $attributes) {
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();
            $randomDate = $startOfMonth->copy()->addDays(rand(0, $endOfMonth->diffInDays($startOfMonth)));
            
            return [
                'date' => $randomDate->format('Y-m-d'),
            ];
        });
    }
} 