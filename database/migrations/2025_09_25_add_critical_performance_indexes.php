<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only add the most critical indexes for homepage performance
        
        // Critical for homepage course queries
        $this->addIndexSafely('courses', ['status', 'created_at']);
        $this->addIndexSafely('courses', ['category_id', 'status']);
        
        // Critical for user role checks
        $this->addIndexSafely('users', ['role']);
        
        // Critical for category navigation
        $this->addIndexSafely('categories', ['parent_id']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe index removal
        $this->dropIndexSafely('courses', ['status', 'created_at']);
        $this->dropIndexSafely('courses', ['category_id', 'status']);
        $this->dropIndexSafely('users', ['role']);
        $this->dropIndexSafely('categories', ['parent_id']);
    }
    
    /**
     * Safely add an index, ignoring if it already exists
     */
    private function addIndexSafely(string $table, array $columns): void
    {
        try {
            if (Schema::hasTable($table)) {
                $indexName = $table . '_' . implode('_', $columns) . '_index';
                DB::statement("CREATE INDEX {$indexName} ON {$table} (" . implode(', ', $columns) . ")");
            }
        } catch (\Exception $e) {
            // Index might already exist or table doesn't exist, continue silently
        }
    }
    
    /**
     * Safely drop an index, ignoring if it doesn't exist
     */
    private function dropIndexSafely(string $table, array $columns): void
    {
        try {
            if (Schema::hasTable($table)) {
                $indexName = $table . '_' . implode('_', $columns) . '_index';
                DB::statement("DROP INDEX {$indexName} ON {$table}");
            }
        } catch (\Exception $e) {
            // Index might not exist, continue silently
        }
    }
};
