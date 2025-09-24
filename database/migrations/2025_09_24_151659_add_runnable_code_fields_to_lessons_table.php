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
        Schema::table('lessons', function (Blueprint $table) {
            // Add fields for runnable HTML/CSS/JS code
            $table->longText('html_code')->nullable()->after('content');
            $table->longText('css_code')->nullable()->after('html_code');
            $table->longText('js_code')->nullable()->after('css_code');
            $table->boolean('is_runnable')->default(false)->after('js_code');
            $table->json('code_settings')->nullable()->after('is_runnable');
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
            $table->dropColumn(['html_code', 'css_code', 'js_code', 'is_runnable', 'code_settings']);
        });
    }
};
