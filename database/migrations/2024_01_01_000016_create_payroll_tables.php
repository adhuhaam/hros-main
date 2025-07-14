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
        Schema::create('salary_income', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 10);
            $table->date('date');
            $table->decimal('basic_salary', 10, 2)->default(0.00);
            $table->decimal('service_allowance', 10, 2)->default(0.00);
            $table->decimal('island_allowance', 10, 2)->default(0.00);
            $table->decimal('attendance_allowance', 10, 2)->default(0.00);
            $table->decimal('salary_arrear_other', 10, 2)->default(0.00);
            $table->decimal('safety_allowance', 10, 2)->default(0.00);
            $table->decimal('pump_brick_batching', 10, 2)->default(0.00);
            $table->decimal('food_and_tea', 10, 2)->default(0.00);
            $table->decimal('long_term_service_allowance', 10, 2)->default(0.00);
            $table->decimal('living_allowance', 10, 2)->default(0.00);
            $table->decimal('ot', 10, 2)->default(0.00);
            $table->decimal('ot_arrears', 10, 2)->default(0.00);
            $table->decimal('phone_allowance', 10, 2)->default(0.00);
            $table->decimal('petrol_allowance', 10, 2)->default(0.00);
            $table->decimal('pension', 10, 2)->default(0.00);
            
            $table->foreign('emp_no')->references('emp_no')->on('employees')->onDelete('cascade')->onUpdate('cascade');
            $table->unique(['emp_no', 'date']);
            $table->index('emp_no');
        });

        Schema::create('salary_deductions', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 10);
            $table->date('date');
            $table->decimal('other_deduction', 10, 2)->default(0.00);
            $table->decimal('salary_advance', 10, 2)->default(0.00);
            $table->decimal('loan', 10, 2)->default(0.00);
            $table->decimal('pension', 10, 2)->default(0.00);
            $table->decimal('medical_deduction', 10, 2)->default(0.00);
            $table->decimal('no_pay', 10, 2)->default(0.00);
            $table->decimal('late', 10, 2)->default(0.00);
            
            $table->foreign('emp_no')->references('emp_no')->on('employees')->onDelete('cascade')->onUpdate('cascade');
            $table->unique(['emp_no', 'date']);
            $table->index('emp_no');
        });

        Schema::create('payroll_summary', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 10);
            $table->date('payroll_month');
            $table->decimal('total_earnings', 10, 2)->default(0.00);
            $table->decimal('total_deductions', 10, 2)->default(0.00);
            $table->decimal('net_pay', 10, 2)->default(0.00);
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('emp_no')->references('emp_no')->on('employees')->onDelete('cascade')->onUpdate('cascade');
            $table->index('emp_no');
        });

        Schema::create('salary_slips', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 10);
            $table->date('date');
            $table->decimal('net_salary', 10, 2);
            $table->string('file_path', 255)->nullable();
            $table->timestamp('generated_date')->useCurrent();
            $table->enum('status', ['pending', 'generated', 'issued'])->default('pending');
            
            $table->foreign('emp_no')->references('emp_no')->on('employees')->onDelete('cascade');
            $table->index('emp_no');
        });

        Schema::create('salary_loans', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 50);
            $table->decimal('amount', 10, 2);
            $table->text('purpose');
            $table->string('currency', 10);
            $table->enum('status', ['Pending', 'HRM Approved', 'Management Approved', 'Rejected'])->default('Pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->date('applied_date')->useCurrent();
            $table->date('approved_date')->nullable();
            $table->boolean('received')->default(false);
            $table->date('received_date')->nullable();
            $table->boolean('deduct')->default(false);
            
            $table->index('emp_no');
        });

        Schema::create('loan_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->integer('payment_month');
            $table->integer('payment_year');
            $table->boolean('payment_status')->default(false);
            $table->decimal('payment_amount', 10, 2);
            
            $table->foreign('loan_id')->references('id')->on('employee_loan')->onDelete('cascade');
            $table->unique(['loan_id', 'payment_month', 'payment_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_payments');
        Schema::dropIfExists('salary_loans');
        Schema::dropIfExists('salary_slips');
        Schema::dropIfExists('payroll_summary');
        Schema::dropIfExists('salary_deductions');
        Schema::dropIfExists('salary_income');
    }
}; 