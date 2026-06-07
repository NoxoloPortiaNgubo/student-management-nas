<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequestSSP extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'student_number' => 'required|string|unique:students_s_s_p,student_number',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
        ];
    }
}

