<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSSP extends Model
{

    protected $table = 'students_s_s_p';

    
    protected $fillable = [
        'user_id',
        'student_number',
        'phone',
        'date_of_birth'
    ];

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

   
    public function getFullNameAttribute(): string
    {
        return $this->user ? $this->user->name : 'N/A';
    }
}

