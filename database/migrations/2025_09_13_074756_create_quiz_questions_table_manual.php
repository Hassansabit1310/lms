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
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id');
            $table->text('question');
            $table->enum('question_type', ['multiple_choice', 'true_false', 'short_answer', 'essay', 'matching', 'fill_blank', 'drag_drop'])->default('multiple_choice');
            $table->json('options')->nullable(); // For multiple choice, matching, etc.
            $table->json('correct_answers'); // Flexible answer storage
            $table->text('explanation')->nullable(); // Explanation for correct answer
            $table->decimal('points', 5, 2)->default(1.00);
            $table->integer('order')->default(0);
            $table->json('metadata')->nullable(); // Additional question data
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            $table->index(['quiz_id', 'order']);
            $table->index(['question_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_questions');
    }
};
