<?php

namespace App\Http\Middleware;

use App\Models\Students;
use App\Models\Teacher;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user() && (Auth::user()->hasRole(['Super Admin', 'Teacher']))) {
            return $next($request);
        }
        return to_route('login.view');
    }
}
