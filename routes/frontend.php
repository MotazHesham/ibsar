<?php

use App\Http\Controllers\Frontend\CoursesController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\VolunteerController;
use App\Http\Controllers\Frontend\AuthController;
use Illuminate\Support\Facades\Route; 

Route::get('/', [HomeController::class, 'index'])->name('home'); 

Route::group(['as' => 'frontend.', 'namespace' => 'Frontend'], function () {

    Route::get('/get-districts-by-city', [HomeController::class, 'getDistrictsByCity'])->name('getDistrictsByCity');

    // Volunteer join (public)
    Route::get('/volunteer/join', [VolunteerController::class, 'join'])->name('volunteer.join');
    Route::post('/volunteer/join', [VolunteerController::class, 'store'])->name('volunteer.store');

    // Course Attendance
    Route::get('/course-attendance/{course}', [CoursesController::class, 'courseAttendance'])->name('course-attendance');
    Route::post('/course-attendance/check', [CoursesController::class, 'checkAttendance'])->name('course-attendance.check');

    // Course Certificate
    Route::get('/course-certificate/{course}', [CoursesController::class, 'courseCertificate'])->name('course-certificate');
    Route::post('/course-certificate/request', [CoursesController::class, 'requestCertificate'])->name('course-certificate.request');

    // Subscription
    Route::post('/subscription', [HomeController::class, 'subscription'])->name('subscription.store');
});
