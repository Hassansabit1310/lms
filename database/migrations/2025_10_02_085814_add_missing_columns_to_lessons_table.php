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
        Schema::table('lessons', function (Blueprint $table) {
            // Check if columns exist before adding them
            if (!Schema::hasColumn('lessons', 'slug')) {
                $table->string('slug')->nullable()->after('title');
            }
            
            if (!Schema::hasColumn('lessons', 'video_url')) {
                $table->string('video_url')->nullable()->after('content');
            }
            
            if (!Schema::hasColumn('lessons', 'video_duration')) {
                $table->integer('video_duration')->nullable()->after('video_url');
            }
            
            // Add sort_orders column if it doesn't exist (some systems might have sort_order instead)
            if (!Schema::hasColumn('lessons', 'sort_orders')) {
                $table->integer('sort_orders')->nullable()->after('order');
            }
        });
        
        // Update status enum to include 'archived' option
        DB::statement("ALTER TABLE lessons MODIFY COLUMN status ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'published'");
        
        // Add indexes for performance (only if they don't exist)
        Schema::table('lessons', function (Blueprint $table) {
            // Check if indexes exist before adding them
            $indexes = collect(DB::select("SHOW INDEX FROM lessons"))->pluck('Key_name')->toArray();
            
            if (!in_array('lessons_course_id_slug_index', $indexes)) {
                $table->index(['course_id', 'slug']);
            }
            if (!in_array('lessons_course_id_status_index', $indexes)) {
                $table->index(['course_id', 'status']);
            }
            if (!in_array('lessons_course_id_order_index', $indexes)) {
                $table->index(['course_id', 'order']);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lessons', function (Blueprint $table) {
            // Drop indexes first (if they exist)
            $indexes = collect(DB::select("SHOW INDEX FROM lessons"))->pluck('Key_name')->toArray();
            
            if (in_array('lessons_course_id_slug_index', $indexes)) {
                $table->dropIndex(['course_id', 'slug']);
            }
            if (in_array('lessons_course_id_status_index', $indexes)) {
                $table->dropIndex(['course_id', 'status']);
            }
            if (in_array('lessons_course_id_order_index', $indexes)) {
                $table->dropIndex(['course_id', 'order']);
            }
            
            // Drop sort_orders column if it exists
            if (Schema::hasColumn('lessons', 'sort_orders')) {
                $table->dropColumn('sort_orders');
            }
        });
        
        // Revert status enum to original values
        DB::statement("ALTER TABLE lessons MODIFY COLUMN status ENUM('draft', 'published') NOT NULL DEFAULT 'draft'");
    }
};
