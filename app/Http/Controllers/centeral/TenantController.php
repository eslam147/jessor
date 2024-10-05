<?php

namespace App\Http\Controllers\centeral;

use Exception;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Facades\Tenancy;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use RealRashid\SweetAlert\Facades\Alert;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::get();

        return view('centeral.admin.pages.tenants.index', compact('tenants'));
    }

    public function create()
    {
        
        $getDateFormat = getDateFormat();
        $getTimezoneList = getTimezoneList();
        $getTimeFormat = getTimeFormat();

        return view('centeral.admin.pages.tenants.create',compact('getDateFormat','getTimezoneList','getTimeFormat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subdomain' => "required|unique:tenants,id|min:4|max:50|regex:/^[a-zA-Z0-9]+$/",
            'company_name' => "required|string",
            "company_address" => "required|string",
            "company_phone" => "required|string",
            "timezone" => "required|string",
            "protection_browser" => "required|string",

        ]);

        try {
            // php artisan tenants:run email:send --tenants=8075a580-1cb8-11e9-8822-49c5d8f8ff23 --option="queue=1" --option="subject=New Feature" --argument="body=We have launched a new feature. ..."
            DB::beginTransaction();
            // $tenant = ;
            if (Tenant::find($request->subdomain)) {
                Alert::warning('Warning', 'This domain already exists');
                return redirect()->back();
            }

            $tenant = Tenant::create(['id' => $request->subdomain]);
            $tenant->domains()->create(['domain' => $request->subdomain . '.localhost']);

            Artisan::call('tenants:migrate-fresh', [
                '--tenants' => $request->subdomain
            ]);
            // tenants:seed
            Artisan::call('tenants:seed', [
                '--tenants' => $request->subdomain
            ]);

            Artisan::call('tenants:run', [
                'commandname' => 'school_settings:update',
                '--tenants' => [$tenant->id],
                '--option' => ['queue=1', 'subject=New Feature'],
                '--argument' => [
                    "name={$request->company_name}",
                    "address={$request->company_address}",
                    "phone={$request->company_phone}",
                    "timezone={$request->timezone}",
                ]
            ]);



            DB::commit();

            Alert::success('Congratulations', 'Tenant Created Successfully');
            return redirect()->back();
        } catch (Exception $e) {
            report($e);
            DB::rollBack();
            Alert::error('Error', 'An error occurred: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
    public function upgrade_settings()
    {
        return view('centeral.tenants.settings');

    }
    public function insert_settings_fields(Request $request)
    {
        // Fetch all tenants from the central database
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            // Initialize the tenant context
            Tenancy::initialize($tenant);

            // Check if the record already exists
            $exists = DB::table('settings')
                ->where('type', $request->type)
                // Add other columns as needed
                ->exists();

            // If the record does not exist, insert it
            if (! $exists) {
                DB::table('settings')->insert([
                    'type' => $request->type,
                    'message' => $request->message,
                    // Add other columns as needed
                ]);
            }

            // End the tenancy context
            Tenancy::end();
        }
    }
}
