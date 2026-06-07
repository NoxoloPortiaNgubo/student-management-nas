<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrolment;
use App\Http\Requests\StoreEnrolmentRequest;
use App\Http\Requests\UpdateEnrolmentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/*EnrolmentController manages student enrolment requests and instructor approvals.*/
class EnrolmentControllerSSP extends ControllerSSP
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /*
     * List enrolments : filtered by role:
     * Admin    : all enrolments
     * Instructor: enrolments on their courses
     * Student  : their own enrolments
     */
    public function index(): View
    {
        $this->authorize('viewAny', Enrolment::class);

        $user = Auth::user();

        $enrolments = Enrolment::with(['student', 'course'])
            ->when($user->isStudent(),    fn ($q) => $q->forStudent($user->id))
            ->when($user->isInstructor(), fn ($q) =>
                $q->whereHas('course', fn ($c) => $c->where('instructor_id', $user->id))
            )
            ->when(request('status'),     fn ($q, $s) => $q->where('status', $s))
            ->when(request('course_id'),  fn ($q, $id) => $q->where('course_id', $id))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('enrolments.index', compact('enrolments'));
    }

    /** Form for a student to choose and request a course. */
    public function create(): View
    {
        $this->authorize('create', Enrolment::class);

        // Show only active courses the student is NOT already enrolled in
        $enrolled = Auth::user()->enrolments()->pluck('course_id');

        $courses = Course::active()
            ->whereNotIn('id', $enrolled)
            ->withCount(['enrolments as approved_count' => fn ($q) => $q->where('status', 'approved')])
            ->having('approved_count', '<', \DB::raw('max_capacity'))
            ->orderBy('name')
            ->get();

        return view('enrolments.create', compact('courses'));
    }

    /** Student submits an enrolment request. */
    public function store(StoreEnrolmentRequest $request): RedirectResponse
    {
        $this->authorize('create', Enrolment::class);

        $course = Course::findOrFail($request->course_id);

        // Enforce capacity guard
        if ($course->is_full) {
            return back()->with('error', 'This course is at full capacity.');
        }

        Enrolment::create([
            'student_id'  => Auth::id(),
            'course_id'   => $course->id,
            'status'      => 'pending',
            'enrolled_at' => now(),
        ]);

        return redirect()->route('enrolments.index')
                         ->with('success', 'Enrolment request submitted. Awaiting approval.');
    }

    /** Show a single enrolment detail. */
    public function show(Enrolment $enrolment): View
    {
        $this->authorize('view', $enrolment);
        $enrolment->load(['student', 'course.instructor']);

        return view('enrolments.show', compact('enrolment'));
    }

    /** Instructor edit form: approve/reject and enter grade. */
    public function edit(Enrolment $enrolment): View
    {
        $this->authorize('update', $enrolment);
        $enrolment->load(['student', 'course']);

        return view('enrolments.edit', compact('enrolment'));
    }

    /** Save approval/rejection and optional grade. */
    public function update(UpdateEnrolmentRequest $request, Enrolment $enrolment): RedirectResponse
    {
        $this->authorize('update', $enrolment);

        $enrolment->update($request->validated());

        if ($request->filled('mark')) {
            $enrolment->completed_at = now()->toDateString();
            $enrolment->save();
        }

        return redirect()->route('enrolments.show', $enrolment)
                         ->with('success', 'Enrolment updated.');
    }

    /** Student withdraws their own pending enrolment. */
    public function destroy(Enrolment $enrolment): RedirectResponse
    {
        $this->authorize('withdraw', $enrolment);

        $enrolment->update(['status' => 'withdrawn']);

        return redirect()->route('enrolments.index')
                         ->with('success', 'Enrolment withdrawn.');
    }
}
