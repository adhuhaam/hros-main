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
        Schema::create('employee_tickets_destination', function (Blueprint $table) {
            $table->id();
            $table->string('destination_name', 255)->unique();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('employee_tickets_price', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('destination_id');
            $table->decimal('online_price', 10, 2);
            $table->decimal('agency_price', 10, 2);
            
            $table->foreign('destination_id')->references('id')->on('employee_tickets_destination')->onDelete('cascade');
        });

        Schema::create('employee_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 50);
            $table->enum('ticket_type', ['Annual Leave', 'Business Travel', 'Emergency Leave', 'Other']);
            $table->string('destination', 255);
            $table->date('departure_date');
            $table->date('return_date')->nullable();
            $table->enum('ticket_status', ['Pending', 'Reservation Sent', 'Ticket Received', 'Departed', 'Pending Arrival', 'Arrived'])->default('Pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('price_id')->nullable();
            $table->string('ticket_file', 255)->nullable();
            $table->string('invoice_file', 255)->nullable();
            
            $table->foreign('price_id')->references('id')->on('employee_tickets_price')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_tickets');
        Schema::dropIfExists('employee_tickets_price');
        Schema::dropIfExists('employee_tickets_destination');
    }
}; 