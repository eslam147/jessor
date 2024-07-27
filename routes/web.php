<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
use App\Http\Controllers\WebhookController;

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
Auth::routes();
Route::controller(WebController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('about', 'about')->name('about.us');
    Route::get('contact', 'contact_us')->name('contact.us');
    Route::get('photo', 'photo')->name('photo');
    Route::get('photo-gallery/{id}', 'photo_details')->name('photo.gallery');
    Route::get('video', 'video')->name('video');
    Route::get('video-gallery', 'video_details')->name('video.gallery');
    Route::post('contact-us/store', 'contact_us_store')->name('contact_us.store');
});
Route::view('login', 'auth.login')->name('login');


// webhooks
Route::post('webhook/razorpay', [WebhookController::class, 'razorpay']);
Route::post('webhook/stripe', [WebhookController::class, 'stripe']);
Route::post('webhook/paystack', [WebhookController::class, 'paystack']);

Route::get('/privacy-policy', function () {
    $settings = getSettings('privacy_policy');
    return $settings['privacy_policy'] ?? '';
});

Route::get('/terms-conditions', function () {
    $settings = getSettings('terms_condition');
    return $settings['terms_condition'] ?? '';
});
