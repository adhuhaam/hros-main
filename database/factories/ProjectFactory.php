<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = Carbon::now()->subMonths(rand(1, 12));
        $endDate = $startDate->copy()->addMonths(rand(3, 24));
        
        return [
            'name' => $this->getRandomProjectName(),
            'description' => $this->getRandomDescription(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'status' => $this->getRandomStatus(),
            'budget' => $this->getRandomBudget(),
            'client_name' => $this->getRandomClient(),
            'project_manager' => $this->getRandomManager(),
            'location' => $this->getRandomLocation(),
        ];
    }

    /**
     * Get random project name
     */
    private function getRandomProjectName(): string
    {
        $projectTypes = ['Residential', 'Commercial', 'Infrastructure', 'Renovation', 'Construction'];
        $projectNames = [
            'Luxury Villa Development',
            'Office Complex Construction',
            'Road Infrastructure Project',
            'Shopping Mall Renovation',
            'Apartment Building Construction',
            'Hotel Development',
            'Bridge Construction',
            'School Building Project',
            'Hospital Extension',
            'Industrial Warehouse'
        ];
        
        $type = $projectTypes[array_rand($projectTypes)];
        $name = $projectNames[array_rand($projectNames)];
        
        return $type . ' - ' . $name;
    }

    /**
     * Get random description
     */
    private function getRandomDescription(): string
    {
        $descriptions = [
            'Complete construction project including planning, execution, and handover',
            'Infrastructure development with modern amenities and sustainable practices',
            'Renovation and modernization of existing facilities',
            'Large-scale construction project with multiple phases',
            'Commercial development with retail and office spaces',
            'Residential complex with luxury amenities and modern design',
            'Public infrastructure project serving the community',
            'Industrial construction with specialized requirements'
        ];
        
        return $descriptions[array_rand($descriptions)];
    }

    /**
     * Get random status
     */
    private function getRandomStatus(): string
    {
        $statuses = ['Planning', 'In Progress', 'On Hold', 'Completed', 'Cancelled'];
        return $statuses[array_rand($statuses)];
    }

    /**
     * Get random budget
     */
    private function getRandomBudget(): float
    {
        return round(rand(500000, 50000000), 2);
    }

    /**
     * Get random client name
     */
    private function getRandomClient(): string
    {
        $clients = [
            'Ministry of Construction',
            'Male City Council',
            'Hulhumale Development Corporation',
            'Addu City Council',
            'Private Developer Group',
            'International Construction Co.',
            'Local Business Consortium',
            'Government Infrastructure Authority'
        ];
        
        return $clients[array_rand($clients)];
    }

    /**
     * Get random project manager
     */
    private function getRandomManager(): string
    {
        $managers = [
            'Ahmed Hassan',
            'Mohamed Ali',
            'Ibrahim Rahman',
            'Abdullah Khan',
            'Omar Yusuf',
            'Fatima Ahmed',
            'Aisha Mohamed',
            'Mariam Ali'
        ];
        
        return $managers[array_rand($managers)];
    }

    /**
     * Get random location
     */
    private function getRandomLocation(): string
    {
        $locations = [
            'Male, Maldives',
            'Hulhumale, Maldives',
            'Addu City, Maldives',
            'Fuvahmulah, Maldives',
            'Thilafushi, Maldives',
            'Villingili, Maldives',
            'Hithadhoo, Maldives',
            'Gan, Maldives'
        ];
        
        return $locations[array_rand($locations)];
    }

    /**
     * Indicate that the project is in planning phase.
     */
    public function planning(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Planning',
                'start_date' => Carbon::now()->addMonths(rand(1, 6))->format('Y-m-d'),
            ];
        });
    }

    /**
     * Indicate that the project is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'In Progress',
                'start_date' => Carbon::now()->subMonths(rand(1, 12))->format('Y-m-d'),
            ];
        });
    }

    /**
     * Indicate that the project is completed.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Completed',
                'start_date' => Carbon::now()->subMonths(rand(6, 24))->format('Y-m-d'),
                'end_date' => Carbon::now()->subMonths(rand(1, 6))->format('Y-m-d'),
            ];
        });
    }

    /**
     * Indicate that the project is on hold.
     */
    public function onHold(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'On Hold',
            ];
        });
    }

    /**
     * Indicate that the project is residential.
     */
    public function residential(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Residential - ' . ['Luxury Villa Development', 'Apartment Complex', 'Housing Project'][array_rand([0, 1, 2])],
            ];
        });
    }

    /**
     * Indicate that the project is commercial.
     */
    public function commercial(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Commercial - ' . ['Office Complex', 'Shopping Mall', 'Hotel Development'][array_rand([0, 1, 2])],
            ];
        });
    }

    /**
     * Indicate that the project is infrastructure.
     */
    public function infrastructure(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Infrastructure - ' . ['Road Project', 'Bridge Construction', 'Public Facility'][array_rand([0, 1, 2])],
            ];
        });
    }
} 