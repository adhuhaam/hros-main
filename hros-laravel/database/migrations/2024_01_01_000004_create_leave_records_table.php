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
        Schema::create('leave_records', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no');
            $table->enum('leave_type', [
                'Annual Leave', 'Medical Leave', 'Emergency Leave', 
                'Maternity Leave', 'Paternity Leave', 'No Pay Leave', 
                'Special Leave', 'Umrah Leave'
            ]);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_requested');
            $table->text('reason')->nullable();
            $table->enum('status', [
                'Pending', 'Approved', 'Rejected', 'Departed', 
                'Arrived', 'Pending Leave Arrival'
            ])->default('Pending');
            $table->timestamp('applied_date')->useCurrent();
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_date')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->timestamp('actual_departure_date')->nullable();
            $table->timestamp('actual_arrival_date')->nullable();
            $table->string('ticket_number')->nullable();
            $table->string('destination')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('emp_no')->references('emp_no')->on('employees')->onDelete('cascade');
            $table->foreign('approved_by')->references('emp_no')->on('employees')->onDelete('set null');

            // Indexes
            $table->index(['emp_no', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index(['status', 'start_date']);
            $table->index('applied_date');
            $table->index('approved_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_records');
    }
};