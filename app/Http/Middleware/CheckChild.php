<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckChild
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $user = $request->user();
        $children = $user->parent->children()->where('id',$request->child_id)->first();
        if(empty($children)){
            return response()->json([
                'error' => true,
                'message' => "Invalid Child ID Passed.",
                'code'=> 105,
            ]);
        }
        return $next($request);
    }
}
