<?php

namespace App\Http\Controllers\centeral;

use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DomainController extends Controller
{
    public function store(Request $request)
    {
        $tanent = Tenant::create(['id' => $request->subdomain]);
        $tanent->domains()->create(['domain' => $request->domain .'.localhost']);
    }
}
