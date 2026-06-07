<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Enrolment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'status',
        'grade',
        'mark',
        'enrolled_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'mark'         => 'float',
        'enrolled_at'  => 'date',
        'completed_at' => 'date',
    ];

    // Relationships

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // Query Scopes

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForCourse($query, int $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    // Accessors / Mutators

    // Derive letter grade automatically when mark is set. 
    public function setMarkAttribute(?float $value): void
    {
        $this->attributes['mark'] = $value;

        if ($value === null) {
            return;
        }

        $this->attributes['grade'] = match (true) {
            $value >= 90 => 'A+',
            $value >= 85 => 'A',
            $value >= 80 => 'A-',
            $value >= 75 => 'B+',
            $value >= 70 => 'B',
            $value >= 65 => 'B-',
            $value >= 60 => 'C+',
            $value >= 55 => 'C',
            $value >= 50 => 'C-',
            $value >= 45 => 'D',
            default      => 'F',
        };
    }

    // Human-readable status label.
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }

    // Bootstrap badge class for the current status.
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'approved'  => 'success',
            'pending'   => 'warning',
            'rejected'  => 'danger',
            'withdrawn' => 'secondary',
            default     => 'dark',
        };
    }

    // Whether the enrolment resulted in a passing grade (mark ≥ 50).
    public function getIsPassingAttribute(): bool
    {
        return $this->mark !== null && $this->mark >= 50;
    }
}
