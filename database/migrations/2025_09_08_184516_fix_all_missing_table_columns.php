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
        // Fix categories table
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if (!Schema::hasColumn('categories', 'name')) {
                    $table->string('name')->after('id');
                }
                if (!Schema::hasColumn('categories', 'slug')) {
                    $table->string('slug')->unique()->after('name');
                }
                if (!Schema::hasColumn('categories', 'description')) {
                    $table->text('description')->nullable()->after('slug');
                }
                if (!Schema::hasColumn('categories', 'parent_id')) {
                    $table->unsignedBigInteger('parent_id')->nullable()->after('description');
                }
                if (!Schema::hasColumn('categories', 'icon')) {
                    $table->string('icon')->nullable()->after('parent_id');
                }
                if (!Schema::hasColumn('categories', 'color')) {
                    $table->string('color')->default('#6366f1')->after('icon');
                }
                if (!Schema::hasColumn('categories', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('color');
                }
                if (!Schema::hasColumn('categories', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('is_active');
                }
            });
        }

        // Fix courses table
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                if (!Schema::hasColumn('courses', 'title')) {
                    $table->string('title')->after('id');
                }
                if (!Schema::hasColumn('courses', 'slug')) {
                    $table->string('slug')->unique()->after('title');
                }
                if (!Schema::hasColumn('courses', 'description')) {
                    $table->text('description')->after('slug');
                }
                if (!Schema::hasColumn('courses', 'content')) {
                    $table->longText('content')->nullable()->after('description');
                }
                if (!Schema::hasColumn('courses', 'category_id')) {
                    $table->unsignedBigInteger('category_id')->nullable()->after('content');
                }
                if (!Schema::hasColumn('courses', 'price')) {
                    $table->decimal('price', 10, 2)->default(0)->after('category_id');
                }
                if (!Schema::hasColumn('courses', 'is_free')) {
                    $table->boolean('is_free')->default(false)->after('price');
                }
                if (!Schema::hasColumn('courses', 'thumbnail')) {
                    $table->string('thumbnail')->nullable()->after('is_free');
                }
                if (!Schema::hasColumn('courses', 'duration_minutes')) {
                    $table->integer('duration_minutes')->nullable()->after('thumbnail');
                }
                if (!Schema::hasColumn('courses', 'level')) {
                    $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner')->after('duration_minutes');
                }
                if (!Schema::hasColumn('courses', 'status')) {
                    $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->after('level');
                }
                if (!Schema::hasColumn('courses', 'featured')) {
                    $table->boolean('featured')->default(false)->after('status');
                }
                if (!Schema::hasColumn('courses', 'requirements')) {
                    $table->json('requirements')->nullable()->after('featured');
                }
                if (!Schema::hasColumn('courses', 'what_you_learn')) {
                    $table->json('what_you_learn')->nullable()->after('requirements');
                }
                if (!Schema::hasColumn('courses', 'meta_title')) {
                    $table->string('meta_title')->nullable()->after('what_you_learn');
                }
                if (!Schema::hasColumn('courses', 'meta_description')) {
                    $table->text('meta_description')->nullable()->after('meta_title');
                }
            });
        }

        // Fix lessons table
        if (Schema::hasTable('lessons')) {
            Schema::table('lessons', function (Blueprint $table) {
                if (!Schema::hasColumn('lessons', 'course_id')) {
                    $table->unsignedBigInteger('course_id')->after('id');
                }
                if (!Schema::hasColumn('lessons', 'title')) {
                    $table->string('title')->after('course_id');
                }
                if (!Schema::hasColumn('lessons', 'slug')) {
                    $table->string('slug')->after('title');
                }
                if (!Schema::hasColumn('lessons', 'content')) {
                    $table->longText('content')->nullable()->after('slug');
                }
                if (!Schema::hasColumn('lessons', 'type')) {
                    $table->enum('type', ['video', 'text', 'quiz', 'assignment', 'pdf', 'h5p', 'youtube', 'vimeo', 'code'])->default('text')->after('content');
                }
                if (!Schema::hasColumn('lessons', 'video_url')) {
                    $table->string('video_url')->nullable()->after('type');
                }
                if (!Schema::hasColumn('lessons', 'video_duration')) {
                    $table->integer('video_duration')->nullable()->after('video_url');
                }
                if (!Schema::hasColumn('lessons', 'is_free')) {
                    $table->boolean('is_free')->default(false)->after('video_duration');
                }
                if (!Schema::hasColumn('lessons', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('is_free');
                }
                if (!Schema::hasColumn('lessons', 'status')) {
                    $table->enum('status', ['draft', 'published'])->default('draft')->after('sort_order');
                }
            });
        }

        // Fix enrollments table
        if (Schema::hasTable('enrollments')) {
            Schema::table('enrollments', function (Blueprint $table) {
                if (!Schema::hasColumn('enrollments', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->after('id');
                }
                if (!Schema::hasColumn('enrollments', 'course_id')) {
                    $table->unsignedBigInteger('course_id')->after('user_id');
                }
                if (!Schema::hasColumn('enrollments', 'enrolled_at')) {
                    $table->timestamp('enrolled_at')->default(now())->after('course_id');
                }
                if (!Schema::hasColumn('enrollments', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable()->after('enrolled_at');
                }
                if (!Schema::hasColumn('enrollments', 'progress_percentage')) {
                    $table->integer('progress_percentage')->default(0)->after('completed_at');
                }
                if (!Schema::hasColumn('enrollments', 'certificate_issued')) {
                    $table->boolean('certificate_issued')->default(false)->after('progress_percentage');
                }
            });
        }

        // Fix progress table
        if (Schema::hasTable('progress')) {
            Schema::table('progress', function (Blueprint $table) {
                if (!Schema::hasColumn('progress', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->after('id');
                }
                if (!Schema::hasColumn('progress', 'lesson_id')) {
                    $table->unsignedBigInteger('lesson_id')->after('user_id');
                }
                if (!Schema::hasColumn('progress', 'completed')) {
                    $table->boolean('completed')->default(false)->after('lesson_id');
                }
                if (!Schema::hasColumn('progress', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable()->after('completed');
                }
                if (!Schema::hasColumn('progress', 'time_spent')) {
                    $table->integer('time_spent')->default(0)->after('completed_at');
                }
            });
        }

        // Fix reviews table
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if (!Schema::hasColumn('reviews', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->after('id');
                }
                if (!Schema::hasColumn('reviews', 'course_id')) {
                    $table->unsignedBigInteger('course_id')->after('user_id');
                }
                if (!Schema::hasColumn('reviews', 'rating')) {
                    $table->integer('rating')->after('course_id');
                }
                if (!Schema::hasColumn('reviews', 'comment')) {
                    $table->text('comment')->nullable()->after('rating');
                }
                if (!Schema::hasColumn('reviews', 'is_approved')) {
                    $table->boolean('is_approved')->default(true)->after('comment');
                }
            });
        }

        // Add foreign keys
        $this->addForeignKeys();
    }

    /**
     * Add foreign key constraints
     */
    private function addForeignKeys()
    {
        // Categories foreign keys
        if (Schema::hasTable('categories') && Schema::hasColumn('categories', 'parent_id')) {
            try {
                Schema::table('categories', function (Blueprint $table) {
                    $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
                });
            } catch (Exception $e) {
                // Foreign key might already exist
            }
        }

        // Courses foreign keys
        if (Schema::hasTable('courses') && Schema::hasColumn('courses', 'category_id')) {
            try {
                Schema::table('courses', function (Blueprint $table) {
                    $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
                });
            } catch (Exception $e) {
                // Foreign key might already exist
            }
        }

        // Lessons foreign keys
        if (Schema::hasTable('lessons') && Schema::hasColumn('lessons', 'course_id')) {
            try {
                Schema::table('lessons', function (Blueprint $table) {
                    $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
                });
            } catch (Exception $e) {
                // Foreign key might already exist
            }
        }

        // Enrollments foreign keys
        if (Schema::hasTable('enrollments') && Schema::hasColumn('enrollments', 'user_id') && Schema::hasColumn('enrollments', 'course_id')) {
            try {
                Schema::table('enrollments', function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
                });
            } catch (Exception $e) {
                // Foreign keys might already exist
            }
        }

        // Progress foreign keys
        if (Schema::hasTable('progress') && Schema::hasColumn('progress', 'user_id') && Schema::hasColumn('progress', 'lesson_id')) {
            try {
                Schema::table('progress', function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
                });
            } catch (Exception $e) {
                // Foreign keys might already exist
            }
        }

        // Reviews foreign keys
        if (Schema::hasTable('reviews') && Schema::hasColumn('reviews', 'user_id') && Schema::hasColumn('reviews', 'course_id')) {
            try {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
                });
            } catch (Exception $e) {
                // Foreign keys might already exist
            }
        }
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