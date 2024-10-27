<?php

namespace App\Models;

use App\Filters\V1\QueryFilter;
use App\Traits\V1\Authorization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class Role extends Main
{
    use HasFactory, Authorization;

    protected $fillable = ['name'];

    public function users(): void
    {
        $this->belongsToMany(User::class, 'user_role');
    }

    public function permissions(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Permission::class, 'model', 'model_has_permissions');
    }

}
