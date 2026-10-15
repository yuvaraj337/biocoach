<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. User enhancements: locale and account status
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'locale')) {
                $table->string('locale', 10)->default('bn')->after('phone');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status', 20)->default('active')->after('locale');
            }
        });

        // 2. Course enhancements: teacher assignment and free flag
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'teacher_id')) {
                $table->foreignId('teacher_id')->nullable()->after('category_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('courses', 'is_free')) {
                $table->boolean('is_free')->default(false)->after('price_paise');
            }
        });

        // 3. Live classes table (scheduled in Asia/Dhaka)
        if (!Schema::hasTable('live_classes')) {
            Schema::create('live_classes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('meeting_link');
                $table->string('platform')->default('Google Meet');
                $table->dateTime('start_time');
                $table->unsignedInteger('duration_minutes')->default(60);
                $table->string('status')->default('scheduled'); // scheduled, live, completed, cancelled
                $table->timestamps();
            });
        }

        // 4. Assignments table
        if (!Schema::hasTable('assignments')) {
            Schema::create('assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->foreignId('lesson_id')->nullable()->constrained('lessons')->nullOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->unsignedInteger('total_marks')->default(100);
                $table->dateTime('due_date')->nullable();
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        // 5. Assignment submissions table
        if (!Schema::hasTable('assignment_submissions')) {
            Schema::create('assignment_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->text('submission_text')->nullable();
                $table->string('attachment_path')->nullable();
                $table->unsignedInteger('marks_obtained')->nullable();
                $table->text('feedback')->nullable();
                $table->string('status')->default('submitted'); // submitted, graded, resubmission_requested
                $table->dateTime('submitted_at')->nullable();
                $table->dateTime('graded_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('live_classes');

        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'is_free')) {
                $table->dropColumn('is_free');
            }
            if (Schema::hasColumn('courses', 'teacher_id')) {
                $table->dropConstrainedForeignId('teacher_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'locale')) {
                $table->dropColumn('locale');
            }
        });
    }
};
