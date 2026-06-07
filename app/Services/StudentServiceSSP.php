<?php

namespace App\Services;

use App\Models\StudentSSP;

class StudentServiceSSP
{
    public function getAllStudents()
    {
        
        return StudentSSP::with('user')->paginate(10);
    }

    public function createStudent(array $data): StudentSSP
    {
        return StudentSSP::create($data);
    }

    public function updateStudent(StudentSSP $student, array $data): bool
    {
        return $student->update($data);
    }

    public function deleteStudent(StudentSSP $student): ?bool
    {
        return $student->delete();
    }
}