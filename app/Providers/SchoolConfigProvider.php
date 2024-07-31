<?php

namespace App\Providers;

use App\Models\Settings;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Events\TenancyInitialized;

class SchoolConfigProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Event::listen(TenancyInitialized::class, function (){

         
        // });

      
    }
}
