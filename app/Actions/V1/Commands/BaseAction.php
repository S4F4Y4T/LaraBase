<?php

namespace App\Actions\V1\Commands;

use App\Models\Role;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;

class BaseAction
{
    protected string $baseNamespace = 'App';

    function generateData(string $resource, string $version = '', string $layer = ''): array
    {
        // Convert resource name to PascalCase for class naming conventions
        $resource = ucfirst($resource);
        $resourceLower = strtolower($resource);
        $resourcePlural = Str::plural($resourceLower);
        $version = ucfirst($version);

        return match (strtolower($layer)) {
            'placeholders' => [
                'Resource' => $resource,
                'Version' => $version,
                'ResourceVariable' => '$'.$resourceLower,
                'ResourceLower' => $resourceLower,
                'ResourcePlural' => $resourcePlural,
                'ControllerImport' => "App\\Http\\Controllers\\Api\\{$version}\\{$resource}Controller",
                'ModelImport' => "App\\Models\\{$resource}",
                'StoreRequestImport' => "App\\Http\\Requests\\{$version}\\{$resource}\\Store{$resource}Request",
                'UpdateRequestImport' => "App\\Http\\Requests\\{$version}\\{$resource}\\Update{$resource}Request",
                'ResourceImport' => "App\\Http\\Resources\\{$version}\\{$resource}Resource",
                'FilterImport' => "App\\Filters\\{$version}\\{$resource}Filter",
            ],
            'controller' => [
                'command' => "Api/$version/{$resource}Controller",
                'path' => app_path("Http/Controllers/Api/{$version}/{$resource}Controller.php"),
                'stub' => app_path('Stubs/controller.crud.stub'),
                'placeholders' => array_merge([
                    'Namespace' => "App\\Http\\Controllers\\Api\\{$version}",
                ], $this->generateData($resource, $version, 'placeholders')),
            ],
            'migration' => [
                'command' => "create_" . $resourcePlural . "_table",
                'path' => '',
                'stub' => app_path('Stubs/migration.crud.stub'),
                'placeholders' => array_merge([
                    'table' => $resourcePlural,
                    'migrations' => "",
                ], $this->generateData($resource, $version, 'placeholders')),
            ],
            'model' => [
                'command' => $resource,
                'path' => app_path("Models/{$resource}.php"),
                'stub' => app_path('Stubs/model.crud.stub'),
                'placeholders' => array_merge([],
                    $this->generateData($resource, $version, 'placeholders')),
            ],
            'store request' => [
                'command' => "$version/$resource/Store{$resource}Request",
                'path' => app_path("Http/Requests/{$version}/{$resource}/Store{$resource}Request.php"),
                'stub' => app_path('Stubs/request.crud.stub'),
                'placeholders' => array_merge([
                    'rules' => "",
                    'Namespace' => "App\\Http\\Requests\\{$version}\\{$resource}",
                    'Class' => "Store{$resource}Request"
                ], $this->generateData($resource, $version, 'placeholders')),
            ],
            'update request' => [
                'command' => "$version/$resource/Update{$resource}Request",
                'path' => app_path("Http/Requests/{$version}/{$resource}/Update{$resource}Request.php"),
                'stub' => app_path('Stubs/request.crud.stub'),
                'placeholders' => array_merge([
                    'rules' => "",
                    'Namespace' => "App\\Http\\Requests\\{$version}\\{$resource}",
                    'Class' => "Update{$resource}Request"
                ], $this->generateData($resource, $version, 'placeholders')),
            ],
            'resource' => [
                'command' => "$version/{$resource}Resource",
                'path' => app_path("Http/Resources/{$version}/{$resource}Resource.php"),
                'stub' => app_path('Stubs/resource.crud.stub'),
                'placeholders' => array_merge([
                    'data' => "",
                    'Namespace' => "App\\Http\\Resources\\" . $version,
                ], $this->generateData($resource, $version, 'placeholders')),
            ],
            'filter' => [
                'command' => "Filters/$version/{$resource}Filter",
                'path' => app_path("Filters/{$version}/{$resource}Filter.php"),
                'stub' => app_path('Stubs/filter.crud.stub'),
                'placeholders' => array_merge([
                    'Namespace' => "App\\Filters\\{$version}"
                ], $this->generateData($resource, $version, 'placeholders')),
            ],
            'route' => [
                'path' => base_path("routes/".strtolower($version)."/".strtolower($resource).".php"),
                'stub' => app_path('Stubs/route.crud.stub'),
                'placeholders' => array_merge([], $this->generateData($resource, $version, 'placeholders')),
            ],
            default => throw new InvalidArgumentException("Invalid layer type: {$layer}"),
        };
    }

    function generateContent($meta): void
    {
        $path = $meta['path'];
        $directory = dirname($path); // Extract the directory path
        $placeholders = $meta['placeholders'];
        $stubContent = File::get($meta['stub']);

        // Ensure the directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true); // Create directory with permissions and recursive set to true
        }

        foreach ($placeholders as $placeholder => $value) {
            $stubContent = str_replace("{{ $placeholder }}", $value, $stubContent);
        }

        File::put($path, $stubContent);
    }

}
