<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Middleware\InitializeSchool;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\student\SignupController;
use App\Http\Controllers\centeral\DomainController;
use App\Http\Controllers\centeral\TenantController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
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
        Route::get('clear-cache',function(){
            $results = [];
            Artisan::call('cache:clear');
            $results[] = "Cache cleared: " . Artisan::output();
            Artisan::call('config:clear');
            $results[] = "Config cache cleared: " . Artisan::output();
            Artisan::call('route:clear');
            $results[] = "Route cache cleared: " . Artisan::output();
            Artisan::call('view:clear');
            $results[] = "View cache cleared: " . Artisan::output(); 
            return implode('<br>', $results);
        });
    });
}
