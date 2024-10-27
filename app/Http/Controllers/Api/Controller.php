<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Gate;

abstract class Controller
{

    public function isAuthorized($ability, $model): void
    {
        Gate::authorize($ability, $model);
    }
}
