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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id', 10);
            $table->foreign('employee_id')->references('emp_no')->on('employees')->onDelete('cascade');
            $table->enum('medical_type', ['Fitness', 'Blood Test', 'Chest X-Ray', 'Eye Test', 'Dental', 'Other']);
            $table->date('test_date');
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['Active', 'Expired', 'Pending'])->default('Active');
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable();
            $table->string('test_center')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->date('next_test_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};