<?php

namespace App\Actions\V1\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class MakeRequestAction extends BaseAction
{
    public function __construct(){}

    /**
     * Create a new class instance.
     */
    public function __invoke(
        $resource,
        $version,
        $data,
        $type = 'store'
    ): void
    {
        $operation = $type === 'store' ? 'Store' : 'Update';
        $meta = $this->generateData($resource, $version, "{$type} request");

        Artisan::call('make:request', [
            'name' => $meta['command']
        ]);

        $data = $this->BuildData($data);
        $meta['placeholders']['rules'] = $data;
        $this->generateContent($meta);
    }

    private function BuildData(array $data): string
    {
        $rules = '';
        foreach ($data ?? [] as $key => $value)
        {
            if(!empty($value['validation'])){

                $rules .= "'".$key."' => '". implode('|', $value['validation']) ."',";

                $rules .= "\n            ";
            }
        }

        return $rules;
    }
}
