<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonResource extends Model
{
    protected $fillable = ['lesson_id','title','file_path','original_name','mime_type','file_size'];
    public function lesson(): BelongsTo { return $this->belongsTo(Lesson::class); }
}
