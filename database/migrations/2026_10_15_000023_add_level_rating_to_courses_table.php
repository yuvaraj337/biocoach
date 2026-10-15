<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'level')) {
                $table->string('level')->default('all_levels')->nullable();
            }
            if (!Schema::hasColumn('courses', 'rating')) {
                $table->decimal('rating', 3, 1)->nullable()->default(5.0);
            }
            if (!Schema::hasColumn('courses', 'review_count')) {
                $table->unsignedInteger('review_count')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['level', 'rating', 'review_count']);
        });
    }
};
