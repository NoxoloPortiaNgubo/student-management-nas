<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * CourseController handles CRUD for the Course resource.
 */
class CourseControllerXYZ extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(Course::class, 'course');
    }

    /** Display paginated list of courses with enrolment counts. */
    public function index(): View
    {
        $courses = Course::with('instructor')
            ->withCount([
                'enrolments',
                'enrolments as approved_enrolments_count' => fn ($q) => $q->where('status', 'approved'),
            ])
            ->when(request('search'), fn ($q, $s) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
            )
            ->when(request('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('courses.index', compact('courses'));
    }

    /** Show creation form (admin only). */
    public function create(): View
    {
        $instructors = User::instructors()->orderBy('name')->get();
        return view('courses.create', compact('instructors'));
    }

    /** Persist a new course. */
    public function store(StoreCourseRequest $request): RedirectResponse
    {
        Course::create($request->validated());

        return redirect()->route('courses.index')
                         ->with('success', 'Course created successfully.');
    }

    /** Show course detail with enrolled students. */
    public function show(Course $course): View
    {
        $course->load([
            'instructor',
            'enrolments.student',
        ]);

        return view('courses.show', compact('course'));
    }

    /** Edit form. */
    public function edit(Course $course): View
    {
        $instructors = User::instructors()->orderBy('name')->get();
        return view('courses.edit', compact('course', 'instructors'));
    }

    /** Update a course record. */
    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $course->update($request->validated());

        return redirect()->route('courses.show', $course)
                         ->with('success', 'Course updated successfully.');
    }

    /** Soft-delete a course. */
    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()->route('courses.index')
                         ->with('success', 'Course removed.');
    }
}
