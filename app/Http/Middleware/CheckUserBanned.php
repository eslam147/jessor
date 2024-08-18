<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;

use Cog\Contracts\Ban\Bannable as BannableContract;
use Illuminate\Contracts\Auth\StatefulGuard as StatefulGuardContract;
class CheckUserBanned
{
    /**
     * The Guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * @param \Illuminate\Contracts\Auth\Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        $user = $this->auth->user();

        if ($user && $user instanceof BannableContract && $user->isBanned()) {

            if ($this->auth instanceof StatefulGuardContract) {
                $this->auth->logout();
            }

            if ($user->isBanned()) {
                return response()->json([
                    'error' => true,
                    'message' => "Your account has been banned.",
                    'code' => 105
                ]);
            }
        }

        return $next($request);
    }
}
