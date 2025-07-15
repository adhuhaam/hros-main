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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('sender_id', 10);
            $table->enum('sender_type', ['employee', 'hr']);
            $table->string('receiver_id', 10);
            $table->enum('receiver_type', ['employee', 'hr']);
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->datetime('created_at')->useCurrent();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warning_id');
            $table->text('comment_content');
            $table->enum('role', ['HRM', 'HOD', 'Management']);
            $table->integer('commenter_id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->foreign('warning_id')->references('id')->on('warnings')->onDelete('cascade');
        });

        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('content');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('mail_logs', function (Blueprint $table) {
            $table->id();
            $table->string('subject', 255)->nullable();
            $table->text('body')->nullable();
            $table->text('recipients')->nullable();
            $table->enum('send_type', ['automatic', 'manual'])->nullable();
            $table->string('sent_by', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('push_logs', function (Blueprint $table) {
            $table->id();
            $table->string('emp_no', 20)->nullable();
            $table->string('title', 255)->nullable();
            $table->text('message')->nullable();
            $table->timestamp('sent_at')->useCurrent();
            $table->enum('status', ['sent', 'failed'])->default('sent');
            $table->text('response')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_logs');
        Schema::dropIfExists('mail_logs');
        Schema::dropIfExists('notices');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('chat_messages');
    }
}; 