<?php

use App\Http\Controllers\Frontend\CoursesController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\VolunteerAuthController;
use App\Http\Controllers\Frontend\VolunteerController;
use App\Http\Controllers\Frontend\VolunteerDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::group(['as' => 'frontend.', 'namespace' => 'Frontend'], function () {

    Route::get('/get-districts-by-city', [HomeController::class, 'getDistrictsByCity'])->name('getDistrictsByCity');

    // Volunteer join (public)
    Route::get('/volunteer/join', [VolunteerController::class, 'join'])->name('volunteer.join');
    Route::post('/volunteer/join', [VolunteerController::class, 'store'])->name('volunteer.store');
});

// Volunteer login (public)
Route::get('/volunteer/login', [VolunteerAuthController::class, 'showLoginForm'])->name('volunteer.login')->middleware('guest:volunteer');
Route::post('/volunteer/login', [VolunteerAuthController::class, 'login'])->name('volunteer.login.submit');
Route::post('/volunteer/logout', [VolunteerAuthController::class, 'logout'])->name('volunteer.logout')->middleware('auth:volunteer');

// Volunteer dashboard (authenticated)
Route::group(['prefix' => 'volunteer', 'as' => 'volunteer.', 'middleware' => ['auth:volunteer'], 'namespace' => 'Frontend'], function () {
    Route::get('/dashboard', [VolunteerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/tasks/{task}', [VolunteerDashboardController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{task}/start', [VolunteerDashboardController::class, 'start'])->name('tasks.start');
    Route::post('/tasks/{task}/finish', [VolunteerDashboardController::class, 'finish'])->name('tasks.finish');
});

// Signed URL for task QR verification (no auth)
Route::get('/volunteer/task-verify/{task}', [VolunteerDashboardController::class, 'verify'])->name('volunteer.task-verify')->middleware('signed');

Route::group(['as' => 'frontend.', 'namespace' => 'Frontend'], function () {

    // Course Attendance
    Route::get('/course-attendance/{course}', [CoursesController::class, 'courseAttendance'])->name('course-attendance');
    Route::post('/course-attendance/check', [CoursesController::class, 'checkAttendance'])->name('course-attendance.check');

    // Course Certificate
    Route::get('/course-certificate/{course}', [CoursesController::class, 'courseCertificate'])->name('course-certificate');
    Route::post('/course-certificate/request', [CoursesController::class, 'requestCertificate'])->name('course-certificate.request');

    // Subscription
    Route::post('/subscription', [HomeController::class, 'subscription'])->name('subscription.store');
});
