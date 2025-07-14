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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id', 10);
            $table->foreign('employee_id')->references('emp_no')->on('employees')->onDelete('cascade');
            $table->enum('loan_type', [
                'Personal', 'Housing', 'Vehicle', 'Education', 'Emergency', 'Other'
            ]);
            $table->decimal('amount', 12, 2);
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('installment_amount', 10, 2);
            $table->integer('total_installments');
            $table->integer('paid_installments')->default(0);
            $table->decimal('remaining_amount', 12, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['Pending', 'Active', 'Completed', 'Rejected'])->default('Pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('purpose');
            $table->string('guarantor_name')->nullable();
            $table->string('guarantor_phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};