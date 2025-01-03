<?php

namespace App\Actions\V1\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class MakeControllerAction extends BaseAction
{
    /**
     * Create a new class instance.
     */
    public function __invoke(
        $resource,
        $version
    ): void
    {
        $meta = $this->generateData($resource, $version, 'controller');

        Artisan::call('make:controller', [
            'name' => $meta['command'],
        ]);

        $this->generateContent($meta);


    }
}
