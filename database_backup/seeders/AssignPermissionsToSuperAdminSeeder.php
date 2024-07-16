<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AssignPermissionsToSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::firstOrCreate([
            'name' => 'coupons-list',
        ]);
        Permission::firstOrCreate([
            'name' => 'coupons-create',
        ]);
        Permission::firstOrCreate([
            'name' => 'coupons-edit',
        ]);
        Permission::firstOrCreate([
            'name' => 'coupons-delete',
        ]);
//         coupon-list
// coupon-create
// coupon-edit
// coupon-delete
        $super_admin_role = Role::where('name', 'Super Admin')->first();
        $super_admin_role->givePermissionTo('coupons-list', 'coupons-create', 'coupons-edit', 'coupons-delete');
    }
}
