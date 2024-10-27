<?php

namespace App\Console\Commands;

use App\Actions\V1\Commands\MakeControllerAction;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Artisan;

class Crud extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {resource : Resource name for the crud operation} {v? : The version of the resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make crud operations faster';

    /**
     * Execute the console command.
     */
    public function handle(
        MakeControllerAction $controllerAction
    ): int
    {
        $resource = ucfirst($this->argument('resource'));
        $resourceLower = strtolower($resource);
        $version  = ucfirst($this->argument('v') ? $this->argument('v') : 'V1');
        $baseNamespace = 'App';
        $replace = [
            'Resource' => $resource,
            'ResourceVariable' => '$'.$resourceLower,
        ];

        if (!$this->confirm('Confirm generation of API resource: ' . $resource . ' for version ' . $version . '?')) {
            $this->warn('Operation cancelled by the user.');
            return 0;
        }

        $this->info("Generating Migration...");
        Artisan::call('make:migration', [
            'name' => "create_". $resourceLower ."_table",
        ]);

        //Model
        $this->info("Generating Model...");
        Artisan::call('make:model', [
            'name' => $resource,
            '--factory' => true,
            '--seed' => true,
        ]);
        $replace['ModelNamespace'] = "{$baseNamespace}\\Models\\{$resource}";

        //request validation
        $this->info("Generating Request Validation...");
        Artisan::call('make:request', [
            'name' => "$version/$resource/Store{$resource}Request",
            '--force' => true
        ]);
        Artisan::call('make:request', [
            'name' => "$version/$resource/Update{$resource}Request",
            '--force' => true
        ]);
        $replace['StoreRequestNamespace'] = "{$baseNamespace}\\Http\\Requests\\{$version}\\{$resource}\\Store{$resource}Request";
        $replace['UpdateRequestNamespace'] = "{$baseNamespace}\\Http\\Requests\\{$version}\\{$resource}\\Update{$resource}Request";

        //resource
        $this->info("Generating Resource...");
        Artisan::call('make:resource', [
            'name' => "$version/{$resource}Resource",
        ]);
        $replace['ResourceNamespace'] = "{$baseNamespace}\\Http\\Resources\\{$version}\\{$resource}Resource";


        //policy
        $this->info("Generating Policy...");
        Artisan::call('make:policy', [
            'name' => "$version/{$resource}Policy",
        ]);

        //filter
        $this->info("Generating Filter...");
        Artisan::call('make:class', [
            'name' => "Filters/$version/{$resource}Filter",
        ]);
        $replace['FilterNamespace'] = "{$baseNamespace}\\Filters\\{$version}\\{$resource}Filter";

        //service
        $this->info("Generating Service...");
        Artisan::call('make:class', [
            'name' => "Services/$version/{$resource}Service",
        ]);
        $replace['ServiceNamespace'] = "{$baseNamespace}\\Services\\{$version}\\$resource}Service";

        //controller
        $this->info("Generating Controller...");
        $controllerAction($resource, $version, $replace);

        $this->info("All components for $resource have been successfully created!");

        return 0;
    }
}
