<?php

namespace App\Actions\V1\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class MakeModelAction extends BaseAction
{
    /**
     * Create a new class instance.
     */
    public function __invoke(
        $resource
    ): void
    {
        $meta = $this->generateData($resource, layer: 'model');

        Artisan::call('make:model', [
            'name' => $meta['command'],
        ]);

        $this->generateContent($meta);
    }
}
