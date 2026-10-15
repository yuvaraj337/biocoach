<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'category_id', 'teacher_id', 'title', 'slug', 'summary', 'description',
        'thumbnail', 'price_paise', 'is_free', 'is_published', 'seo_title', 'seo_description',
        'level', 'rating', 'review_count'
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_free' => 'boolean',
            'price_paise' => 'integer',
            'rating' => 'float',
            'review_count' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order')->orderBy('id');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function liveClasses(): HasMany
    {
        return $this->hasMany(LiveClass::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getPriceRupeesAttribute(): string
    {
        return number_format($this->price_paise / 100, 2);
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->is_free || $this->price_paise === 0) {
            return is_bangla() ? 'ফ্রি' : 'Free';
        }
        return bdt($this->price_paise, true);
    }

    public function getThumbnailUrlAttribute(): string
    {
        if (!empty($this->thumbnail)) {
            $thumb = trim($this->thumbnail);
            if (str_starts_with($thumb, 'http://') || str_starts_with($thumb, 'https://')) {
                return $thumb;
            }
            $cleanPath = ltrim($thumb, '/');
            if (str_starts_with($cleanPath, 'storage/')) {
                return asset($cleanPath);
            }
            if (file_exists(public_path($cleanPath))) {
                return asset($cleanPath);
            }
            if (file_exists(public_path('storage/' . $cleanPath))) {
                return asset('storage/' . $cleanPath);
            }
            return asset($cleanPath);
        }

        if (file_exists(public_path('images/courses/neet-biology.jpg'))) {
            return asset('images/courses/neet-biology.jpg');
        }
        return asset('images/course-dna.svg');
    }
}
