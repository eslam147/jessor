<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Facades\Tenancy;
use Spatie\Permission\Models\Permission;

class AssignPermissionToAllTenants extends Command
{
    // php artisan tenants:assign-permission --role="Teacher" --permission="live-lesson-update"


    // ---------------------------------------- 
    // php artisan tenants:assign-permission --role="Teacher" --permission="live_lesson-list"
    // php artisan tenants:assign-permission --role="Super Admin" --permission="meeting-provider-settings"
    // php artisan tenants:assign-permission --role="Super Admin" --permission="meeting-provider-settings-update"
    // php artisan tenants:assign-permission --role="Super Admin" --permission="user-devices-list"
    // php artisan tenants:assign-permission --role="Super Admin" --permission="user-devices-delete"
    // ---------------------------------------- 

    protected $signature = 'tenants:assign-permission {--role=} {--permission=}';
    protected $description = 'Assign a specific permission to all tenants';

    public function handle()
    {
        $permissionName = $this->option('permission');
        $roleName = $this->option('role');
        // ----------------------------------------------- \\
        $tenants = Tenant::get();
        // ----------------------------------------------- \\
        foreach ($tenants as $tenant) {
            Tenancy::initialize($tenant);
            $roleModel = Role::where('name', $roleName)->first();

            if ($roleModel) {
                $permissionModel = Permission::firstOrCreate(['name' => $permissionName]);
                if (! $roleModel->hasPermissionTo($permissionModel)) {
                    $roleModel->givePermissionTo($permissionModel);
                    $this->info("Permission assigned to `{$tenant->id}`.");
                }else{
                    $this->warn("The Permission assigned Before to `{$tenant->id}`.");
                }
            } else {
                $this->error("Role `{$roleName}` not found for `{$tenant->id}`.");
            }
            Tenancy::end();
        }
        // ----------------------------------------------- \\
        $this->info("Permission '{$permissionName}' assigned to role '{$roleName}' for all tenants.");
        // ----------------------------------------------- \\
    }
}
