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
        // Update enum to include matter_js (it should already be there from the original migration)
        // This is just to ensure consistency
        DB::statement("ALTER TABLE lesson_contents MODIFY COLUMN content_type ENUM('h5p', 'matter_js', 'interactive', 'quiz', 'assessment', 'video', 'text', 'code', 'runnable_code')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Rollback if needed
        DB::statement("ALTER TABLE lesson_contents MODIFY COLUMN content_type ENUM('h5p', 'matter_js', 'interactive', 'quiz', 'assessment', 'video', 'text', 'code', 'runnable_code')");
    }
};
