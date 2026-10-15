<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'is_admin', 'role', 'locale', 'status'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function orders() { return $this->hasMany(\App\Models\Order::class); }
    public function enrollments() { return $this->hasMany(\App\Models\Enrollment::class); }
    public function lessonProgress() { return $this->hasMany(\App\Models\LessonProgress::class); }
    public function quizAttempts() { return $this->hasMany(\App\Models\QuizAttempt::class); }
    public function certificates() { return $this->hasMany(\App\Models\Certificate::class); }
    public function assignmentSubmissions() { return $this->hasMany(\App\Models\AssignmentSubmission::class); }
    public function assignedCourses() { return $this->hasMany(\App\Models\Course::class, 'teacher_id'); }
    public function taughtLiveClasses() { return $this->hasMany(\App\Models\LiveClass::class, 'teacher_id'); }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->is_admin || in_array($this->role, ['admin', 'super_admin'], true);
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isModerator(): bool
    {
        return $this->role === 'moderator';
    }

    public function isStudent(): bool
    {
        return empty($this->role) || $this->role === 'student';
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        if ($this->isAdmin() && in_array('admin', $roles, true)) {
            return true;
        }
        return in_array($this->role, $roles, true);
    }

    public function canAccessAdmin(): bool
    {
        return $this->isAdmin() || $this->isTeacher() || $this->isModerator();
    }
}
