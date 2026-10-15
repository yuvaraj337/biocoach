<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'order_number',
        'amount_paise',
        'status',
        'payment_status',
        'payment_reference',
        'paid_at',
        'payment_method',
        'transaction_id',
        'payment_proof_path',
        'payment_proof_original_name',
        'proof_submitted_at',
        'rejection_reason',
        'verified_by',
        'verified_at',
        'verification_notes',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'proof_submitted_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function paymentAttempts(): HasMany
    {
        return $this->hasMany(PaymentAttempt::class);
    }

    public function enrollment(): HasOne
    {
        return $this->hasOne(Enrollment::class);
    }

    public function isAwaitingVerification(): bool
    {
        return $this->payment_status === 'verification_pending' || $this->status === 'verification_pending';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid' || $this->status === 'paid';
    }

    public function isRejected(): bool
    {
        return $this->payment_status === 'rejected' || $this->status === 'rejected';
    }

    public function isPendingSubmission(): bool
    {
        return !$this->isPaid() && !$this->isAwaitingVerification() && !$this->isRejected();
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->isPaid()) {
            return 'Verified / Enrolled';
        }
        if ($this->isAwaitingVerification()) {
            return 'Awaiting Verification';
        }
        if ($this->isRejected()) {
            return 'Rejected — Action Required';
        }
        return 'Payment Not Submitted';
    }

    public function getStatusBadgeColorAttribute(): string
    {
        if ($this->isPaid()) {
            return '#107e46'; // Green
        }
        if ($this->isAwaitingVerification()) {
            return '#b7791f'; // Amber
        }
        if ($this->isRejected()) {
            return '#c53030'; // Red
        }
        return '#4a5568'; // Gray
    }
}
