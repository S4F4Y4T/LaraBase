<?php

namespace App\Actions\V1\Commands;

use App\Models\Role;
use Illuminate\Support\Facades\Artisan;

class BaseAction
{
    protected string $baseNamespace = 'App';
}
