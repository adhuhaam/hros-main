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
            $table->id();
            $table->string('emp_no')->unique();
            $table->string('name');
            $table->enum('gender', ['Male', 'Female']);
            $table->string('designation');
            $table->string('xpat_designation')->nullable();
            $table->date('xpat_join_date')->nullable();
            $table->string('department');
            $table->string('nationality')->nullable();
            $table->string('passport_nic_no')->nullable();
            $table->date('passport_nic_no_expires')->nullable();
            $table->date('dob')->nullable();
            $table->string('wp_no')->nullable();
            $table->date('date_of_join')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('emergency_contact_number')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->enum('employment_status', [
                'Active', 'Terminated', 'Resigned', 'Rejoined', 
                'Dead', 'Retired', 'Missing'
            ])->default('Active');
            $table->string('work_site')->nullable();
            $table->string('insurance_provider')->nullable();
            $table->string('recruiting_agency')->nullable();
            $table->string('emp_email')->nullable();
            $table->text('permanent_address')->nullable();
            $table->decimal('basic_salary', 10, 2)->default(0.00);
            $table->string('salary_currency', 3)->default('MVR');
            $table->date('termination_date')->nullable();
            $table->string('player_id')->nullable(); // For push notifications
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['emp_no', 'employment_status']);
            $table->index(['department', 'employment_status']);
            $table->index(['nationality', 'employment_status']);
            $table->index('passport_nic_no_expires');
            $table->index('date_of_join');
            $table->index('termination_date');
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