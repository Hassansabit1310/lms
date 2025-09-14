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
        Schema::table('subscriptions', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('subscriptions', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id');
            }
            if (!Schema::hasColumn('subscriptions', 'plan_type')) {
                $table->enum('plan_type', ['monthly', 'annual'])->after('user_id');
            }
            if (!Schema::hasColumn('subscriptions', 'amount')) {
                $table->decimal('amount', 10, 2)->after('plan_type');
            }
            if (!Schema::hasColumn('subscriptions', 'status')) {
                $table->enum('status', ['active', 'inactive', 'cancelled', 'expired'])->default('active')->after('amount');
            }
            if (!Schema::hasColumn('subscriptions', 'start_date')) {
                $table->timestamp('start_date')->after('status');
            }
            if (!Schema::hasColumn('subscriptions', 'end_date')) {
                $table->timestamp('end_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('subscriptions', 'gateway_subscription_id')) {
                $table->string('gateway_subscription_id')->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('subscriptions', 'next_billing_date')) {
                $table->timestamp('next_billing_date')->nullable()->after('gateway_subscription_id');
            }
        });

        // Add foreign key if it doesn't exist
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'user_id')) {
                try {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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