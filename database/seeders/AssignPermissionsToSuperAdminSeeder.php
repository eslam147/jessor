<?php

namespace Database\Seeders;

use App\Services\Permission\PermissionService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AssignPermissionsToSuperAdminSeeder extends Seeder
{
    public function __construct(
        private readonly PermissionService $permissionService
    ) {
    }
    public function run()
    {
        $superAdminPermission = [
            'coupons-list',
            'coupons-create',
            'coupons-edit',
            'coupons-delete',
            /// ------------------------------- \\ 
            'enrollments-list',
            'enrollments-edit',
            'enrollments-create',
            'enrollments-delete',
            /// ------------------------------- \\
            'manage-online-exam',
            'wallet-show',
            'meeting-provider-settings',
            'meeting-provider-settings-update',
            'user-devices-list',
            "student-list-deleted",
            'user-devices-delete'
            /// ------------------------------- \\
        ];
        $studentPermissions = [
        ];
        $teacherPermissions = [
            'live_lesson-list',
        ];
        $permissions = array_merge($superAdminPermission, $studentPermissions, $teacherPermissions);
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->assignPermission("Super Admin", $superAdminPermission);
        $this->assignPermission("Student", $studentPermissions);
        $this->assignPermission("Teacher", $teacherPermissions);
    }
    private function assignPermission($roleName, $permissions)
    {
        $role = $this->permissionService->findRole($roleName);
        $this->permissionService->assignPermissionToRole($permissions, $role);
    }
}
