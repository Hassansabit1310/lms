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
        Schema::create('h5_p_contents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path'); // Path to original .h5p file
            $table->string('extracted_path')->nullable(); // Path to extracted content
            $table->integer('file_size'); // File size in bytes
            $table->string('content_type')->nullable(); // H5P content type (e.g., 'H5P.InteractiveVideo')
            $table->json('metadata')->nullable(); // H5P metadata from h5p.json
            $table->string('thumbnail_path')->nullable(); // Generated thumbnail
            $table->string('version')->nullable(); // H5P content version
            $table->boolean('is_active')->default(true);
            $table->string('upload_status')->default('pending'); // pending, processing, completed, failed
            $table->text('error_message')->nullable(); // Error details if processing failed
            $table->timestamps();
            
            // Indexes
            $table->index('content_type');
            $table->index('is_active');
            $table->index('upload_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('h5_p_contents');
    }
};
