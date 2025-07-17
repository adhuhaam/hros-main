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
        Schema::table('holidays', function (Blueprint $table) {
            // First rename existing columns
            $table->renameColumn('holiday_name', 'name');
            $table->renameColumn('holiday_date', 'date');
        });
        
        Schema::table('holidays', function (Blueprint $table) {
            // Then add new columns
            $table->string('type')->after('date');
            $table->text('description')->nullable()->after('type');
            $table->boolean('is_recurring')->default(false)->after('description');
            $table->integer('recurring_month')->nullable()->after('is_recurring');
            $table->integer('recurring_day')->nullable()->after('recurring_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('holidays', function (Blueprint $table) {
            // Remove new columns
            $table->dropColumn(['type', 'description', 'is_recurring', 'recurring_month', 'recurring_day']);
        });
        
        Schema::table('holidays', function (Blueprint $table) {
            // Rename columns back
            $table->renameColumn('name', 'holiday_name');
            $table->renameColumn('date', 'holiday_date');
        });
    }
};
