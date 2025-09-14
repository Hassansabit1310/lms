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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('attempt_number');
            $table->json('answers'); // User's answers
            $table->decimal('score', 5, 2)->nullable(); // Percentage score
            $table->decimal('points_earned', 8, 2)->default(0);
            $table->decimal('points_possible', 8, 2)->default(0);
            $table->boolean('is_passed')->default(false);
            $table->enum('status', ['in_progress', 'completed', 'abandoned', 'time_expired'])->default('in_progress');
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            $table->integer('time_spent_seconds')->default(0);
            $table->json('detailed_results')->nullable(); // Question-by-question breakdown
            $table->timestamps();
            
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['quiz_id', 'user_id', 'attempt_number']);
            $table->index(['user_id', 'status']);
            $table->index(['quiz_id', 'is_passed']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
