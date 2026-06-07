<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {

    // Dashboard (role-aware)
    Route::get('/dashboard', [DashboardControllerXYZ::class, 'index'])
         ->name('dashboard');

    // Courses (RESTful resource)
    Route::resource('courses', CourseControllerXYZ::class);

    // Enrolments (RESTful resource)
    Route::resource('enrolments', EnrolmentControllerXYZ::class);

    // Reports (admin + instructor)
    Route::prefix('reports')->name('reports.')->group(function () {

        // System-wide overview (admin only)
        Route::get('/overview', [ReportControllerXYZ::class, 'overview'])
             ->name('overview');

        // Single student academic transcript
        Route::get('/student/{student}', [ReportControllerXYZ::class, 'studentProgress'])
             ->name('student');

        // Course-level grade & enrolment report
        Route::get('/course/{course}', [ReportControllerXYZ::class, 'courseReport'])
             ->name('course');
    });
});