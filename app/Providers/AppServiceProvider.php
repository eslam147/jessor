<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            return url(route('auth.password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        });
        // --------------------------------------- \\
        view()->share('static_site_logo', asset("assets/logo.svg"));
        view()->share('static_site_name', 'Jessor');
        // --------------------------------------- \\
        Schema::defaultStringLength(191);
    }
}
