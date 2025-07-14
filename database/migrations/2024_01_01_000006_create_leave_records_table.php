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
            $table->string('emp_no', 10);
            $table->unsignedBigInteger('leave_type_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('actual_arrival_date')->nullable();
            $table->integer('num_days');
            $table->text('remarks')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Pending Leave Arrival', 'Arrived', 'Departed'])->default('Pending');
            $table->timestamp('applied_date')->useCurrent();
            $table->integer('approved_by')->nullable();
            $table->timestamp('approval_date')->nullable();
            $table->integer('ticket_id')->nullable();
            $table->integer('departure_ticket_id')->nullable();
            $table->integer('arrival_ticket_id')->nullable();
            $table->string('medical_doc', 2000);
            
            // Foreign keys
            $table->foreign('emp_no')->references('emp_no')->on('employees')->onDelete('cascade');
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade');
            
            // Indexes
            $table->index('emp_no');
            $table->index('leave_type_id');
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