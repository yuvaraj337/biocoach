<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizQuestion extends Model
{
    protected $fillable = ['quiz_id','prompt','options','correct_option','points','sort_order'];
    protected function casts(): array { return ['options'=>'array','correct_option'=>'integer','points'=>'integer','sort_order'=>'integer']; }
    public function quiz(): BelongsTo { return $this->belongsTo(Quiz::class); }
}
