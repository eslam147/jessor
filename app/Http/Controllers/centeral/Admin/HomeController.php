<?php

namespace App\Http\Controllers\centeral\Admin;


use App\Models\Tenant;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function __invoke()
    {
        $tenantsCount = Tenant::count();
        return view('centeral.admin.pages.home', compact('tenantsCount'));
    }
}
