<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding attendance data...');

        // Get existing employees
        $employees = Employee::all();
        
        if ($employees->isEmpty()) {
            $this->command->warn('No employees found. Creating sample employees first...');
            $employees = Employee::factory(10)->create();
        }

        // Create shifts for each employee
        $this->command->info('Creating employee shifts...');
        foreach ($employees as $employee) {
            $this->createEmployeeShift($employee);
        }

        // Create attendance records for the last 30 days
        $this->command->info('Creating attendance records...');
        $this->createAttendanceRecords($employees);

        $this->command->info('Attendance seeding completed!');
    }

    /**
     * Create a shift for an employee.
     */
    private function createEmployeeShift($employee): void
    {
        // Define shift types
        $shiftTypes = [
            [
                'name' => 'Morning Shift',
                'start_time' => '08:00',
                'end_time' => '16:00',
                'break_start' => '12:00',
                'break_end' => '13:00',
                'working_days' => [1, 2, 3, 4, 5], // Monday to Friday
                'grace_period_minutes' => 15
            ],
            [
                'name' => 'Afternoon Shift',
                'start_time' => '14:00',
                'end_time' => '22:00',
                'break_start' => '18:00',
                'break_end' => '19:00',
                'working_days' => [1, 2, 3, 4, 5],
                'grace_period_minutes' => 15
            ],
            [
                'name' => 'Night Shift',
                'start_time' => '22:00',
                'end_time' => '06:00',
                'break_start' => '02:00',
                'break_end' => '03:00',
                'working_days' => [1, 2, 3, 4, 5],
                'grace_period_minutes' => 20
            ],
            [
                'name' => 'Flexible Shift',
                'start_time' => '09:00',
                'end_time' => '17:00',
                'break_start' => '12:00',
                'break_end' => '13:00',
                'working_days' => [1, 2, 3, 4, 5, 6], // Including Saturday
                'grace_period_minutes' => 30
            ],
            [
                'name' => 'Part-time Morning',
                'start_time' => '09:00',
                'end_time' => '13:00',
                'break_start' => null,
                'break_end' => null,
                'working_days' => [1, 2, 3, 4, 5],
                'grace_period_minutes' => 10
            ]
        ];

        // Randomly assign a shift type
        $shiftType = $shiftTypes[array_rand($shiftTypes)];

        EmployeeShift::create([
            'employee_id' => $employee->emp_no,
            'shift_name' => $shiftType['name'],
            'start_time' => $shiftType['start_time'],
            'end_time' => $shiftType['end_time'],
            'break_start' => $shiftType['break_start'],
            'break_end' => $shiftType['break_end'],
            'grace_period_minutes' => $shiftType['grace_period_minutes'],
            'is_active' => true,
            'working_days' => $shiftType['working_days'],
            'effective_from' => Carbon::now()->subMonths(3),
            'effective_to' => null,
            'notes' => 'Default shift assignment'
        ]);
    }

    /**
     * Create attendance records for employees.
     */
    private function createAttendanceRecords($employees): void
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        foreach ($employees as $employee) {
            $shift = EmployeeShift::where('employee_id', $employee->emp_no)
                ->where('is_active', true)
                ->first();

            if (!$shift) {
                continue;
            }

            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                // Skip weekends if employee doesn't work weekends
                if (!in_array($currentDate->dayOfWeek, $shift->working_days)) {
                    $currentDate->addDay();
                    continue;
                }

                // Determine attendance status for this day
                $status = $this->determineAttendanceStatus($employee, $currentDate);
                
                if ($status !== 'No Record') {
                    $this->createAttendanceRecord($employee, $shift, $currentDate, $status);
                }

                $currentDate->addDay();
            }
        }
    }

    /**
     * Determine attendance status for a given day.
     */
    private function determineAttendanceStatus($employee, $date): string
    {
        // 85% chance of being present
        $random = rand(1, 100);
        
        if ($random <= 85) {
            // Present, but could be late
            return rand(1, 100) <= 15 ? 'Late' : 'Present';
        } elseif ($random <= 90) {
            // Absent
            return 'Absent';
        } elseif ($random <= 95) {
            // Remote work
            return 'Remote';
        } else {
            // Leave
            return 'Leave';
        }
    }

    /**
     * Create a single attendance record.
     */
    private function createAttendanceRecord($employee, $shift, $date, $status): void
    {
        $checkIn = null;
        $checkOut = null;
        $totalHours = 0;
        $overtimeHours = 0;
        $checkInStatus = null;
        $checkOutStatus = null;

        if (in_array($status, ['Present', 'Late', 'Remote'])) {
            // Generate check-in time
            $scheduledStart = Carbon::parse($shift->start_time);
            $gracePeriod = $shift->grace_period_minutes;
            
            if ($status === 'Late') {
                // Late check-in (after grace period)
                $checkIn = $scheduledStart->copy()->addMinutes($gracePeriod + rand(5, 30));
                $checkInStatus = 'Late';
            } else {
                // On time or early check-in
                $checkIn = $scheduledStart->copy()->addMinutes(rand(-30, $gracePeriod));
                $checkInStatus = 'On Time';
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

            $checkOutStatus = 'On Time';
        }

        // Determine approval status
        $isApproved = rand(1, 100) <= 80; // 80% chance of being approved
        $approvedBy = $isApproved ? User::first()?->id : null;
        $approvedAt = $isApproved ? $date->copy()->addHours(rand(1, 8)) : null;

        Attendance::create([
            'employee_id' => $employee->emp_no,
            'date' => $date->format('Y-m-d'),
            'scheduled_start' => $shift->start_time,
            'scheduled_end' => $shift->end_time,
            'check_in' => $checkIn ? $checkIn->format('H:i') : null,
            'check_out' => $checkOut ? $checkOut->format('H:i') : null,
            'total_hours' => round($totalHours, 2),
            'overtime_hours' => round($overtimeHours, 2),
            'break_hours' => $shift->break_start ? 1.0 : 0,
            'status' => $status,
            'check_in_status' => $checkInStatus,
            'check_out_status' => $checkOutStatus,
            'check_in_location' => $status === 'Remote' ? 'Remote - ' . fake()->city() : fake()->optional()->city(),
            'check_out_location' => $status === 'Remote' ? 'Remote - ' . fake()->city() : fake()->optional()->city(),
            'check_in_ip' => fake()->ipv4(),
            'check_out_ip' => fake()->optional()->ipv4(),
            'notes' => $this->generateNotes($status),
            'manager_notes' => fake()->optional()->sentence(),
            'is_approved' => $isApproved,
            'approved_by' => $approvedBy,
            'approved_at' => $approvedAt,
        ]);
    }

    /**
     * Generate appropriate notes based on status.
     */
    private function generateNotes($status): ?string
    {
        $notes = [
            'Present' => null,
            'Late' => fake()->randomElement(['Traffic delay', 'Public transport issue', 'Personal emergency']),
            'Absent' => fake()->randomElement(['Sick leave', 'Personal leave', 'No show']),
            'Remote' => 'Working from home',
            'Leave' => fake()->randomElement(['Annual leave', 'Sick leave', 'Personal leave', 'Maternity leave']),
        ];

        return $notes[$status] ?? null;
    }
} 