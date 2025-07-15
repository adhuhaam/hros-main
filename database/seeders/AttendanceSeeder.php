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
            
            // Create 2 simple employees without factory
            Employee::create([
                'emp_no' => '001',
                'name' => 'John Doe',
                'gender' => 'Male',
                'designation' => 'Software Developer',
                'department' => 'IT',
                'nationality' => 'Maldives',
                'date_of_join' => '2024-01-01',
                'contact_number' => '+960 1234567',
                'emergency_contact_number' => '+960 7654321',
                'emergency_contact_name' => 'Jane Doe',
                'employment_status' => 'Active',
                'emp_email' => 'john@rcc.com.mv',
                'company_email' => 'john.doe@rcc.com.mv',
                'permanent_address' => 'Male, Maldives',
                'persent_address' => 'Male, Maldives',
                'basic_salary' => 15000.00,
                'salary_currency' => 'MVR',
                'level' => 'senior',
                'company' => 'RASHEED CARPENTRY AND CONSTRUCTION PVT LTD',
            ]);

            Employee::create([
                'emp_no' => '002',
                'name' => 'Jane Smith',
                'gender' => 'Female',
                'designation' => 'HR Officer',
                'department' => 'HR',
                'nationality' => 'Maldives',
                'date_of_join' => '2024-01-15',
                'contact_number' => '+960 2345678',
                'emergency_contact_number' => '+960 8765432',
                'emergency_contact_name' => 'John Smith',
                'employment_status' => 'Active',
                'emp_email' => 'jane@rcc.com.mv',
                'company_email' => 'jane.smith@rcc.com.mv',
                'permanent_address' => 'Male, Maldives',
                'persent_address' => 'Male, Maldives',
                'basic_salary' => 12000.00,
                'salary_currency' => 'MVR',
                'level' => 'junior',
                'company' => 'RASHEED CARPENTRY AND CONSTRUCTION PVT LTD',
            ]);

            $employees = Employee::all();
        }

        // Create shifts for each employee
        $this->command->info('Creating employee shifts...');
        foreach ($employees as $employee) {
            $this->createEmployeeShift($employee);
        }

        // Create attendance records for the last 2 days only
        $this->command->info('Creating attendance records...');
        $this->createAttendanceRecords($employees);

        $this->command->info('Attendance seeding completed!');
    }

    /**
     * Create a shift for an employee.
     */
    private function createEmployeeShift($employee): void
    {
        EmployeeShift::create([
            'employee_id' => $employee->emp_no,
            'shift_name' => 'Morning Shift',
            'start_time' => '08:00',
            'end_time' => '16:00',
            'break_start' => '12:00',
            'break_end' => '13:00',
            'grace_period_minutes' => 15,
            'is_active' => true,
            'working_days' => [1, 2, 3, 4, 5], // Monday to Friday
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
        $startDate = Carbon::now()->subDays(2);
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
                // Skip weekends
                if (!in_array($currentDate->dayOfWeek, $shift->working_days)) {
                    $currentDate->addDay();
                    continue;
                }

                // Create simple attendance record
                $this->createSimpleAttendanceRecord($employee, $shift, $currentDate);

                $currentDate->addDay();
            }
        }
    }

    /**
     * Create a simple attendance record.
     */
    private function createSimpleAttendanceRecord($employee, $shift, $date): void
    {
        $status = 'Present';
        $checkIn = '08:15';
        $checkOut = '16:00';
        $totalHours = 7.75;
        $overtimeHours = 0;

        Attendance::create([
            'employee_id' => $employee->emp_no,
            'date' => $date->format('Y-m-d'),
            'scheduled_start' => $shift->start_time,
            'scheduled_end' => $shift->end_time,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'total_hours' => $totalHours,
            'overtime_hours' => $overtimeHours,
            'break_hours' => 1.0,
            'status' => $status,
            'check_in_status' => 'On Time',
            'check_out_status' => 'On Time',
            'check_in_location' => 'Office',
            'check_out_location' => 'Office',
            'check_in_ip' => '192.168.1.100',
            'check_out_ip' => '192.168.1.100',
            'notes' => null,
            'manager_notes' => 'Approved',
            'is_approved' => true,
            'approved_by' => User::first()?->id,
            'approved_at' => $date->copy()->addHours(2),
        ]);
    }
} 