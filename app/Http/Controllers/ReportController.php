<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrolment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ReportController generates aggregate progress reports.
 */
class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Only admins and instructors may view reports
        $this->middleware(function ($request, $next) {
            if (auth()->user()->isStudent()) {
                abort(403, 'Students do not have access to reports.');
            }
            return $next($request);
        });
    }

    // Student Progress Report 

    /**
     * Show a single student's full academic transcript.
     */
    public function studentProgress(User $student): View
    {
        abort_if(! $student->isStudent(), 404);

        $enrolments = $student->enrolments()
            ->with('course')
            ->latest()
            ->get();

        $summary = [
            'total_courses'    => $enrolments->count(),
            'approved'         => $enrolments->where('status', 'approved')->count(),
            'pending'          => $enrolments->where('status', 'pending')->count(),
            'average_mark'     => $enrolments->whereNotNull('mark')->avg('mark'),
            'passed'           => $enrolments->where('mark', '>=', 50)->count(),
            'failed'           => $enrolments->whereNotNull('mark')->where('mark', '<', 50)->count(),
            'total_credits'    => $enrolments->where('mark', '>=', 50)
                                             ->sum(fn ($e) => $e->course->credits ?? 0),
        ];

        return view('reports.student-progress', compact('student', 'enrolments', 'summary'));
    }

    // Course Enrolment Report 

    /**
     * Statistics for a single course: grade distribution, pass rate, top performers.
     */
    public function courseReport(Course $course): View
    {
        // Only the assigned instructor or admin can view this report
        if (auth()->user()->isInstructor() && $course->instructor_id !== auth()->id()) {
            abort(403);
        }

        $enrolments = $course->enrolments()->with('student')->get();

        $gradeDistribution = $enrolments
            ->whereNotNull('grade')
            ->groupBy('grade')
            ->map->count()
            ->sortKeys();

        $summary = [
            'total_enrolled' => $enrolments->count(),
            'approved'       => $enrolments->where('status', 'approved')->count(),
            'pending'        => $enrolments->where('status', 'pending')->count(),
            'average_mark'   => round($enrolments->whereNotNull('mark')->avg('mark'), 2),
            'pass_rate'      => $this->passRate($enrolments),
            'highest_mark'   => $enrolments->whereNotNull('mark')->max('mark'),
            'lowest_mark'    => $enrolments->whereNotNull('mark')->min('mark'),
        ];

        return view('reports.course-report', compact('course', 'enrolments', 'summary', 'gradeDistribution'));
    }

    // Admin Overview Report

    /**
     * System-wide statistics for admin dashboard.
     * Returns data used by the admin dashboard and a standalone report page.
     */
    public function overview(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $stats = [
            'total_students'     => User::students()->count(),
            'total_instructors'  => User::instructors()->count(),
            'total_courses'      => Course::count(),
            'active_courses'     => Course::active()->count(),
            'total_enrolments'   => Enrolment::count(),
            'pending_enrolments' => Enrolment::pending()->count(),
            'approved_enrolments'=> Enrolment::approved()->count(),
            'overall_pass_rate'  => $this->passRate(Enrolment::whereNotNull('mark')->get()),
        ];

        // Top 5 most enrolled courses
        $topCourses = Course::withCount(['enrolments as approved_count' => fn ($q) => $q->approved()])
            ->orderByDesc('approved_count')
            ->take(5)
            ->get();

        // Monthly enrolment trend (last 6 months)
        $trend = Enrolment::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();

        return view('reports.overview', compact('stats', 'topCourses', 'trend'));
    }

    // Private helpers 

    private function passRate($enrolments): float
    {
        $graded = $enrolments->filter(fn ($e) => $e->mark !== null);
        if ($graded->isEmpty()) {
            return 0.0;
        }
        return round(($graded->where('mark', '>=', 50)->count() / $graded->count()) * 100, 1);
    }
}
