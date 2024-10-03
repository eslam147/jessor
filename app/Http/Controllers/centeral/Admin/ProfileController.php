<?php

namespace App\Http\Controllers\centeral\Admin;


use App\Http\Traits\ImageTrait;
use App\Http\Controllers\Controller;

use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Requests\Profile\{
    UpdateInfoRequest,
    UpdateImageRequest,
    UpdatePasswordRequest
};
class ProfileController extends Controller
{
    // use ImageTrait;

    public function index()
    {
        return view('centeral.admin.pages.profile.index');
    }

    public function updateInfo(UpdateInfoRequest $request)
    {
        auth()->user()->update([
            'name'  => $request->name,
            'email'       => $request->email,
        ]);
        Alert::success('تم تحديث بياناتك بنجاح');
        return back();
    }

    public function updateImage(UpdateImageRequest $request)
    {
        $admin = auth()->user();

        if($request->hasFile('image')){
            $admin->addMediaFromRequest('image')->toMediaCollection('avatars');
        }

        Alert::success('تم تحديث الصوره الشخصيه بنجاح');
        return back();
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        auth('admin')->user()->update([
            'password'  => bcrypt($request->new_password),
        ]);
        Alert::success('تم تحديث الباسورد بنجاح');
        return back();
    }
}
