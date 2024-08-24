<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle user authenticated event.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Set your maximum device limit here
        $maxDevices = settingByType('device_limit');

        // Count the active devices for the user
        $agent = md5($_SERVER['HTTP_USER_AGENT']);
        $deviceExists  = $user->devices()->where('device_agent', $agent)->get();

        // if($user->hasRole('Student')){
        //     if ($deviceExists->count() >= $maxDevices) {
        //         // Optionally log out the user or prevent new device logins
        //         Auth::logout();

        //         return redirect()->route('login')->withErrors([
        //             'error' => 'لقد تجاوزت الحد الأقصي من الأجهزه المسموحه'
        //         ]);
        //     }else{
        //         // Store the current device information
        //         $user->devices()->create([
        //             'device_name' => $_SERVER['HTTP_USER_AGENT'],
        //             'device_ip' => $request->ip(),
        //             'device_agent' => $agent
        //         ]);
        //     }
        // }


        // Redirect based on role
        if ($user->hasRole('Student')) {
            return redirect()->intended(route('home.index'));
        } else {
            return redirect()->intended(route('home'));
        }
    }
}
