<?php

use App\Http\Controllers\student\EnrollController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\student\TopicsController;
use App\Http\Controllers\student\SubjectController;
use App\Http\Controllers\student\TeachersController;
use App\Http\Controllers\student\StudentDashboardController;

Route::prefix('student_dashboard')->group(function () {
    Route::group(['middleware' => 'student_authorized'], function () {
        Route::resource('/home', StudentDashboardController::class);
        Route::resource('/subjects', SubjectController::class);
        Route::controller(SubjectController::class)->prefix('subjects')->as('subjects.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{subject}', 'show')->name('show');
        });

        // topic_files
        Route::controller(TopicsController::class)->prefix('topics')->as('topics.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::get('topic_files/{topic_id}', 'topic_files')->name('topic.files');
        });

        Route::get('/teacher_lessons/{teacher_id}/subject/{subject_id}', [TeachersController::class, 'teacher_lessons'])->name('teacher.lessons');
        Route::resource('/enroll',EnrollController::class);
    });
});
