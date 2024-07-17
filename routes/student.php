<?php

use App\Http\Controllers\student\StudentDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::prefix('student_dashboard')->group(function(){
    Route::group(['middleware' => 'auth'], function () {
        Route::resource('/', StudentDashboardController::class);
    });
});
