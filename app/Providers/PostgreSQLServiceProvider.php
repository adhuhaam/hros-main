<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PostgreSQLServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only register if using PostgreSQL
        if (config('database.default') === 'pgsql') {
            $this->registerPostgreSQLTypes();
        }
    }

    /**
     * Register custom PostgreSQL types with Laravel Schema Builder
     */
    private function registerPostgreSQLTypes(): void
    {
        // Register enum types
        $enumTypes = [
            'employment_status_enum',
            'salary_currency_enum', 
            'employee_level_enum',
            'company_enum',
            'leave_type_enum',
            'leave_status_enum',
            'leave_record_status_enum',
            'gender_restriction_enum',
            'attendance_status_enum',
            'check_status_enum',
            'loan_type_enum',
            'loan_status_enum',
            'project_status_enum',
            'warning_type_enum',
            'warning_status_enum',
            'severity_level_enum',
            'medical_type_enum',
            'medical_status_enum',
            'medical_examination_status_enum',
            'bank_status_enum',
            'print_type_enum',
            'card_status_enum',
            'payment_status_enum',
            'sender_receiver_type_enum',
            'role_enum',
            'send_type_enum',
            'mail_status_enum',
            'visa_status_enum',
            'visa_renewal_status_enum',
            'collection_status_enum',
            'direction_enum',
            'passport_status_enum',
            'ticket_type_enum',
            'ticket_status_enum',
            'contract_type_enum',
            'contract_status_enum',
            'contract_variant_enum',
            'payroll_status_enum',
            'payroll_approval_status_enum',
            'allowance_type_enum',
            'general_status_enum',
            'active_inactive_enum'
        ];

        foreach ($enumTypes as $enumType) {
            $this->registerEnumType($enumType);
        }
    }

    /**
     * Register a custom enum type with the schema builder
     */
    private function registerEnumType(string $typeName): void
    {
        Blueprint::macro('addColumn', function ($type, $name, $parameters = []) {
            return $this->addColumnDefinition($this->createColumn($type, $name, $parameters));
        });

        // Register the custom column type
        Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping($typeName, 'string');
    }
} 