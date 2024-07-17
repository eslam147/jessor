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
Route::get('/', [WebController::class,'index']);
Route::get('about',[WebController::class,'about'])->name('about.us');
Route::get('contact',[WebController::class, 'contact_us'])->name('contact.us');
Route::get('photo',[WebController::class, 'photo'])->name('photo');
Route::get('photo-gallery/{id}',[WebController::class, 'photo_details'])->name('photo.gallery');
Route::get('video',[WebController::class, 'video'])->name('video');
Route::get('video-gallery',[WebController::class, 'video_details'])->name('video.gallery');
Route::post('contact-us/store',[WebController::class,'contact_us_store'])->name('contact_us.store');
Route::view('login', 'auth.login')->name('login');


// webhooks
Route::post('webhook/razorpay', [WebhookController::class, 'razorpay']);
Route::post('webhook/stripe', [WebhookController::class, 'stripe']);
Route::post('webhook/paystack',[WebhookController::class,'paystack']);

Route::get('/privacy-policy', function () {
    $settings = getSettings('privacy_policy');
    return $settings['privacy_policy'];
});

Route::get('/terms-conditions', function(){
    $settings = getSettings('terms_condition');
    return $settings['terms_condition'];
});
