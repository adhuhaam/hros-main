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
        Schema::create('medical_examinations', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id', 10);
            $table->string('medical_center_name', 255)->default('Default Medical Center');
            $table->date('date_of_medical')->nullable();
            $table->enum('status', ['Pending', 'Medical Center Visited', 'Uploaded', 'Incomplete', 'Completed'])->default('Pending');
            $table->string('medical_document', 2555)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->boolean('uploaded_xpat')->default(false);
            
            $table->foreign('employee_id')->references('emp_no')->on('employees')->onDelete('cascade');
            $table->unique('employee_id');
        });

        Schema::create('opd_records', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 50);
            $table->string('project_name', 255);
            $table->string('invoice_no', 100);
            $table->text('medication_detail');
            $table->decimal('medication_amount', 10, 2);
            $table->date('consultation_date');
            $table->string('entered_by', 50);
            $table->datetime('created_at')->useCurrent();
        });

        Schema::create('ot_records', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 20)->nullable();
            $table->date('ot_date')->nullable();
            $table->string('ot_type', 100)->nullable();
            $table->string('requested_by', 100)->nullable();
            $table->decimal('requested_hrs', 5, 2)->nullable();
            $table->decimal('approved_hrs', 5, 2)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->text('reason')->nullable();
            $table->string('status', 20)->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
        });

        Schema::create('sick_leaves', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 50);
            $table->date('from_date');
            $table->integer('no_of_days');
            $table->string('reason', 255);
            $table->date('leave_date');
            $table->string('reference_doc', 255)->nullable();
            $table->datetime('created_at')->useCurrent();
        });

        Schema::create('missing', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 20);
            $table->date('missing_date');
            $table->string('reported_by', 100)->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['Pending', 'Resolved', 'Approved'])->default('Pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::create('resignations', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 50);
            $table->date('resignation_date');
            $table->date('resign_requested_date');
            $table->text('remarks')->nullable();
            $table->text('statement');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('retirement_records', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 20);
            $table->date('retirement_date');
            $table->string('reason', 255)->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->text('remarks')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::create('termination', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 20);
            $table->date('termination_date');
            $table->text('reason')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->text('remarks')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::create('island_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 50);
            $table->unsignedBigInteger('destination_from');
            $table->unsignedBigInteger('destination_to');
            $table->timestamp('transfer_date')->useCurrent();
            
            $table->foreign('destination_from')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('destination_to')->references('id')->on('projects')->onDelete('cascade');
        });

        Schema::create('mailing_group', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 50)->nullable();
            $table->text('tags')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('system_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100);
            $table->string('label', 255);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_permissions');
        Schema::dropIfExists('mailing_group');
        Schema::dropIfExists('island_transfers');
        Schema::dropIfExists('termination');
        Schema::dropIfExists('retirement_records');
        Schema::dropIfExists('resignations');
        Schema::dropIfExists('missing');
        Schema::dropIfExists('sick_leaves');
        Schema::dropIfExists('ot_records');
        Schema::dropIfExists('opd_records');
        Schema::dropIfExists('medical_examinations');
    }
}; 