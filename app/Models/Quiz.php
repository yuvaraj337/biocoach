<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = ['course_id','title','slug','description','duration_minutes','passing_percentage','attempts_allowed','is_published'];
    protected function casts(): array { return ['duration_minutes'=>'integer','passing_percentage'=>'integer','attempts_allowed'=>'integer','is_published'=>'boolean']; }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function questions(): HasMany { return $this->hasMany(QuizQuestion::class)->orderBy('sort_order')->orderBy('id'); }
    public function attempts(): HasMany { return $this->hasMany(QuizAttempt::class); }
}
