<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminAuditLog extends Model
{
    protected $fillable = [
        'admin_user_id', 'action', 'subject_type', 'subject_id',
        'reason', 'before_json', 'after_json', 'ip_address',
    ];

    protected function casts(): array
    {
        return ['before_json' => 'array', 'after_json' => 'array'];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}
