<?php

namespace App\Actions\V1\Roles;

use App\Models\Role;

class CreateRoleAction
{
    /**
     * Create a new class instance.
     */
    public function __invoke(array $data)
    {
        $role = Role::query()->create(['name' => $data['name']]);
        $role->assignPermission($data['permissions'] ?? []);

        return $role;
    }
}
