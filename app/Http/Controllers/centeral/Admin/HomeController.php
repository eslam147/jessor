<?php

namespace App\Http\Controllers\centeral\Admin\Auth;


use App\Http\Controllers\Controller;
use App\Http\Interfaces\Admin\HomeContract;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('centeral.admin.pages.home');
    }
}
