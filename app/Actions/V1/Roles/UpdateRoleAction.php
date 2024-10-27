<?php

namespace App\Actions\V1\Roles;

class UpdateRoleAction
{
    public function __invoke($role, array $data)
    {
        $role = tap($role)->update(['name' => $data['name']]);

        if(!empty($data['permissions'])){
            $role->assignPermission($data['permissions']);
        }

        return $role;
    }
}
