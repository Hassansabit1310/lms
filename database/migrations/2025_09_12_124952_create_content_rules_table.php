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
        Schema::create('content_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->morphs('target_content'); // What content this rule applies to
            $table->enum('rule_type', ['show_if', 'hide_if', 'lock_if', 'unlock_if'])->default('show_if');
            $table->json('conditions'); // Complex conditional logic
            $table->json('actions'); // What happens when conditions are met
            $table->integer('priority')->default(0); // Rule execution priority
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by'); // Admin who created the rule
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['rule_type', 'is_active']);
            $table->index(['priority']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_rules');
    }
};
