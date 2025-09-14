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
        Schema::create('h5_p_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('h5p_content_id');
            $table->unsignedBigInteger('lesson_content_id');
            $table->unsignedBigInteger('course_id');
            $table->timestamps();
            
            // Indexes
            $table->index('h5p_content_id');
            $table->index('lesson_content_id');
            $table->index('course_id');
            
            // Unique constraint to prevent duplicate usage records
            $table->unique(['h5p_content_id', 'lesson_content_id']);
            
            // Foreign key constraints (added separately to avoid issues)
            $table->foreign('h5p_content_id')->references('id')->on('h5_p_contents')->onDelete('cascade');
            $table->foreign('lesson_content_id')->references('id')->on('lesson_contents')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('h5_p_usages');
    }
};
