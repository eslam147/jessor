<?php

namespace App\Http\Controllers\centeral;

use Exception;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Stancl\Tenancy\Commands\Rollback;
use Illuminate\Support\Facades\Artisan;
use RealRashid\SweetAlert\Facades\Alert;

class DomainController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'subdomain' => "required|unique:tenants,id|min:4|max:50|regex:/^[a-zA-Z0-9]+$/"
        ]);
        DB::beginTransaction();

        try {
            $tenant = Tenant::find($request->subdomain);
            if ($tenant) {
                Alert::warning('Warning', 'This domain already exists');
                return redirect()->back();
            }

            $tenant = Tenant::create(['id' => $request->subdomain]);
            $tenant->domains()->create(['domain' => $request->subdomain . '.localhost']);

            Artisan::call('tenants:migrate-fresh', [
                '--tenants' => $request->subdomain
            ]);
            Artisan::call('tenants:seed', [
                '--tenants' => $request->subdomain
            ]);

            DB::commit();

            Alert::success('Congratulations', 'Tenant Created Successfully');
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            Alert::error('Error', 'An error occurred: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

}
