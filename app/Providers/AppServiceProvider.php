<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Events\TenancyInitialized;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
        if (config('app.env') == 'production') {
            URL::forceScheme('https');
        }
        Schema::defaultStringLength(191);
    }
}
