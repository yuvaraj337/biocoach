<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id(); $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title',190); $table->string('slug',190); $table->text('description')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->default(20); $table->unsignedTinyInteger('passing_percentage')->default(50);
            $table->unsignedTinyInteger('attempts_allowed')->default(3); $table->boolean('is_published')->default(false);
            $table->timestamps(); $table->unique(['course_id','slug']); $table->index(['course_id','is_published']);
        });
    }
    public function down(): void { Schema::dropIfExists('quizzes'); }
};
