<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\centeral\DomainController;
use App\Http\Controllers\centeral\TenantController;
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
    Route::domain($domain)->group(function () {
        Route::get('/', function () {
            return view('centeral.landing.home');
        })->name('centeral.home');
        Route::resource('domain', DomainController::class);
        Route::resource('tenants',TenantController::class);
        Route::get('settings',[TenantController::class,'upgrade_settings']);
        Route::post('settings',[TenantController::class,'insert_settings_fields'])->name('insert.settings.field');
    });
}
