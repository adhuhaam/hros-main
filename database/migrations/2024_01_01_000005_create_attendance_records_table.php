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
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id('record_id');
            $table->string('emp_no', 10);
            $table->integer('month');
            $table->integer('year');
            $table->string('day_type', 255)->nullable();
            $table->string('shift', 255)->nullable();
            $table->string('present_absent', 10)->nullable();
            $table->time('work_in')->nullable();
            $table->time('work_out')->nullable();
            $table->text('remarks')->nullable();
            $table->string('island_name', 255)->nullable();
            $table->string('site_name', 255)->nullable();
            $table->string('status', 50)->nullable();
            $table->date('upload_date')->useCurrent();
            $table->integer('day');
            
            // Foreign key
            $table->foreign('emp_no')->references('emp_no')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
}; 