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
        Schema::create('bank_account_records', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 10);
            $table->string('email', 225)->nullable();
            $table->string('phone', 225)->nullable();
            $table->string('bank_name', 100);
            $table->string('bank_acc_no', 50)->nullable();
            $table->enum('currency', ['MVR', 'USD'])->default('MVR');
            $table->enum('status', ['Pending', 'Scheduled', 'Completed'])->default('Pending');
            $table->date('entry_date');
            $table->boolean('form_filled')->default(false);
            $table->date('scheduled_date')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->foreign('emp_no')->references('emp_no')->on('employees')->onDelete('cascade');
            $table->index(['emp_no', 'status']);
            $table->index('emp_no');
        });

        Schema::create('card_print', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 10);
            $table->enum('print_type', ['Work Permit Card', 'Access Card']);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->enum('status', ['Pending', 'Printed', 'Handed Over'])->default('Pending');
            $table->enum('payment_status', ['Not Received', 'Received'])->default('Not Received');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->text('remarks')->nullable();
            $table->date('requested_date')->nullable();
            $table->date('handover_date')->nullable();
            $table->boolean('handed_over_accounts')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_print');
        Schema::dropIfExists('bank_account_records');
    }
}; 