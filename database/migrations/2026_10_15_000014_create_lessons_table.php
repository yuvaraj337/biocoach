<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void { Schema::create('lessons', function (Blueprint $table) { $table->id(); $table->foreignId('course_id')->constrained()->cascadeOnDelete(); $table->string('title',190); $table->string('slug',190); $table->string('summary',500)->nullable(); $table->string('video_url',1000)->nullable(); $table->longText('content')->nullable(); $table->unsignedInteger('sort_order')->default(0); $table->boolean('is_preview')->default(false); $table->boolean('is_published')->default(false)->index(); $table->timestamps(); $table->unique(['course_id','slug']); $table->index(['course_id','sort_order']); }); }
    public function down(): void { Schema::dropIfExists('lessons'); }
};
