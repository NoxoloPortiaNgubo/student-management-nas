<?php

namespace App\Policies;

use App\Models\Enrolment;
use App\Models\User;

class EnrolmentPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Enrolment $enrolment): bool
    {
        if ($user->isStudent()) {
            return $enrolment->student_id === $user->id;
        }
        if ($user->isInstructor()) {
            return $enrolment->course->instructor_id === $user->id;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->isStudent();
    }

    public function update(User $user, Enrolment $enrolment): bool
    {
        if ($user->isInstructor()) {
            return $enrolment->course->instructor_id === $user->id;
        }
        return false;
    }

    public function withdraw(User $user, Enrolment $enrolment): bool
    {
        return $user->isStudent()
            && $enrolment->student_id === $user->id
            && $enrolment->status === 'pending';
    }

    public function delete(User $user, Enrolment $enrolment): bool
    {
        return false;
    }
}