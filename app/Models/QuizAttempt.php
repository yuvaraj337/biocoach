<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    protected $fillable = ['quiz_id','user_id','attempt_number','answers','score_points','total_points','percentage','passed','started_at','submitted_at','status'];
    protected function casts(): array { return ['answers'=>'array','score_points'=>'integer','total_points'=>'integer','percentage'=>'decimal:2','passed'=>'boolean','started_at'=>'datetime','submitted_at'=>'datetime','attempt_number'=>'integer']; }
    public function quiz(): BelongsTo { return $this->belongsTo(Quiz::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
