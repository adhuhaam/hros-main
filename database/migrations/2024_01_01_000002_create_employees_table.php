<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->string('emp_no', 10)->primary();
            $table->string('name', 255)->nullable();
            $table->string('gender', 191);
            $table->string('designation', 255)->nullable();
            $table->string('xpat_designation', 225)->nullable();
            $table->date('xpat_join_date')->nullable();
            $table->string('department', 255)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('passport_nic_no', 100)->nullable();
            $table->date('passport_nic_no_expires')->nullable();
            $table->date('dob')->nullable();
            $table->string('wp_no', 100)->nullable();
            $table->date('date_of_join')->nullable();
            $table->string('contact_number', 15)->nullable();
            $table->string('contact_number_foregn', 225)->nullable();
            $table->string('emergency_contact_number', 15)->nullable();
            $table->string('emergency_contact_name', 255)->nullable();
            $table->string('work_site', 255)->nullable();
            $table->string('insurance_provider', 255)->nullable();
            $table->string('recruiting_agency', 255)->nullable();
            $table->string('emp_email', 225)->nullable();
            $table->string('company_email', 255)->nullable();
            $table->string('permanent_address', 1500)->nullable();
            $table->string('persent_address', 2000);
            $table->decimal('basic_salary', 10, 2)->nullable();
            $table->date('termination_date')->nullable();
            $table->string('player_id', 100)->nullable();
            
            // Indexes
            $table->index('name');
            $table->index('emp_no');
        });

        // Add enum columns using raw SQL for PostgreSQL compatibility
        DB::statement('ALTER TABLE employees ADD COLUMN employment_status employment_status_enum');
        DB::statement('ALTER TABLE employees ADD COLUMN salary_currency salary_currency_enum DEFAULT \'MVR\'');
        DB::statement('ALTER TABLE employees ADD COLUMN level employee_level_enum DEFAULT \'junior\'');
        DB::statement('ALTER TABLE employees ADD COLUMN company company_enum DEFAULT \'RASHEED CARPENTRY AND CONSTRUCTION PVT LTD\'');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
}; 