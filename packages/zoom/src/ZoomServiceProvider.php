<?php

namespace Jubaer\Zoom;

use Jubaer\Zoom\Zoom;
use Illuminate\Support\ServiceProvider;
use Jubaer\Zoom\Zoom as ZoomBase;

class ZoomServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        // Automatically apply the package configuration
        $this->app->bind(ZoomBase::class, function ($app) {
            return new Zoom();
        });
    }
}
