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
            // Add missing columns only if they don't exist
            if (!Schema::hasColumn('lessons', 'slug')) {
                $table->string('slug')->nullable()->after('title');
            }
            
            if (!Schema::hasColumn('lessons', 'video_url')) {
                $table->string('video_url')->nullable()->after('content');
            }
            
            if (!Schema::hasColumn('lessons', 'video_duration')) {
                $table->integer('video_duration')->nullable()->after('video_url');
            }
            
            // Add status column if it doesn't exist
            if (!Schema::hasColumn('lessons', 'status')) {
                $table->enum('status', ['draft', 'published', 'archived'])->default('published')->after('is_free');
            }
        });
        
        // If status column already exists, safely update it to include 'archived' option
        if (Schema::hasColumn('lessons', 'status')) {
            // Get current enum values
            $result = DB::select("SHOW COLUMNS FROM lessons WHERE Field = 'status'");
            if (!empty($result)) {
                $enumValues = $result[0]->Type;
                
                // Only modify if 'archived' is not already in the enum
                if (!str_contains($enumValues, 'archived')) {
                    DB::statement("ALTER TABLE lessons MODIFY COLUMN status ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'published'");
                }
            }
        }
        
        // Remove unnecessary sort_orders column if it exists (we use sort_order instead)
        if (Schema::hasColumn('lessons', 'sort_orders')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->dropColumn('sort_orders');
            });
        }
        
        // Add performance indexes (only if they don't exist)
        $indexes = collect(DB::select("SHOW INDEX FROM lessons"))->pluck('Key_name')->toArray();
        
        Schema::table('lessons', function (Blueprint $table) use ($indexes) {
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
        // Get current indexes
        $indexes = collect(DB::select("SHOW INDEX FROM lessons"))->pluck('Key_name')->toArray();
        
        Schema::table('lessons', function (Blueprint $table) use ($indexes) {
            // Drop indexes that were added by this migration
            if (in_array('lessons_course_id_slug_index', $indexes)) {
                $table->dropIndex(['course_id', 'slug']);
            }
            if (in_array('lessons_course_id_status_index', $indexes)) {
                $table->dropIndex(['course_id', 'status']);
            }
            // Note: Don't drop course_id_order index as it might be needed for foreign keys
        });
        
        // Only drop columns that were definitely added by this migration
        // In production, these columns might not exist, so we check first
        if (Schema::hasColumn('lessons', 'video_duration')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->dropColumn('video_duration');
            });
        }
        
        if (Schema::hasColumn('lessons', 'video_url')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->dropColumn('video_url');
            });
        }
        
        if (Schema::hasColumn('lessons', 'slug')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
        
        // For status column, only revert if we know it was modified by this migration
        // In production, this column might not exist at all
        if (Schema::hasColumn('lessons', 'status')) {
            try {
                DB::statement("ALTER TABLE lessons MODIFY COLUMN status ENUM('draft', 'published') NOT NULL DEFAULT 'draft'");
            } catch (Exception $e) {
                // If modification fails, the column might have been added by this migration
                // In that case, drop it entirely
                Schema::table('lessons', function (Blueprint $table) {
                    $table->dropColumn('status');
                });
            }
        }
    }
};