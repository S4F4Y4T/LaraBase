<?php

namespace App\Actions\V1\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class MakeRouteAction extends BaseAction
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
        $meta = $this->generateData($resource, $version, 'route');
        $this->generateContent($meta);

        $versionDirectory = base_path("routes/" . strtolower($version));
        $routeFile = $versionDirectory . "/api.php";

        // Ensure the directory for the version exists
        if (!File::exists($versionDirectory)) {
            File::makeDirectory($versionDirectory, 0755, true);
        }

        // Ensure the route file exists or create it if necessary
        if (!File::exists($routeFile)) {
            File::put($routeFile, "<?php\n\n"); // Initialize with PHP opening tag
        }

        // Append the require statement for the resource's route file
        File::append($routeFile, "\nrequire __DIR__ . '/" . strtolower($resource) . ".php';");

    }
}
