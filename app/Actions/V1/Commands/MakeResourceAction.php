<?php

namespace App\Actions\V1\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class MakeResourceAction extends BaseAction
{
    public function __construct(){}

    /**
     * Create a new class instance.
     */
    public function __invoke(
        $resource,
        $version,
        $data,
    ): void
    {
        $meta = $this->generateData($resource, $version, 'resource');

        Artisan::call('make:resource', [
            'name' => $meta['command'],
        ]);

        $meta['placeholders']['data'] = $this->BuildData($data);
        $this->generateContent($meta);
    }

    private function BuildData($data): string
    {
        $resourceData = '';
        foreach ($data ?? [] as $key => $value) {
            $resourceData .= "'{$key}' => \$this->{$key},";
            $resourceData .= "\n            ";
        }

        return $resourceData;
    }
}
