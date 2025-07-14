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
            $table->string('emp_no', 20);
            $table->text('problem');
            $table->text('employee_statement')->nullable();
            $table->text('hrm_statement')->nullable();
            $table->text('hod_statement')->nullable();
            $table->text('management_comment')->nullable();
            $table->text('management_decision')->nullable();
            $table->enum('status', ['Pending HOD Review', 'Pending HRM Review', 'Pending Director Review', 'Resolved'])->default('Pending HOD Review');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
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