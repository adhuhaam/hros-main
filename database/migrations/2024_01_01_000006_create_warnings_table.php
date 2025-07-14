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
        Schema::create('warnings', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id', 10);
            $table->foreign('employee_id')->references('emp_no')->on('employees')->onDelete('cascade');
            $table->addColumn('warning_type_enum', 'warning_type');
            $table->string('subject');
            $table->text('description');
            $table->date('warning_date');
            $table->foreignId('issued_by')->constrained('users')->onDelete('cascade');
            $table->addColumn('warning_status_enum', 'status')->default('Active');
            $table->addColumn('severity_level_enum', 'severity_level')->default('Medium');
            $table->text('action_taken')->nullable();
            $table->text('improvement_plan')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warnings');
    }
};