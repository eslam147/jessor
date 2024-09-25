<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Services\Auth\LoginService;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Services\UserDeviceHistory\UserDeviceHistoryService;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = RouteServiceProvider::HOME;
    // protected $maxAttempts = 10; // Default is 5
    // protected $decayMinutes = 120; // Default is 1

    public function __construct(
        private readonly LoginService $loginService,
        private readonly UserDeviceHistoryService $userDeviceHistoryService
    ) {
        $this->middleware('guest')->except('logout');
    }
    protected function authenticated(Request $request, $user)
    {
        if ($user->hasRole('Student')) {
            $loginDevice = $this->loginService->handleDeviceLimit($user);
            return redirect()->intended(route('home.index'))->withCookie(
                cookie()->forever('device_token', $loginDevice['token'])
            );
        } elseif ($user->hasRole(['Super Admin', 'Teacher'])) {
            return redirect()->intended(route('home'));
        }

        return abort(404);
    }
    public function logout(Request $request)
    {
        $this->userDeviceHistoryService->storeUserLogoutHistory(auth()->user());

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return to_route('login.view');
    }
}
