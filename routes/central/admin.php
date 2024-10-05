<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\centeral\DomainController;
use App\Http\Controllers\centeral\TenantController;
use App\Http\Controllers\centeral\Admin\HomeController;
use App\Http\Controllers\centeral\Admin\ProfileController;
use App\Http\Controllers\centeral\Admin\Auth\AuthController;
use App\Http\Controllers\centeral\Admin\Setting\SettingController;


Route::middleware('central.auth')->group(function () {
    Route::get('/', HomeController::class)->name('admin.home');
    Route::controller(SettingController::class)->group(function () {
        Route::group(['prefix' => 'settings/', 'as' => 'setting.'], function () {
            /*----------------------------------------------*/
            Route::get('general', 'general')->name('general');
            Route::put('general', 'generalUpdate')->name('general.update');
        });
    });
    Route::controller(ProfileController::class)->group(function () {
        Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
            Route::get('/', 'index')->name('index');
            Route::put('updateInfo', 'updateInfo')->name('admin-update-info');
            Route::put('updateImage', 'updateImage')->name('admin-update-image');
            Route::put('updatePassword', 'updatePassword')->name('admin-update-password');
        });
    });
    Route::resource('tenants', TenantController::class);
    // Route::resource('tenants', DomainController::class);
    // Route::get('settings', [TenantController::class, 'upgrade_settings']);
    // Route::post('settings', [TenantController::class, 'insert_settings_fields'])->name('insert.settings.field');

});
Route::prefix('auth')->controller(AuthController::class)->middleware('guest')->group(function () {
    Route::get('login', 'loginView')->name('login.view');
    Route::post('login', 'login')->name('login');
    Route::post('logout', 'logout')->name('logout');
});