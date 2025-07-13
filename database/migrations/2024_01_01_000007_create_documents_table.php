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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->enum('document_type', ['Passport', 'Visa', 'Work Permit', 'Contract', 'ID Card', 'Certificate', 'Other']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->bigInteger('file_size');
            $table->string('file_type');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['Active', 'Expired', 'Archived'])->default('Active');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};