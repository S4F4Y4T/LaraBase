<?php

namespace App\Services\V1;

use App\Models\User;

class UserService
{
    public function create(array $data, string $role){
        $user = User::query()->create($data);
        $user->assignRoles($role);
        return $user;
    }

    public function update(array $data){
        return User::query()->update($data);
    }
}
