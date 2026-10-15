<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void { Schema::create('lesson_resources', function (Blueprint $table) { $table->id(); $table->foreignId('lesson_id')->constrained()->cascadeOnDelete(); $table->string('title',190); $table->string('file_path',500); $table->string('original_name',255); $table->string('mime_type',150)->nullable(); $table->unsignedBigInteger('file_size')->default(0); $table->timestamps(); }); }
    public function down(): void { Schema::dropIfExists('lesson_resources'); }
};
