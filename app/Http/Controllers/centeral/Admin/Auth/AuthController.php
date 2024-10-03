<?php

namespace App\Http\Controllers\centeral\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Requests\Central\Dashboard\Auth\LoginRequest;

class AuthController extends Controller
{
    public function loginView()
    {
        return view('centeral.admin.pages.auth.login');
    }
    public function login(LoginRequest $request)
    {

        if (
            Auth::attempt([
                'email' => $request->username,
                'password' => $request->password
            ])
        ) {
            return redirect()->route('central.admin.home');
        }
        Alert::error('خطأ', 'البريد الالكتروني او كلمة السر خاطئه');
        return back()->with(['error' => "البريد الالكتروني او كلمة السر خاطئه"]);
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();
        return redirect()->to('admin.login.view');
    }

}
