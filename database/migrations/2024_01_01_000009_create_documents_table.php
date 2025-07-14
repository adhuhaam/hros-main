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
            $table->string('emp_no', 50);
            $table->string('doc_type', 50);
            $table->timestamp('uploaded_at')->useCurrent();
            $table->string('front_file_name', 255)->nullable();
            $table->string('back_file_name', 255)->nullable();
            $table->string('photo_file_name', 255)->nullable();
            
            // Unique constraint
            $table->unique(['emp_no', 'doc_type']);
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