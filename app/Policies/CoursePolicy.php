<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
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

    public function view(User $user, Course $course): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Course $course): bool
    {
        return $user->isInstructor() && $course->instructor_id === $user->id;
    }

    public function delete(User $user, Course $course): bool
    {
        return false;
    }

    public function restore(User $user, Course $course): bool
    {
        return false;
    }
}