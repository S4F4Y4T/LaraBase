<?php

namespace App\Policies\V1;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class RolePolicy
{
    /**
     * @throws AuthorizationException
     */
    public function all(User $user): bool
    {
        return $user->can('role:show');
    }

    public function show(User $user, $role): bool
    {
        return $user->can('role:show') || $user->hasRole($role->name);
    }

    public function create(User $user): bool
    {
        return $user->can('role:create');
    }

    public function update(User $user, $role): bool
    {
        return $user->can('role:update');
    }

    public function delete(User $user, $role): bool
    {
        return $user->can('role:delete');
    }
}
