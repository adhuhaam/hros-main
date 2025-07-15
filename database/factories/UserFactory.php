<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = $this->getRandomFirstName();
        $lastName = $this->getRandomLastName();
        $fullName = $firstName . ' ' . $lastName;
        $username = $this->generateUsername($firstName, $lastName);
        $email = $this->generateEmail($firstName, $lastName);

        return [
            'emp_no' => $this->generateEmployeeNumber(),
            'username' => $username,
            'staff_name' => $fullName,
            'des' => $this->getRandomDesignation(),
            'email' => $email,
            'password' => Hash::make('password'), // Default password
            'role_id' => rand(1, 14),
            'remember_token' => Str::random(10),
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
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
     * Get random first name
     */
    private function getRandomFirstName(): string
    {
        $names = ['Ahmed', 'Mohamed', 'Ali', 'Hassan', 'Ibrahim', 'Abdullah', 'Omar', 'Yusuf', 'Khalid', 'Zain', 'Aisha', 'Fatima', 'Mariam', 'Zara', 'Layla', 'Noor', 'Hana', 'Sara', 'Yasmin', 'Amira'];
        return $names[array_rand($names)];
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
     * Generate username
     */
    private function generateUsername(string $firstName, string $lastName): string
    {
        static $counter = 1;
        $baseUsername = strtolower($firstName . '.' . $lastName);
        return $baseUsername . $counter++;
    }

    /**
     * Generate email
     */
    private function generateEmail(string $firstName, string $lastName): string
    {
        static $counter = 1;
        $baseEmail = strtolower($firstName . '.' . $lastName);
        return $baseEmail . $counter++ . '@rcc.com.mv';
    }

    /**
     * Get random designation
     */
    private function getRandomDesignation(): string
    {
        $designations = [
            'System Administrator', 'HR Manager', 'Finance Manager', 'Project Manager', 
            'Site Engineer', 'Accountant', 'HR Officer', 'Administrative Assistant',
            'IT Support', 'Operations Manager', 'Safety Officer', 'Quality Manager'
        ];
        return $designations[array_rand($designations)];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => 1, // Assuming role_id 1 is admin
                'username' => 'admin',
                'email' => 'admin@rcc.com.mv',
            ];
        });
    }

    /**
     * Indicate that the user is HR staff.
     */
    public function hr(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => 2, // Assuming role_id 2 is HR
                'des' => 'HR Officer',
            ];
        });
    }

    /**
     * Indicate that the user is a manager.
     */
    public function manager(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => 3, // Assuming role_id 3 is manager
                'des' => 'Project Manager',
            ];
        });
    }
}
