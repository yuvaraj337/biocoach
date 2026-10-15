<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    protected $fillable = ['course_id','title','slug','summary','video_url','content','sort_order','is_preview','is_published'];
    protected function casts(): array { return ['is_preview'=>'boolean','is_published'=>'boolean','sort_order'=>'integer']; }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function resources(): HasMany { return $this->hasMany(LessonResource::class)->orderBy('id'); }
    public function progress(): HasMany { return $this->hasMany(LessonProgress::class); }
}
