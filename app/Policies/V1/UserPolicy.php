<?php

namespace App\Policies\V1;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function create(User $user): true
    {
        return true;
    }
}
