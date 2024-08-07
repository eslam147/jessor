<?php

namespace App\Http\Controllers\centeral;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Facades\Tenancy;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use RealRashid\SweetAlert\Facades\Alert;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $migrateOutput = '';
        return view('centeral.tenants.index',compact('migrateOutput'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        dd('tenant create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $migrateStatus = Artisan::call('tenants:migrate');
        $migrateOutput = Artisan::output();
        return view('centeral.tenants.index',compact('migrateOutput'));
    }

    public function upgrade_settings(){

        return view('centeral.tenants.settings');

    }
    public function insert_settings_fields(){
        // Fetch all tenants from the central database
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // Initialize the tenant context
            Tenancy::initialize($tenant);

            // Insert the new row into the settings table
            DB::table('settings')->insert([
                'type' => 'custom_browser',
                'message' => 'enabled',
                // Add other columns as needed
            ]);

            // End the tenancy context
            Tenancy::end();
        }

    }

    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
