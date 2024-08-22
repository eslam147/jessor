<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AssignPermissionsToSuperAdminSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'coupons-list',
            'coupons-create',
            'coupons-edit',
            'coupons-delete',
            /// -------------------------------
            'enrollments-list',
            'enrollments-edit',
            'enrollments-create',
            'enrollments-delete',
            /// -------------------------------
            'manage-online-exam',
            'wallet-show',
            /// -------------------------------
            
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $super_admin_role = Role::where('name', 'Super Admin')->first();
        $super_admin_role->givePermissionTo($permissions);
    }
}
