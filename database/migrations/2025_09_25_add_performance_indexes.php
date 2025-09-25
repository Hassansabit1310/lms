<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Optimize Course queries
        Schema::table('courses', function (Blueprint $table) {
            // Check if indexes don't already exist
            if (!$this->indexExists('courses', 'courses_status_created_at_index')) {
                $table->index(['status', 'created_at'], 'courses_status_created_at_index');
            }
            if (!$this->indexExists('courses', 'courses_category_id_status_index')) {
                $table->index(['category_id', 'status'], 'courses_category_id_status_index');
            }
        });

        // Optimize User queries
        Schema::table('users', function (Blueprint $table) {
            if (!$this->indexExists('users', 'users_role_index')) {
                $table->index('role', 'users_role_index');
            }
        });

        // Optimize Category queries  
        Schema::table('categories', function (Blueprint $table) {
            if (!$this->indexExists('categories', 'categories_parent_id_index')) {
                $table->index('parent_id', 'categories_parent_id_index');
            }
        });

        // Optimize Enrollment queries
        if (Schema::hasTable('enrollments')) {
            Schema::table('enrollments', function (Blueprint $table) {
                if (!$this->indexExists('enrollments', 'enrollments_user_id_index')) {
                    $table->index('user_id', 'enrollments_user_id_index');
                }
                if (!$this->indexExists('enrollments', 'enrollments_course_id_index')) {
                    $table->index('course_id', 'enrollments_course_id_index');
                }
            });
        }

        // Optimize Bundle queries (if table exists)
        if (Schema::hasTable('bundles')) {
            Schema::table('bundles', function (Blueprint $table) {
                if (!$this->indexExists('bundles', 'bundles_is_active_created_at_index')) {
                    $table->index(['is_active', 'created_at'], 'bundles_is_active_created_at_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('courses_status_created_at_index');
            $table->dropIndex('courses_category_id_status_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_parent_id_index');
        });

        if (Schema::hasTable('enrollments')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropIndex('enrollments_user_id_index');
                $table->dropIndex('enrollments_course_id_index');
            });
        }

        if (Schema::hasTable('bundles')) {
            Schema::table('bundles', function (Blueprint $table) {
                $table->dropIndex('bundles_is_active_created_at_index');
            });
        }
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $index): bool
    {
        $indexes = Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableIndexes($table);
        
        return array_key_exists($index, $indexes);
    }
};
