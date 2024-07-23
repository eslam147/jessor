<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\student\TopicsController;
use App\Http\Controllers\student\SubjectController;
use App\Http\Controllers\student\TeachersController;
use App\Http\Controllers\student\StudentDashboardController;

Route::prefix('student_dashboard')->group(function(){
    Route::group(['middleware' => 'auth'], function () {
        Route::resource('/home', StudentDashboardController::class);
        Route::resource('/subjects', SubjectController::class);
        Route::resource('/topics', TopicsController::class);
        Route::get('/teacher_lessons/{teacher_id}/subject/{subject_id}', [TeachersController::class,'teacher_lessons'])->name('teacher.lessons');
    });
});
