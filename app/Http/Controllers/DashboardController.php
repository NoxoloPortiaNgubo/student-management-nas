<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrolment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * DashboardController routes authenticated users to their role-specific dashboard.
 */
class DashboardControllerXYZ extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin'      => $this->adminDashboard(),
            'instructor' => $this->instructorDashboard($user),
            default      => $this->studentDashboard($user),
        };
    }

    // ─── Admin Dashboard ──────────────────────────────────────────────────────

    private function adminDashboard(): View
    {
        $stats = [
            'students'            => User::students()->count(),
            'instructors'         => User::instructors()->count(),
            'courses'             => Course::count(),
            'active_courses'      => Course::active()->count(),
            'enrolments'          => Enrolment::count(),
            'pending_enrolments'  => Enrolment::pending()->count(),
        ];

        $recentEnrolments = Enrolment::with(['student', 'course'])
            ->latest()
            ->take(8)
            ->get();

        $topCourses = Course::withCount(['enrolments as approved_count' => fn ($q) => $q->approved()])
            ->orderByDesc('approved_count')
            ->take(5)
            ->get();

        $recentStudents = User::students()
            ->latest()
            ->take(5)
            ->get();

        return view('dashboards.admin', compact(
            'stats', 'recentEnrolments', 'topCourses', 'recentStudents'
        ));
    }

    // ─── Instructor Dashboard ─────────────────────────────────────────────────

    private function instructorDashboard(User $user): View
    {
        $courses = $user->taughtCourses()
            ->withCount([
                'enrolments',
                'enrolments as approved_count' => fn ($q) => $q->approved(),
                'enrolments as pending_count'  => fn ($q) => $q->pending(),
            ])
            ->get();

        $pendingEnrolments = Enrolment::with(['student', 'course'])
            ->whereHas('course', fn ($q) => $q->where('instructor_id', $user->id))
            ->pending()
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'my_courses'  => $courses->count(),
            'my_students' => $courses->sum('approved_count'),
            'pending'     => $courses->sum('pending_count'),
        ];

        return view('dashboards.instructor', compact('courses', 'pendingEnrolments', 'stats'));
    }

    // ─── Student Dashboard ────────────────────────────────────────────────────

    private function studentDashboard(User $user): View
    {
        $enrolments = $user->enrolments()
            ->with('course')
            ->latest()
            ->get();

        $stats = [
            'enrolled'      => $enrolments->where('status', 'approved')->count(),
            'pending'       => $enrolments->where('status', 'pending')->count(),
            'completed'     => $enrolments->whereNotNull('mark')->count(),
            'average_mark'  => round($enrolments->whereNotNull('mark')->avg('mark') ?? 0, 1),
        ];

        $availableCourses = Course::active()
            ->whereNotIn('id', $enrolments->pluck('course_id'))
            ->withCount(['enrolments as approved_count' => fn ($q) => $q->approved()])
            ->having('approved_count', '<', \DB::raw('max_capacity'))
            ->take(6)
            ->get();

        return view('dashboards.student', compact('enrolments', 'stats', 'availableCourses'));
    }
}
