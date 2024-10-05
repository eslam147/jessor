<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\centeral\DomainController;
use App\Http\Controllers\centeral\TenantController;
use App\Http\Controllers\centeral\Admin\Auth\AuthController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->as('central.')->group(function () {
        Route::prefix('admin')->group(base_path('routes/central/admin.php'));
    // include_once base_path('routes/central/admin.php');
        Route:: as('end_user.')->group(function () {
            Route::view('/', 'centeral.landing.home')->name('home');
        });
    });
}
