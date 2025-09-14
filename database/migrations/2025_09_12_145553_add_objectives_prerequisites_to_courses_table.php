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
        Schema::table('courses', function (Blueprint $table) {
            $table->json('learning_objectives')->nullable()->after('description');
            $table->json('prerequisites')->nullable()->after('learning_objectives');
            
            // Add index for better search performance on title
            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['learning_objectives', 'prerequisites']);
            $table->dropIndex(['title']);
        });
    }
};