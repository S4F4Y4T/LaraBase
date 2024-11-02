<?php

namespace App\Actions\V1\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class MakeFilterAction extends BaseAction
{
    public function __construct(){}

    /**
     * Create a new class instance.
     */
    public function __invoke(
        $resource,
        $version
    ): void
    {
        $meta = $this->generateData($resource, $version, 'filter');

        Artisan::call('make:class', [
            'name' => $meta['command'],
        ]);

        $this->generateContent($meta);
    }
}
