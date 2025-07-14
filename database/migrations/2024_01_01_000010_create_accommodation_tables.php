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
        Schema::create('accommodation_buildings', function (Blueprint $table) {
            $table->id();
            $table->string('building_name', 255)->nullable();
            $table->string('location', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('accommodation_floors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('building_id')->nullable();
            $table->integer('floor_number')->nullable();
            
            $table->foreign('building_id')->references('id')->on('accommodation_buildings')->onDelete('cascade');
        });

        Schema::create('accommodation_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('floor_id')->nullable();
            $table->string('room_number', 50)->nullable();
            
            $table->foreign('floor_id')->references('id')->on('accommodation_floors')->onDelete('cascade');
        });

        Schema::create('accommodation_beds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id');
            $table->string('bed_number', 50)->nullable();
            $table->string('occupied_by', 10)->nullable();
            $table->timestamp('assigned_at')->nullable();
            
            $table->foreign('room_id')->references('id')->on('accommodation_rooms')->onDelete('cascade');
            $table->foreign('occupied_by')->references('emp_no')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accommodation_beds');
        Schema::dropIfExists('accommodation_rooms');
        Schema::dropIfExists('accommodation_floors');
        Schema::dropIfExists('accommodation_buildings');
    }
}; 