<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserSSP extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'student_number',
        'phone',
        'date_of_birth',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth'     => 'date',
        'password'          => 'hashed',
    ];

    // Role Helpers

    public function isAdmin(): bool 
    { 
        return $this->role === 'admin'; 
    }
    public function isInstructor(): bool   
    { return $this->role === 'instructor'; 
    }
    public function isStudent(): bool
    {
         return $this->role === 'student'; 
    }

    // Relationships 

    //Courses the student is enrolled in.
    public function enrolments(): HasMany
    {
        return $this->hasMany(Enrolment::class, 'student_id');
    }

    // Courses this instructor teaches.
    public function taughtCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    // Direct many-to-many to courses (for students).
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrolments', 'student_id', 'course_id')
                    ->withPivot(['status', 'grade', 'mark', 'enrolled_at'])
                    ->withTimestamps();
    }

    // Query Scopes

    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    public function scopeInstructors($query)
    {
        return $query->where('role', 'instructor');
    }

    // Accessors

    // Overall GPA-style average mark across approved+completed enrolments.
    public function getAverageMarkAttribute(): ?float
    {
        $marks = $this->enrolments()
                      ->whereNotNull('mark')
                      ->where('status', 'approved')
                      ->pluck('mark');

        return $marks->count() > 0 ? round($marks->avg(), 2) : null;
    }

    // Number of courses the student passed (mark ≥ 50).
    public function getPassedCoursesCountAttribute(): int
    {
        return $this->enrolments()
                    ->where('status', 'approved')
                    ->where('mark', '>=', 50)
                    ->count();
    }
}
