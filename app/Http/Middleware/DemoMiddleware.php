<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DemoMiddleware
{
    public function handle(Request $request, Closure $next)
    {

        $exclude_uri = [
            '/login',
            '/api/student/login',
            '/api/parent/login',
            '/api/teacher/login'
        ];

        if (env('DEMO_MODE')) {
            if (! $request->isMethod('get') && ! in_array($request->getRequestUri(), $exclude_uri)) {
                return response()->json([
                    'error' => true,
                    'message' => "This is not allowed in the Demo Version.",
                    'code' => 112
                ]);
            }
        }
        return $next($request);
    }
}
