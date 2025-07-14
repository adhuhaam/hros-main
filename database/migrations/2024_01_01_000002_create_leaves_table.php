<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id', 10);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_requested');
            $table->text('reason');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('destination')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('emp_no')->on('employees')->onDelete('cascade');
        });

        // Add enum columns using raw SQL for PostgreSQL compatibility
        DB::statement('ALTER TABLE leaves ADD COLUMN leave_type leave_type_enum');
        DB::statement('ALTER TABLE leaves ADD COLUMN status leave_status_enum DEFAULT \'Pending\'');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};