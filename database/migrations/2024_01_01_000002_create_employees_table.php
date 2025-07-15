<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->enum('gender', ['Male', 'Female']);
            $table->string('designation', 255)->nullable();
            $table->string('xpat_designation', 225)->nullable();
            $table->date('xpat_join_date')->nullable();
            $table->string('department', 255)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('passport_nic_no', 100)->nullable();
            $table->date('passport_expire_date')->nullable();
            $table->date('dob')->nullable();
            $table->string('wp_no', 100)->nullable();
            $table->date('date_of_join')->nullable();
            $table->string('contact_number', 50)->nullable();
            $table->string('contact_number_foregn', 225)->nullable();
            $table->string('emergency_contact_number', 50)->nullable();
            $table->string('emergency_contact_name', 255)->nullable();
            $table->enum('employment_status', [
                'Active', 'Terminated', 'Resigned', 'Rejoined', 'Dead', 'Retired', 'Missing'
            ])->default('Active');
            $table->string('work_site', 255)->nullable();
            $table->string('insurance_provider', 255)->nullable();
            $table->string('recruiting_agency', 255)->nullable();
            $table->string('emp_email', 225)->nullable();
            $table->string('company_email', 255)->nullable();
            $table->string('permanent_address', 1500)->nullable();
            $table->string('persent_address', 2000);
            $table->decimal('basic_salary', 10, 2)->nullable();
            $table->enum('salary_currency', ['MVR', 'USD'])->default('MVR');
            $table->date('termination_date')->nullable();
            $table->enum('level', ['senior', 'junior'])->default('junior');
            $table->enum('company', [
                'RASHEED CARPENTRY AND CONSTRUCTION PVT LTD',
                'NAZRASH COMPANY PVT LTD'
            ])->default('RASHEED CARPENTRY AND CONSTRUCTION PVT LTD');
            
            // Indexes
            $table->index('name');
            $table->index('emp_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
}; 