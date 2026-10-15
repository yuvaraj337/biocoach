<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method', 32)->nullable()->default('manual_upi')->after('amount_paise');
            $table->string('transaction_id', 120)->nullable()->index()->after('payment_method');
            $table->string('payment_proof_path', 255)->nullable()->after('transaction_id');
            $table->string('payment_proof_original_name', 255)->nullable()->after('payment_proof_path');
            $table->timestamp('proof_submitted_at')->nullable()->after('payment_proof_original_name');
            $table->text('rejection_reason')->nullable()->after('proof_submitted_at');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->after('rejection_reason');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->text('verification_notes')->nullable()->after('verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'payment_method',
                'transaction_id',
                'payment_proof_path',
                'payment_proof_original_name',
                'proof_submitted_at',
                'rejection_reason',
                'verified_by',
                'verified_at',
                'verification_notes',
            ]);
        });
    }
};
