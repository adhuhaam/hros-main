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
        Schema::create('employee_loan', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id', 50);
            $table->decimal('loan_amount', 10, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->decimal('emi', 10, 2);
            $table->integer('installment_period');
            $table->decimal('total_outstanding', 10, 2);
            $table->integer('installments_paid')->default(0);
            $table->boolean('active_status')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->decimal('remaining_balance', 10, 2)->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_loan');
    }
}; 