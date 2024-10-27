<?php

namespace App\Actions\V1\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class MakeControllerAction extends BaseAction
{
    public function __construct(){
        $this->stubFile = app_path('Stubs/controller.crud.stub');
    }

    /**
     * Create a new class instance.
     */
    public function __invoke(
        $resource,
        $version,
        $replace
    ): void
    {
        $replace['ControllerNamespace'] = "{$this->baseNamespace}\\Http\\Controllers\\Api\\{$version}";

        Artisan::call('make:controller', [
            'name' => "Api/$version/{$resource}Controller",
        ]);

        // Locate the generated controller
        $controllerPath = app_path("Http/Controllers/Api/{$version}/{$resource}Controller.php");

        // Read the stub file
        $stubContent = File::get($this->stubFile);

        // Perform replacements
        foreach ($replace as $placeholder => $value) {
            $stubContent = str_replace("{{ $placeholder }}", $value, $stubContent);
        }
        // Add more replacements as needed

        // Write the modified content to the controller
        File::put($controllerPath, $stubContent);

    }
}
