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
        Schema::create('visa_sticker', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 10);
            $table->date('visa_expiry_date')->nullable();
            $table->enum('visa_status', ['Pending', 'Pending Approval', 'Ready for Submission', 'Ready for Collection', 'Completed'])->default('Pending');
            $table->text('remarks')->nullable();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->foreign('emp_no')->references('emp_no')->on('employees')->onDelete('cascade');
            $table->index('emp_no');
        });

        Schema::create('work_visa', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 10);
            $table->string('visa_number', 100)->nullable();
            $table->date('visa_issue_date')->nullable();
            $table->date('visa_expiry_date')->nullable();
            $table->enum('visa_status', ['Expiring Soon', 'Pending', 'Pending Approval', 'Ready for Submission', 'Ready for Collection', 'Completed', 'Expired'])->default('Pending');
            $table->string('visa_type', 100)->nullable();
            $table->string('place_of_issue', 100)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('last_updated')->useCurrent();
            
            $table->foreign('emp_no')->references('emp_no')->on('employees')->onDelete('cascade')->onUpdate('cascade');
            $table->index('emp_no');
        });

        Schema::create('work_permit_fees', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 10);
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['Pending', 'Collection Created', 'Paid', 'Completed'])->default('Pending');
            $table->text('remarks')->nullable();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->foreign('emp_no')->references('emp_no')->on('employees')->onDelete('cascade');
            $table->index('emp_no');
        });

        Schema::create('passport_inventory', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 50);
            $table->enum('direction', ['IN', 'OUT']);
            $table->string('taken_by', 100)->nullable();
            $table->text('purpose')->nullable();
            $table->string('handed_over_by', 100)->nullable();
            $table->string('received_by', 100)->nullable();
            $table->text('remark')->nullable();
            $table->datetime('taken_by_date')->nullable();
            $table->datetime('received_by_date')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('passport_renewals', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 20);
            $table->date('renewal_date');
            $table->text('remarks')->nullable();
            $table->enum('status', ['Pending', 'Scheduled', 'Went to embassy', 'Applied', 'Rejected', 'Incomplete', 'Approved', 'Received new passport'])->default('Pending');
            $table->string('phone', 15)->nullable();
            $table->string('email', 50)->nullable();
            $table->text('address')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passport_renewals');
        Schema::dropIfExists('passport_inventory');
        Schema::dropIfExists('work_permit_fees');
        Schema::dropIfExists('work_visa');
        Schema::dropIfExists('visa_sticker');
    }
}; 