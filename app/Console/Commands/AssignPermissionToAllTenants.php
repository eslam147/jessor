<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Facades\Tenancy;
use Spatie\Permission\Models\Permission;

class AssignPermissionToAllTenants extends Command
{
    protected $signature = 'tenants:assign-permission {permission} {role}';
    protected $description = 'Assign a specific permission to all tenants';

    public function handle()
    {
        $permissionName = $this->argument('permission');
        $roleName = $this->argument('role');
        // ----------------------------------------------- \\
        $tenants = Tenant::get();
        // ----------------------------------------------- \\
        foreach ($tenants as $tenant) {
            Tenancy::initialize($tenant);
            $roleModel = Role::where('name', $roleName)->first();
            if ($roleModel) {
                $Permission = Permission::firstOrCreate(['name' => $permissionName]);
                if (! $roleModel->hasPermissionTo($Permission)) {
                    $roleModel->givePermissionTo($Permission);
                }
                $this->info("Permission assigned to `{$tenant->id}`.");
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
