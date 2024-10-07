<?php

namespace App\Services\Permission;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission as PermissionModel;

class PermissionService
{
    public function __construct(
        private readonly Role $roleModel,
        private readonly PermissionModel $permissionModel
    ) {
    }

    public function getPermissions(): Collection
    {
        return $this->permissionModel->get();
    }

    public function getRoles(): Collection
    {
        return $this->roleModel->get();
    }
    public function findRole($roleName)
    {
        return $this->roleModel->where('name', $roleName)->first();
    }
    public function assignPermissionToRole($permission, Role $role): Role
    {
        $role->givePermissionTo($permission);
        $role->load('permissions');
        return $role;
    }
}