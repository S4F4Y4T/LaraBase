<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Main
{
    use HasFactory;

    public function users()
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_permissions');
    }


    public function roles()
    {
        return $this->morphedByMany(Role::class, 'model', 'model_has_permissions');
    }
}
