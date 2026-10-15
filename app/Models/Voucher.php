<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'discount_type', 'discount_value', 'max_uses',
        'uses', 'starts_at', 'expires_at', 'is_active'
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime', 'expires_at' => 'datetime',
            'is_active' => 'boolean', 'discount_value' => 'integer'
        ];
    }
}
