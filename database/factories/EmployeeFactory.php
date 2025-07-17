<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nationalities = ['Maldives', 'Bangladesh', 'India', 'Sri Lanka', 'Nepal', 'Pakistan', 'Philippines'];
        $employmentStatuses = ['Active', 'Terminated', 'Resigned', 'Rejoined', 'Dead', 'Retired', 'Missing'];
        $companies = [
            'RASHEED CARPENTRY AND CONSTRUCTION PVT LTD',
            'NAZRASH COMPANY PVT LTD'
        ];

        $gender = $this->getRandomGender();
        $firstName = $this->getRandomFirstName($gender);
        $lastName = $this->getRandomLastName();
        $fullName = $firstName . ' ' . $lastName;

        return [
            'emp_no' => $this->generateEmployeeNumber(),
            'name' => $fullName,
            'gender' => $gender,
            'designation' => $this->getRandomDesignation(),
            'xpat_designation' => $this->getRandomXpatDesignation(),
            'xpat_join_date' => $this->getRandomDate(),
            'department' => $this->getRandomDepartment(),
            'nationality' => $nationalities[array_rand($nationalities)],
            'passport_nic_no' => $this->generatePassportNumber(),
            'passport_expire_date' => $this->getRandomFutureDate(),
            'dob' => $this->getRandomDOB(),
            'wp_no' => $this->generateWorkPermitNumber(),
            'date_of_join' => $this->getRandomJoinDate(),
            'contact_number' => $this->generatePhoneNumber(),
            'contact_number_foregn' => $this->generateForeignPhoneNumber(),
            'emergency_contact_number' => $this->generatePhoneNumber(),
            'emergency_contact_name' => $this->getRandomName(),
            'employment_status' => $employmentStatuses[array_rand($employmentStatuses)],
            'work_site' => $this->getRandomWorkSite(),
            'insurance_provider' => $this->getRandomInsuranceProvider(),
            'recruiting_agency' => $this->getRandomRecruitingAgency(),
            'emp_email' => $this->generateEmail($firstName, $lastName),
            'company_email' => $this->generateCompanyEmail($firstName, $lastName),
            'permanent_address' => $this->getRandomAddress(),
            'persent_address' => $this->getRandomAddress(),
            'basic_salary' => $this->getRandomSalary(),
            'salary_currency' => ['MVR', 'USD'][array_rand([0, 1])],
            'termination_date' => null, // Will be set if terminated
            'level' => ['senior', 'junior'][array_rand([0, 1])],
            'company' => $companies[array_rand($companies)],
        ];
    }

    /**
     * Generate unique employee number
     */
    private function generateEmployeeNumber(): string
    {
        static $counter = 1;
        return str_pad($counter++, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get random gender
     */
    private function getRandomGender(): string
    {
        return ['Male', 'Female'][array_rand([0, 1])];
    }

    /**
     * Get random first name based on gender
     */
    private function getRandomFirstName(string $gender): string
    {
        $maleNames = ['Ahmed', 'Mohamed', 'Ali', 'Hassan', 'Ibrahim', 'Abdullah', 'Omar', 'Yusuf', 'Khalid', 'Zain'];
        $femaleNames = ['Aisha', 'Fatima', 'Mariam', 'Zara', 'Layla', 'Noor', 'Hana', 'Sara', 'Yasmin', 'Amira'];
        
        return $gender === 'Male' ? $maleNames[array_rand($maleNames)] : $femaleNames[array_rand($femaleNames)];
    }

    /**
     * Get random last name
     */
    private function getRandomLastName(): string
    {
        $lastNames = ['Hassan', 'Ahmed', 'Mohamed', 'Ali', 'Rahman', 'Khan', 'Singh', 'Patel', 'Fernando', 'Silva'];
        return $lastNames[array_rand($lastNames)];
    }

    /**
     * Get random name
     */
    private function getRandomName(): string
    {
        return $this->getRandomFirstName($this->getRandomGender()) . ' ' . $this->getRandomLastName();
    }

    /**
     * Get random designation
     */
    private function getRandomDesignation(): string
    {
        $designations = [
            'Software Developer', 'HR Officer', 'Accountant', 'Project Manager', 'Site Engineer',
            'Carpenter', 'Electrician', 'Plumber', 'Driver', 'Security Guard', 'Cleaner',
            'Administrative Assistant', 'Sales Representative', 'Customer Service Officer'
        ];
        return $designations[array_rand($designations)];
    }

    /**
     * Get random expat designation
     */
    private function getRandomXpatDesignation(): ?string
    {
        $designations = [
            'Senior Engineer', 'Project Director', 'Technical Specialist', 'Quality Manager',
            'Safety Officer', 'Site Supervisor', 'Foreman', null
        ];
        return $designations[array_rand($designations)];
    }

    /**
     * Get random department
     */
    private function getRandomDepartment(): string
    {
        $departments = ['HR', 'Finance', 'Operations', 'Engineering', 'Administration', 'IT', 'Sales', 'Marketing'];
        return $departments[array_rand($departments)];
    }

    /**
     * Generate passport number
     */
    private function generatePassportNumber(): ?string
    {
        if (rand(1, 100) <= 80) { // 80% chance of having passport
            return strtoupper(substr(md5(rand()), 0, 8));
        }
        return null;
    }

    /**
     * Get random future date
     */
    private function getRandomFutureDate(): ?string
    {
        if (rand(1, 100) <= 70) { // 70% chance of having expiry date
            return Carbon::now()->addYears(rand(1, 10))->format('Y-m-d');
        }
        return null;
    }

    /**
     * Get random date of birth
     */
    private function getRandomDOB(): string
    {
        return Carbon::now()->subYears(rand(20, 60))->format('Y-m-d');
    }

    /**
     * Generate work permit number
     */
    private function generateWorkPermitNumber(): ?string
    {
        if (rand(1, 100) <= 60) { // 60% chance of having work permit
            return 'WP' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
        return null;
    }

    /**
     * Get random join date
     */
    private function getRandomJoinDate(): string
    {
        return Carbon::now()->subYears(rand(0, 5))->subMonths(rand(0, 11))->format('Y-m-d');
    }

    /**
     * Generate phone number
     */
    private function generatePhoneNumber(): ?string
    {
        if (rand(1, 100) <= 90) { // 90% chance of having phone
            return '+960 ' . rand(7000000, 9999999);
        }
        return null;
    }

    /**
     * Generate foreign phone number
     */
    private function generateForeignPhoneNumber(): ?string
    {
        if (rand(1, 100) <= 30) { // 30% chance of having foreign phone
            $codes = ['+880', '+91', '+94', '+977', '+92'];
            return $codes[array_rand($codes)] . ' ' . rand(100000000, 999999999);
        }
        return null;
    }

    /**
     * Get random work site
     */
    private function getRandomWorkSite(): ?string
    {
        $sites = ['Male Site', 'Hulhumale Site', 'Addu Site', 'Fuvahmulah Site', 'Thilafushi Site', null];
        return $sites[array_rand($sites)];
    }

    /**
     * Get random insurance provider
     */
    private function getRandomInsuranceProvider(): ?string
    {
        $providers = ['Amana Takaful', 'Allianz', 'HDFC', 'Axa', null];
        return $providers[array_rand($providers)];
    }

    /**
     * Get random recruiting agency
     */
    private function getRandomRecruitingAgency(): ?string
    {
        $agencies = ['ABC Recruitment', 'Global Staffing', 'Pro HR Solutions', 'Elite Recruitment', null];
        return $agencies[array_rand($agencies)];
    }

    /**
     * Generate email
     */
    private function generateEmail(string $firstName, string $lastName): ?string
    {
        if (rand(1, 100) <= 80) { // 80% chance of having email
            $domains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];
            return strtolower($firstName . '.' . $lastName . '@' . $domains[array_rand($domains)]);
        }
        return null;
    }

    /**
     * Generate company email
     */
    private function generateCompanyEmail(string $firstName, string $lastName): ?string
    {
        if (rand(1, 100) <= 70) { // 70% chance of having company email
            return strtolower($firstName . '.' . $lastName . '@rcc.com.mv');
        }
        return null;
    }

    /**
     * Get random address
     */
    private function getRandomAddress(): string
    {
        $cities = ['Male', 'Hulhumale', 'Addu City', 'Fuvahmulah', 'Thilafushi', 'Villingili'];
        $city = $cities[array_rand($cities)];
        $street = rand(1, 100) . ' Street';
        return $street . ', ' . $city . ', Maldives';
    }

    /**
     * Get random salary
     */
    private function getRandomSalary(): float
    {
        return round(rand(5000, 50000), 2);
    }

    /**
     * Get random date
     */
    private function getRandomDate(): ?string
    {
        if (rand(1, 100) <= 50) { // 50% chance of having date
            return Carbon::now()->subYears(rand(0, 3))->format('Y-m-d');
        }
        return null;
    }

    /**
     * Indicate that the employee is active.
     */
    public function active(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'employment_status' => 'Active',
                'termination_date' => null,
            ];
        });
    }

    /**
     * Indicate that the employee is terminated.
     */
    public function terminated(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'employment_status' => 'Terminated',
                'termination_date' => Carbon::now()->subMonths(rand(1, 12))->format('Y-m-d'),
            ];
        });
    }

    /**
     * Indicate that the employee is senior level.
     */
    public function senior(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'level' => 'senior',
                'basic_salary' => round(rand(25000, 50000), 2),
            ];
        });
    }

    /**
     * Indicate that the employee is junior level.
     */
    public function junior(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'level' => 'junior',
                'basic_salary' => round(rand(5000, 25000), 2),
            ];
        });
    }

    /**
     * Indicate that the employee is from Bangladesh.
     */
    public function bangladeshi(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'nationality' => 'BANGLADESHI',
                'contact_number_foregn' => '+880 ' . rand(100000000, 999999999),
            ];
        });
    }

    /**
     * Indicate that the employee is from India.
     */
    public function indian(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'nationality' => 'INDIAN',
                'contact_number_foregn' => '+91 ' . rand(100000000, 999999999),
            ];
        });
    }

    /**
     * Indicate that the employee is from Sri Lanka.
     */
    public function srilankan(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'nationality' => 'SRI LANKAN',
                'contact_number_foregn' => '+94 ' . rand(100000000, 999999999),
            ];
        });
    }

    /**
     * Indicate that the employee is from Nepal.
     */
    public function nepali(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'nationality' => 'NEPALI',
                'contact_number_foregn' => '+977 ' . rand(100000000, 999999999),
            ];
        });
    }

    /**
     * Indicate that the employee is from Pakistan.
     */
    public function pakistani(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'nationality' => 'PAKISTANI',
                'contact_number_foregn' => '+92 ' . rand(100000000, 999999999),
            ];
        });
    }
} 