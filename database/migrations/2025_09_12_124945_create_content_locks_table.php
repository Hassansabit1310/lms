<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_locks', function (Blueprint $table) {
            $table->id();
            $table->morphs('lockable'); // Can lock lessons, quizzes, etc.
            $table->unsignedBigInteger('user_id')->nullable(); // Specific user (null = all users)
            $table->enum('lock_type', ['hidden', 'locked', 'preview_only'])->default('locked');
            $table->enum('unlock_condition', ['manual', 'task_completion', 'time_based', 'payment', 'subscription'])->default('manual');
            $table->json('unlock_criteria')->nullable(); // Flexible criteria storage
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('unlocks_at')->nullable(); // For time-based unlocks
            $table->boolean('is_active')->default(true);
            $table->text('reason')->nullable(); // Admin notes
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'is_active']);
            $table->index(['unlock_condition']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_locks');
    }
};
