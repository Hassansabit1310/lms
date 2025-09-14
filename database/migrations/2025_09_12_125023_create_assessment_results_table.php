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
        Schema::create('assessment_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->morphs('assessable'); // Quizzes, lessons, courses, etc.
            $table->enum('assessment_type', ['quiz', 'assignment', 'project', 'participation', 'h5p_interaction', 'code_exercise']);
            $table->decimal('score', 5, 2)->nullable(); // Percentage or raw score
            $table->decimal('max_score', 8, 2)->default(100);
            $table->enum('grade', ['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'F'])->nullable();
            $table->boolean('is_passed')->default(false);
            $table->text('feedback')->nullable(); // Instructor feedback
            $table->json('detailed_breakdown')->nullable(); // Skill/topic breakdown
            $table->json('learning_analytics')->nullable(); // Time spent, attempts, etc.
            $table->timestamp('assessed_at');
            $table->unsignedBigInteger('graded_by')->nullable(); // Who graded it
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('graded_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['user_id', 'assessment_type']);
            $table->index(['is_passed', 'assessed_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessment_results');
    }
};
