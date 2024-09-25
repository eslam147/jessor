<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use App\Services\UserDeviceHistory\UserDeviceHistoryService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                if ($user->hasRole('Student')) {
                    if(app(UserDeviceHistoryService::class)->checkSessionCookiesIsValid()){
                        return to_route('home.index');
                    }else{
                        app(UserDeviceHistoryService::class)->storeUserLogoutHistory($user);
                        return to_route('login.view');
                    }
                } elseif($user->hasRole(['Super Admin', 'Teacher'])) {
                    return to_route('home');
                }else{
                    return abort(404);
                }

            }
        }

        return $next($request);
    }
}
