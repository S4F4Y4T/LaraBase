<?php

namespace App\Actions\V1\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class MakePolicyAction extends BaseAction
{
    public function __construct(){}

    /**
     * Create a new class instance.
     */
    public function __invoke(
        $resource,
        $version,
        $replace
    ): string
    {
        Artisan::call('make:policy', [
            'name' => "$version/{$resource}Policy",
        ]);
        return "App\\Http\\Policies\\{$version}\\{$resource}Policy";

    }
}
