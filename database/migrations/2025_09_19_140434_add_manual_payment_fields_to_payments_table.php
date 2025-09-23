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
            // Manual payment fields
            $table->string('payment_method')->default('online')->after('gateway'); // 'online', 'bank_transfer', 'mobile_wallet'
            $table->string('user_transaction_id')->nullable()->after('gateway_transaction_id'); // User provided transaction ID
            $table->text('payment_note')->nullable()->after('user_transaction_id'); // User's payment notes
            $table->string('sender_name')->nullable()->after('payment_note'); // Name of person who sent money
            $table->string('sender_mobile')->nullable()->after('sender_name'); // Mobile number used for payment
            $table->timestamp('approved_at')->nullable()->after('sender_mobile'); // When admin approved the payment
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at'); // Admin who approved
            $table->text('admin_note')->nullable()->after('approved_by'); // Admin's approval/rejection note
            
            // Add foreign key for approved_by
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            // Add index for manual payment queries
            $table->index(['payment_method', 'status']);
            $table->index(['user_transaction_id']);
        });
        
        // Update the status enum to include 'approved' and 'rejected'
        DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending', 'success', 'failed', 'cancelled', 'approved', 'rejected') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropIndex(['payment_method', 'status']);
            $table->dropIndex(['user_transaction_id']);
            $table->dropColumn([
                'payment_method',
                'user_transaction_id', 
                'payment_note',
                'sender_name',
                'sender_mobile',
                'approved_at',
                'approved_by',
                'admin_note'
            ]);
        });
        
        // Revert status enum
        DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending', 'success', 'failed', 'cancelled') DEFAULT 'pending'");
    }
};