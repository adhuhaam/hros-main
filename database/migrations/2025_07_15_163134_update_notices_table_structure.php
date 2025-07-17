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
        Schema::table('notices', function (Blueprint $table) {
            // Add new columns
            $table->string('type')->after('content');
            $table->enum('status', ['Draft', 'Published', 'Archived'])->default('Draft')->after('type');
            $table->date('publish_date')->nullable()->after('status');
            $table->date('expiry_date')->nullable()->after('publish_date');
            $table->boolean('is_featured')->default(false)->after('expiry_date');
            $table->string('target_audience')->nullable()->after('is_featured');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->after('target_audience');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null')->after('created_by');
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            // Remove foreign key constraints first
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            
            // Remove columns
            $table->dropColumn([
                'type', 'status', 'publish_date', 'expiry_date', 
                'is_featured', 'target_audience', 'created_by', 'updated_by', 'updated_at'
            ]);
        });
    }
};
