<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StudentHaveAccessToLesson
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()->hasAccessToLesson($request->lesson_id)) {
            return response()->json([
                'error' => true,
                'message' => "You don't have access to this lesson.",
                'code' => 105
            ]);
        }
        return $next($request);
    }
}
