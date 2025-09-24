<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add 'runnable_code' to the content_type enum in lesson_contents table
        DB::statement("ALTER TABLE lesson_contents MODIFY COLUMN content_type ENUM('h5p', 'matter_js', 'interactive', 'quiz', 'assessment', 'video', 'text', 'code', 'runnable_code')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove 'runnable_code' from the content_type enum
        DB::statement("ALTER TABLE lesson_contents MODIFY COLUMN content_type ENUM('h5p', 'matter_js', 'interactive', 'quiz', 'assessment', 'video', 'text', 'code')");
    }
};
