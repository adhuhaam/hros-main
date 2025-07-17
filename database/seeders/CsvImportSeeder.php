<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use App\Models\AttendanceRecord;
use App\Models\LeaveRecord;
use App\Models\Warning;
use App\Models\SalaryIncome;
use App\Models\SalaryDeduction;
use Illuminate\Support\Facades\DB;

class CsvImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only run in local/testing environments
        if (!app()->environment(['local', 'testing'])) {
            return;
        }

        $this->command->info('Importing data from CSV files...');

        // Import employees from CSV
        $this->command->info('Importing employees...');
        $this->importEmployeesFromCsv();

        // Import users from CSV (if exists)
        $this->command->info('Importing users...');
        $this->importUsersFromCsv();

        // Import attendance records from CSV (if exists)
        $this->command->info('Importing attendance records...');
        $this->importAttendanceFromCsv();

        $this->command->info('CSV import completed successfully!');
    }

    /**
     * Import employees from CSV file
     */
    private function importEmployeesFromCsv(): void
    {
        $csvPath = database_path('seeders/data/employees_template.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->warn('CSV file not found: ' . $csvPath);
            $this->command->info('Please create the CSV file with employee data first.');
            return;
        }

        $file = fopen($csvPath, 'r');
        if (!$file) {
            $this->command->error('Could not open CSV file: ' . $csvPath);
            return;
        }

        // Read header
        $headers = fgetcsv($file);
        if (!$headers) {
            $this->command->error('Could not read CSV headers');
            fclose($file);
            return;
        }

        $employees = [];
        $rowCount = 0;

        // Read data rows
        while (($row = fgetcsv($file)) !== false) {
            $rowCount++;
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Combine headers with row data
            $employeeData = array_combine($headers, $row);
            
            // Clean and validate data
            $employeeData = $this->cleanEmployeeData($employeeData);
            
            if ($employeeData) {
                $employees[] = $employeeData;
            }
        }

        fclose($file);

        if (empty($employees)) {
            $this->command->warn('No valid employee data found in CSV');
            return;
        }

        // Insert employees in batches
        $chunks = array_chunk($employees, 10);
        foreach ($chunks as $chunk) {
            foreach ($chunk as $employee) {
                try {
                    Employee::create($employee);
                } catch (\Exception $e) {
                    $this->command->error('Error creating employee ' . $employee['emp_no'] . ': ' . $e->getMessage());
                }
            }
        }

        $this->command->info("Imported {$rowCount} employees successfully");
    }

    /**
     * Clean and validate employee data
     */
    private function cleanEmployeeData(array $data): ?array
    {
        // Remove any empty or null values
        $data = array_filter($data, function($value) {
            return $value !== null && $value !== '';
        });

        // Ensure required fields exist
        $requiredFields = ['emp_no', 'name', 'gender', 'designation', 'department', 'nationality', 'date_of_join'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $this->command->warn("Missing required field: {$field}");
                return null;
            }
        }

        // Validate enum fields
        $validGenders = ['Male', 'Female'];
        if (isset($data['gender']) && !in_array($data['gender'], $validGenders)) {
            $this->command->warn("Invalid gender: {$data['gender']}");
            return null;
        }

        $validCompanies = ['RASHEED CARPENTRY AND CONSTRUCTION PVT LTD', 'NAZRASH COMPANY PVT LTD'];
        if (isset($data['company']) && !in_array($data['company'], $validCompanies)) {
            $this->command->warn("Invalid company: {$data['company']}");
            return null;
        }

        $validStatuses = ['Active', 'Terminated', 'Resigned', 'Rejoined', 'Dead', 'Retired', 'Missing'];
        if (isset($data['employment_status']) && !in_array($data['employment_status'], $validStatuses)) {
            $this->command->warn("Invalid employment status: {$data['employment_status']}");
            return null;
        }

        $validCurrencies = ['MVR', 'USD'];
        if (isset($data['salary_currency']) && !in_array($data['salary_currency'], $validCurrencies)) {
            $this->command->warn("Invalid salary currency: {$data['salary_currency']}");
            return null;
        }

        $validLevels = ['senior', 'junior'];
        if (isset($data['level']) && !in_array($data['level'], $validLevels)) {
            $this->command->warn("Invalid level: {$data['level']}");
            return null;
        }

        // Convert numeric fields
        if (isset($data['basic_salary'])) {
            $data['basic_salary'] = (float) $data['basic_salary'];
        }

        if (isset($data['month'])) {
            $data['month'] = (int) $data['month'];
        }

        if (isset($data['year'])) {
            $data['year'] = (int) $data['year'];
        }

        if (isset($data['day'])) {
            $data['day'] = (int) $data['day'];
        }

        return $data;
    }

    /**
     * Import users from CSV file
     */
    private function importUsersFromCsv(): void
    {
        $csvPath = database_path('seeders/data/users.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->info('Users CSV file not found, skipping user import');
            return;
        }

        $file = fopen($csvPath, 'r');
        if (!$file) {
            $this->command->error('Could not open users CSV file: ' . $csvPath);
            return;
        }

        // Read header
        $headers = fgetcsv($file);
        if (!$headers) {
            $this->command->error('Could not read users CSV headers');
            fclose($file);
            return;
        }

        $users = [];
        $rowCount = 0;

        // Read data rows
        while (($row = fgetcsv($file)) !== false) {
            $rowCount++;
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Combine headers with row data
            $userData = array_combine($headers, $row);
            
            // Clean and validate data
            $userData = $this->cleanUserData($userData);
            
            if ($userData) {
                $users[] = $userData;
            }
        }

        fclose($file);

        if (empty($users)) {
            $this->command->warn('No valid user data found in CSV');
            return;
        }

        // Insert users
        foreach ($users as $user) {
            try {
                User::create($user);
            } catch (\Exception $e) {
                $this->command->error('Error creating user ' . $user['emp_no'] . ': ' . $e->getMessage());
            }
        }

        $this->command->info("Imported {$rowCount} users successfully");
    }

    /**
     * Clean and validate user data
     */
    private function cleanUserData(array $data): ?array
    {
        // Remove any empty or null values
        $data = array_filter($data, function($value) {
            return $value !== null && $value !== '';
        });

        // Ensure required fields exist
        $requiredFields = ['emp_no', 'username', 'staff_name', 'des', 'email', 'password', 'role_id'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $this->command->warn("Missing required user field: {$field}");
                return null;
            }
        }

        // Hash password if not already hashed
        if (isset($data['password']) && !str_starts_with($data['password'], '$2y$')) {
            $data['password'] = bcrypt($data['password']);
        }

        // Convert role_id to integer
        if (isset($data['role_id'])) {
            $data['role_id'] = (int) $data['role_id'];
        }

        return $data;
    }

    /**
     * Import attendance records from CSV file
     */
    private function importAttendanceFromCsv(): void
    {
        $csvPath = database_path('seeders/data/attendance.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->info('Attendance CSV file not found, skipping attendance import');
            return;
        }

        $file = fopen($csvPath, 'r');
        if (!$file) {
            $this->command->error('Could not open attendance CSV file: ' . $csvPath);
            return;
        }

        // Read header
        $headers = fgetcsv($file);
        if (!$headers) {
            $this->command->error('Could not read attendance CSV headers');
            fclose($file);
            return;
        }

        $records = [];
        $rowCount = 0;

        // Read data rows
        while (($row = fgetcsv($file)) !== false) {
            $rowCount++;
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Combine headers with row data
            $recordData = array_combine($headers, $row);
            
            // Clean and validate data
            $recordData = $this->cleanAttendanceData($recordData);
            
            if ($recordData) {
                $records[] = $recordData;
            }
        }

        fclose($file);

        if (empty($records)) {
            $this->command->warn('No valid attendance data found in CSV');
            return;
        }

        // Insert records in batches
        $chunks = array_chunk($records, 50);
        foreach ($chunks as $chunk) {
            try {
                DB::table('attendance_records')->insert($chunk);
            } catch (\Exception $e) {
                $this->command->error('Error inserting attendance records: ' . $e->getMessage());
            }
        }

        $this->command->info("Imported {$rowCount} attendance records successfully");
    }

    /**
     * Clean and validate attendance data
     */
    private function cleanAttendanceData(array $data): ?array
    {
        // Remove any empty or null values
        $data = array_filter($data, function($value) {
            return $value !== null && $value !== '';
        });

        // Ensure required fields exist
        $requiredFields = ['emp_no', 'month', 'year', 'day', 'day_type', 'shift', 'present_absent', 'status'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $this->command->warn("Missing required attendance field: {$field}");
                return null;
            }
        }

        // Validate enum fields
        $validDayTypes = ['Weekday', 'Weekend', 'Holiday'];
        if (isset($data['day_type']) && !in_array($data['day_type'], $validDayTypes)) {
            $this->command->warn("Invalid day type: {$data['day_type']}");
            return null;
        }

        $validShifts = ['Morning', 'Evening', 'Night', 'Day'];
        if (isset($data['shift']) && !in_array($data['shift'], $validShifts)) {
            $this->command->warn("Invalid shift: {$data['shift']}");
            return null;
        }

        $validPresentAbsent = ['Present', 'Absent', 'Late', 'Half Day'];
        if (isset($data['present_absent']) && !in_array($data['present_absent'], $validPresentAbsent)) {
            $this->command->warn("Invalid present/absent: {$data['present_absent']}");
            return null;
        }

        $validStatuses = ['Approved', 'Pending', 'Rejected'];
        if (isset($data['status']) && !in_array($data['status'], $validStatuses)) {
            $this->command->warn("Invalid status: {$data['status']}");
            return null;
        }

        // Convert numeric fields
        if (isset($data['month'])) {
            $data['month'] = (int) $data['month'];
        }

        if (isset($data['year'])) {
            $data['year'] = (int) $data['year'];
        }

        if (isset($data['day'])) {
            $data['day'] = (int) $data['day'];
        }

        return $data;
    }
} 