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
        Schema::create('lesson_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->enum('content_type', ['h5p', 'matter_js', 'interactive', 'quiz', 'assessment', 'video', 'text', 'code']);
            $table->json('content_data'); // Stores flexible content configuration
            $table->json('settings')->nullable(); // H5P settings, Matter.js configurations, etc.
            $table->string('h5p_content_id')->nullable(); // For H5P integration
            $table->text('matter_js_code')->nullable(); // For custom Matter.js physics code
            $table->json('interactive_config')->nullable(); // For other interactive content
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->index(['lesson_id', 'content_type']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_contents');
    }
};
