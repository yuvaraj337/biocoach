<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('payment_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('provider',40)->default('razorpay');
            $table->string('provider_order_id',120)->nullable()->unique();
            $table->string('provider_payment_id',120)->nullable()->unique();
            $table->string('status',24)->default('created');
            $table->unsignedBigInteger('amount_paise');
            $table->char('currency',3)->default('INR');
            $table->timestamp('verified_at')->nullable();
            $table->json('payload_json')->nullable();
            $table->timestamps();
            $table->index(['order_id','status']);
        });
    }
    public function down(): void { Schema::dropIfExists('payment_attempts'); }
};
