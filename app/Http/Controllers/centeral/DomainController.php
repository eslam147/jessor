<?php

namespace App\Http\Controllers\centeral;

use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use RealRashid\SweetAlert\Facades\Alert;

class DomainController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'subdomain' => "required|unique:tenants|min:4|max:50|regex:^[a-zA-Z0-9]+$"
        ]);
        $tenant= Tenant::find($request->subdomain);
        if($tenant){
            Alert::warning('Warning','this domain already exist');
            return redirect()->back();
        }else{
            $tanent = Tenant::create(['id' => $request->subdomain]);
            $tanent->domains()->create(['domain' => $request->subdomain .'.localhost']);

            // remove old dummy data
            Artisan::call('tenants:migrate-fresh', [
                '--tenants' => $request->subdomain
            ]);
            // add defult data from seed
            Artisan::call('tenants:seed', [
                '--tenants' => $request->subdomain
            ]);
            Alert::success('Congratulations','Tenant Created Successfully');
            return redirect()->back();
        }
    }
}
