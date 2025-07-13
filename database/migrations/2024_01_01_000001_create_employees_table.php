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
            $table->string('employee_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->date('date_of_birth');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('nationality');
            $table->string('passport_number')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('work_permit_number')->nullable();
            $table->date('work_permit_expiry')->nullable();
            $table->string('visa_number')->nullable();
            $table->date('visa_expiry')->nullable();
            $table->string('department');
            $table->string('position');
            $table->enum('employment_status', ['Active', 'Inactive', 'Resigned', 'Terminated', 'Retired', 'Dead', 'Missing'])->default('Active');
            $table->date('hire_date');
            $table->decimal('salary', 10, 2);
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->text('address')->nullable();
            $table->enum('accommodation_status', ['Provided', 'Not Provided', 'Self-Arranged'])->default('Not Provided');
            $table->enum('medical_status', ['Fit', 'Unfit', 'Pending'])->default('Pending');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('profile_photo')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
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