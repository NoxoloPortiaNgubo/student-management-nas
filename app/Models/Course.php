<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'credits',
        'max_capacity',
        'instructor_id',
        'status',
    ];

    protected $casts = [
        'credits'      => 'integer',
        'max_capacity' => 'integer',
    ];

    // Relationships
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
    public function enrolments(): HasMany
    {
        return $this->hasMany(Enrolment::class);
    }
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrolments', 'course_id', 'student_id')
                    ->withPivot(['status', 'grade', 'mark', 'enrolled_at', 'completed_at'])
                    ->withTimestamps();
    }

    // ─── Query Scopes ─────────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function scopeForInstructor($query, int $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }
    public function scopeWithAvailableSeats($query)
    {
        return $query->withCount(['enrolments' => fn ($q) => $q->where('status', 'approved')])
                     ->havingRaw('enrolments_count < max_capacity');
    }

    // Accessors 
    public function getApprovedCountAttribute(): int
    {
        return $this->enrolments()->where('status', 'approved')->count();
    }
    public function getAvailableSeatsAttribute(): int
    {
        return max(0, $this->max_capacity - $this->approved_count);
    }
    public function getIsFullAttribute(): bool
    {
        return $this->available_seats === 0;
    }
    public function getCapacityPercentageAttribute(): int
    {
        if ($this->max_capacity === 0) {
            return 0;
        }
        return (int) min(100, round(($this->approved_count / $this->max_capacity) * 100));
    }
}
