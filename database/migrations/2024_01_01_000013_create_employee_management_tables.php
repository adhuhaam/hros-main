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
        Schema::create('employee_allowances', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 10);
            $table->enum('allowance_type', ['Fixed', 'Variable']);
            $table->string('allowance_name', 255);
            $table->decimal('amount', 10, 2);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->foreign('emp_no')->references('emp_no')->on('employees')->onDelete('cascade');
        });

        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 50);
            $table->string('doc_type', 50);
            $table->timestamp('uploaded_at')->useCurrent();
            $table->string('front_file_name', 255)->nullable();
            $table->string('back_file_name', 255)->nullable();
            $table->string('photo_file_name', 255)->nullable();
            
            $table->unique(['emp_no', 'doc_type']);
        });

        Schema::create('employee_login', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 10);
            $table->string('username', 100);
            $table->string('password', 255);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->unique('username');
        });

        Schema::create('employee_project_allocations', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id', 10);
            $table->unsignedBigInteger('project_id');
            $table->timestamp('assigned_date')->useCurrent();
            
            $table->foreign('employee_id')->references('emp_no')->on('employees')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_project_allocations');
        Schema::dropIfExists('employee_login');
        Schema::dropIfExists('employee_documents');
        Schema::dropIfExists('employee_allowances');
    }
}; 