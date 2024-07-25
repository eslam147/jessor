<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccountType
{
    public function handle(Request $request, Closure $next)
    {
        if(is_null(Auth::user()->is_student)){
            return $next($request);
        }else{
            return redirect()->route('home.index');
        }
    }
}
