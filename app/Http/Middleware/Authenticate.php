<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use App\Services\UserDeviceHistory\UserDeviceHistoryService;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {

            return route('login.view');
        }
    }
    public function handle($request, Closure $next, ...$guards)
    {
        if (app(UserDeviceHistoryService::class)->checkSessionCookiesIsValid()) {
            return parent::handle($request, $next, ...$guards);
        } else {
            app(UserDeviceHistoryService::class)->storeUserLogoutHistory(auth()->user()->id);
            return to_route('login.view');
        }
    }
}
