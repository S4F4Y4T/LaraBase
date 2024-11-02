<?php

namespace App\Actions\V1\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class MakeMigrationAction extends BaseAction
{

    /**
     * Create a new class instance.
     */
    public function __invoke(
        $resource,
        $version,
        $data
    ): void {

        $meta = $this->generateData($resource, $version, 'migration');

        // Run artisan command to create migration file
        Artisan::call('make:migration', [
            'name' => $meta['command'],
        ]);

        $latestMigration = $this->getLatestMigrationPath();
        $meta['path'] = $latestMigration;
        $meta['placeholders']['migrations'] = $this->BuildData($data);
        $this->generateContent($meta);
    }

    private function BuildData(array $data): string
    {
        $migrationFields = '';

        foreach ($data as $key => $value) {
            // Start with column type and name
            $migrationFields .= '$table->' . $value['type'] . "('" . $key . "')";

            // Add default value if specified
            if (!empty($value['default'])) {
                $migrationFields .= '->default(' . var_export($value['default'], true) . ')';
            }

            // Add nullable if specified
            if (!empty($value['nullable']) && $value['nullable'] === true) {
                $migrationFields .= '->nullable()';
            }

            // Add unique constraint if specified
            if (!empty($value['unique']) && $value['unique'] === true) {
                $migrationFields .= '->unique()';
            }

            // End each field line
            $migrationFields .= ";\n            ";
        }

        return $migrationFields;
    }

    private function getLatestMigrationPath()
    {
        // Find the latest migration file
        $files = File::files(database_path('migrations'));
        return collect($files)->sortByDesc(fn($file) => $file->getFilename())->first()->getPathname();
    }
}
