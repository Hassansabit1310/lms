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
        Schema::table('payments', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('payments', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id');
            }
            if (!Schema::hasColumn('payments', 'course_id')) {
                $table->unsignedBigInteger('course_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('payments', 'subscription_id')) {
                $table->unsignedBigInteger('subscription_id')->nullable()->after('course_id');
            }
            if (!Schema::hasColumn('payments', 'amount')) {
                $table->decimal('amount', 10, 2)->after('subscription_id');
            }
            if (!Schema::hasColumn('payments', 'gateway')) {
                $table->string('gateway')->default('sslcommerz')->after('amount');
            }
            if (!Schema::hasColumn('payments', 'transaction_id')) {
                $table->string('transaction_id')->unique()->after('gateway');
            }
            if (!Schema::hasColumn('payments', 'gateway_transaction_id')) {
                $table->string('gateway_transaction_id')->nullable()->after('transaction_id');
            }
            if (!Schema::hasColumn('payments', 'status')) {
                $table->enum('status', ['pending', 'success', 'failed', 'cancelled'])->default('pending')->after('gateway_transaction_id');
            }
            if (!Schema::hasColumn('payments', 'gateway_response')) {
                $table->json('gateway_response')->nullable()->after('status');
            }
        });

        // Add foreign keys if they don't exist
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'user_id') && Schema::hasColumn('payments', 'course_id') && Schema::hasColumn('payments', 'subscription_id')) {
                // Foreign keys will be added only if columns exist
                try {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                } catch (Exception $e) {
                    // Foreign key might already exist
                }
                
                try {
                    $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null');
                } catch (Exception $e) {
                    // Foreign key might already exist
                }
                
                try {
                    $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('set null');
                } catch (Exception $e) {
                    // Foreign key might already exist
                }
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
        // Don't drop columns in down method to prevent data loss
    }
};