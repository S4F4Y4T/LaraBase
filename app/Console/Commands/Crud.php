<?php

namespace App\Console\Commands;

use App\Actions\V1\Commands\MakeControllerAction;
use App\Actions\V1\Commands\MakeFilterAction;
use App\Actions\V1\Commands\MakeMigrationAction;
use App\Actions\V1\Commands\MakeModelAction;
use App\Actions\V1\Commands\MakePolicyAction;
use App\Actions\V1\Commands\MakeRequestAction;
use App\Actions\V1\Commands\MakeResourceAction;
use App\Actions\V1\Commands\MakeRouteAction;
use App\Actions\V1\Commands\MakeServiceAction;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

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
     * @throws Exception
     */

    public function handle(
        MakeControllerAction $controllerAction,
        MakeMigrationAction $migrationAction,
        MakeModelAction $modelAction,
        MakeRequestAction $requestAction,
        MakeResourceAction $resourceAction,
        MakeFilterAction $filterAction,
        MakeRouteAction $routeAction
    ): int {
        $resource = $this->argument('resource');
        $version = $this->argument('v') ?: 'V1';

        if (!$this->confirm("Confirm generation of API resource: $resource for version $version?")) {
            $this->warn('Operation cancelled by the user.');
            return 1;
        }

        $this->info("Provide data info");
        $data = $this->collectColumnData();
        $bar = $this->output->createProgressBar(8);

        // Define tasks with action callbacks
        $actions = [
            'Generating Migration...' => fn() => $migrationAction($resource, $version, $data),
            'Generating Model...' => fn() => $modelAction($resource),
            'Generating Request Validation...' => fn() => [
                $requestAction($resource, $version, $data),        // For creation request
                $requestAction($resource, $version, $data, 'update') // For update request
            ],
            'Generating Resource...' => fn() => $resourceAction($resource, $version, $data),
            'Generating Filter...' => fn() => $filterAction($resource, $version),
            'Generating Controller...' => fn() => $controllerAction($resource, $version),
            'Generating Route...' => fn() => $routeAction($resource, $version),
        ];

        foreach ($actions as $message => $action) {
            $this->progress($bar, $message);
            try {
                $action();
            } catch (\Exception $e) {
                $this->error("Failed to execute action: $message. Error: " . $e->getMessage());
                info($e);
                return 1;
            }
        }

        $this->progress($bar, 'Finalizing...');
        $bar->finish();
        $this->info("\nAll components for $resource have been successfully created!");

        return 0;
    }

    private function progress($bar, $message): void
    {
        $this->info($message);
        $bar->advance();
        echo PHP_EOL; // Add a new line after each progress bar step
        usleep(100000); // Optional delay for visualization
    }

    private function collectColumnData(): array {
        $data = [];
        $typeOptions = [
            'string', 'integer', 'text', 'boolean', 'date'
        ];
        $ruleOptions = ['required', 'nullable', 'string', 'integer', 'max:255', 'email'];


        while ($this->confirm('Add a column?')) {
            $column = $this->ask('Input column name') ?? '';

            if (empty($column)) continue;

            $data[$column] = [
                'type' => $this->choice('Choose column type?', $typeOptions, 0),
                'default' => $this->ask('Input default value'),
                'validation' => $this->choice('Choose validation rules?', $ruleOptions, 0, null, true),
            ];
        }

        return $data;
    }

}
