<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
//        $this->renderable(function (NotFoundHttpException $e, $request) {
//            if ($request->is('api/*')) {
//                return response()->json([
//                    'message' => 'Record not found.'
//                ], 404);
//            }
//        });
//        $this->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
//            if ($request->is('api/*')) {
//                return response()->json([
//                    'message' => 'Not authenticated'
//                ], 401);
//            }
//        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
        Schema::defaultStringLength(191);
    }
}
