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


// 1. STANDARD SHARED DASHBOARD (Fallback)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 2. ADMIN ONLY ROUTES
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('dashboard'); // We can make a separate admin view folder later!
    })->name('admin.dashboard');
    
    // Your teammates can add their Admin management routes right here
});

// 3. LECTURER ONLY ROUTES
Route::middleware(['auth', 'role:lecturer'])->group(function () {
    Route::get('/lecturer/dashboard', function () {
        return "<h3>Welcome to the Lecturer Portal. Here you can post grades.</h3>";
    })->name('lecturer.dashboard');
});

// 4. STUDENT ONLY ROUTES
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student/dashboard', function () {
        return "<h3>Welcome to your Student Portal. Here you can see your courses.</h3>";
    })->name('student.dashboard');
});
