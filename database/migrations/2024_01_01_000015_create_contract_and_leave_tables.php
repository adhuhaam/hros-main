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
        Schema::create('e_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 10);
            $table->enum('contract_type', ['Offer Letter', 'Employment Contract', 'Renewal', 'Termination', 'Other'])->default('Renewal');
            $table->string('contract_title', 255);
            $table->string('contract_file', 255);
            $table->date('issued_date');
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['Active', 'Expired', 'Revoked'])->default('Active');
            $table->enum('variant', ['renewal', 'new'])->default('renewal');
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('emp_no')->references('emp_no')->on('employees')->onDelete('cascade');
        });

        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 10);
            $table->unsignedBigInteger('leave_type_id');
            $table->integer('balance')->default(0);
            $table->timestamp('last_updated')->useCurrent();
            
            $table->foreign('emp_no')->references('emp_no')->on('employees')->onDelete('cascade');
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade');
            
            $table->index('emp_no');
            $table->index('leave_type_id');
        });

        Schema::create('leave_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leave_id');
            $table->string('leave_form', 255)->nullable();
            $table->string('departure_sheet', 255)->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
            
            $table->foreign('leave_id')->references('id')->on('leave_records')->onDelete('cascade');
        });

        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('holiday_name', 255);
            $table->date('holiday_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('leave_files');
        Schema::dropIfExists('leave_balances');
        Schema::dropIfExists('e_contracts');
    }
}; 