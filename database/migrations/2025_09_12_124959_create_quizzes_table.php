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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id')->nullable(); // Can be standalone or attached to lesson
            $table->unsignedBigInteger('course_id')->nullable(); // Course-level quizzes
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('quiz_type', ['multiple_choice', 'true_false', 'short_answer', 'essay', 'matching', 'fill_blank', 'drag_drop'])->default('multiple_choice');
            $table->json('settings'); // Time limits, retries, grading settings
            $table->integer('max_attempts')->default(3);
            $table->integer('time_limit_minutes')->nullable();
            $table->decimal('passing_score', 5, 2)->default(70.00); // Percentage
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('show_correct_answers')->default(true);
            $table->boolean('is_required')->default(false); // Required to progress
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->index(['lesson_id', 'is_active']);
            $table->index(['course_id', 'quiz_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quizzes');
    }
};
