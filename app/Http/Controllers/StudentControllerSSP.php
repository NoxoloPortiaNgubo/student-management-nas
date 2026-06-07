<?php

namespace App\Http\Controllers;

use App\Models\StudentSSP;
use App\Http\Requests\StoreStudentRequestSSP;
use App\Http\Requests\UpdateStudentRequestSSP;
use App\Services\StudentServiceSSP;


class StudentControllerSSP extends ControllerSSP
{
    
    public function __construct(
        protected StudentServiceSSP $studentService
    ) {}

    public function index()
    {
        $students = $this->studentService->getAllStudents();
        return view('students.index', compact('students'));

    } 

    public function create()
    {
       
        return view('students.create');
    }

    public function store(StoreStudentRequestSSP $request)
    {
        $this->studentService->createStudent($request->validated());
        return redirect()->route('students.index')->with('success', 'Student added successfully.');
    }

    public function edit(StudentSSP $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(UpdateStudentRequestSSP $request, StudentSSP $student)
    {
        $this->studentService->updateStudent($student, $request->validated());
        return redirect()->route('students.index')->with('success', 'Student record updated.');
    }

    public function destroy(StudentSSP $student)
    {
        $this->studentService->deleteStudent($student);
        return redirect()->route('students.index')->with('success', 'Student deleted.');
    }
}
