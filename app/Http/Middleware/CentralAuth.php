<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CentralAuth
{
    public function handle(Request $request, Closure $next)
    {
        if(!auth()->check()){
            return to_route('central.login.view');
        }
        return $next($request);
    }
}
