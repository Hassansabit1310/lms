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
            $table->unsignedBigInteger('bundle_id')->nullable()->after('subscription_id');
            $table->foreign('bundle_id')->references('id')->on('bundles')->onDelete('set null');
            $table->index(['bundle_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['bundle_id']);
            $table->dropIndex(['bundle_id']);
            $table->dropColumn('bundle_id');
        });
    }
};